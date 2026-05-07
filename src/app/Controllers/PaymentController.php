<?php
namespace App\Controllers;

use App\Services\BniVaService;
use App\Core\Database;

class PaymentController {

    public function createVa() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $studentId = $input['student_id'] ?? null;
            $amount = $input['amount'] ?? null;
            $description = $input['description'] ?? 'Biaya Pendidikan';

            if (!$studentId || !$amount || !is_numeric($amount)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid student_id or amount']);
                return;
            }

            $db = Database::getInstance();
            $student = $db->query("SELECT * FROM student_students WHERE id = ?", [$studentId])->fetch();
            $reg = $db->query("SELECT * FROM student_student_registrations WHERE student_id = ? ORDER BY id DESC LIMIT 1", [$studentId])->fetch();

            if (!$student) {
                echo json_encode(['status' => 'error', 'message' => 'Student not found']);
                return;
            }

            // Extract unit info for correct BNI Credential (e.g., from ppdb_info JSON if it's a registration)
            $unit = 'ALIYAH';
            if ($reg && isset($reg['ppdb_info'])) {
                $ppdb = json_decode($reg['ppdb_info'], true);
                if (isset($ppdb['education_unit'])) $unit = $ppdb['education_unit'];
            }

            $bniService = new BniVaService($unit);

            $trxId = 'TRX-' . time() . '-' . rand(1000, 9999);
            $invoiceNumber = 'INV-' . $trxId;
            $vaNumber = $bniService->getPrefix() . $bniService->getClientId() . substr(preg_replace('/[^0-9]/', '', $student['nisn'] ?: rand(10000, 99999)), 0, 10);
            $dueDate = date('Y-m-d H:i:s', strtotime('+3 days'));

            $schoolId = $reg ? $reg['school_id'] : 'DEFAULT_SCHOOL_ID';

            // 1. Create Invoice in DB
            $invoiceId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
            $db->query("INSERT INTO payment_invoices (id, invoice_number, student_id, issue_date, due_date, status, total_amount, description, school_id) VALUES (?, ?, ?, NOW(), ?, 'WAITING_PAYMENT', ?, ?, ?)", [
                $invoiceId, $invoiceNumber, $studentId, $dueDate, $amount, $description, $schoolId
            ]);

            // 2. Hit BNI API
            $response = $bniService->createVaBilling([
                'type' => 'createbilling',
                'client_id' => $bniService->getClientId(),
                'trx_id' => $trxId,
                'trx_amount' => $amount,
                'billing_type' => 'o',
                'customer_name' => $student['name'],
                'customer_email' => $student['email'] ?: 'santri@thawalib-parabek.sch.id',
                'customer_phone' => $student['phone_number'] ?: '08111111111',
                'virtual_account' => $vaNumber,
                'datetime_expired' => date('Y-m-d\TH:i:sP', strtotime('+3 days')),
                'description' => $description
            ]);

            if (isset($response['virtual_account'])) {
                // If response provides actual VA number, override it (in case BNI auto-generated it)
                $vaNumber = $response['virtual_account'];
            }

            // 3. Save VA to Database
            $db->query("INSERT INTO payment_virtual_account (order_id, billing_type, client_id, customer_name, customer_phone, customer_email, description, trx_amount, trx_id, type, datetime_expired, student_id, va_number) VALUES (?, 'o', ?, ?, ?, ?, ?, ?, ?, 'c', ?, ?, ?)", [
                $invoiceNumber, $bniService->getClientId(), $student['name'], $student['phone_number'] ?? '-', $student['email'] ?? '-', $description, $amount, $trxId, $dueDate, $studentId, $vaNumber
            ]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Virtual Account created',
                'data' => [
                    'va_number' => $vaNumber,
                    'amount' => $amount,
                    'expired_at' => $dueDate,
                    'invoice' => $invoiceNumber
                ]
            ]);

        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function inquiry() {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $trxId = $input['trx_id'] ?? null;
            $unit = $input['unit'] ?? 'ALIYAH';

            if (!$trxId) throw new \Exception("Missing trx_id");

            $bniService = new BniVaService($unit);
            $response = $bniService->inquiry([
                'type' => 'inquirybilling',
                'client_id' => $bniService->getClientId(),
                'trx_id' => $trxId
            ]);

            echo json_encode(['status' => 'success', 'data' => $response]);

        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function callback() {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['data']) || !isset($input['client_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid callback payload']);
            return;
        }

        // We don't necessarily know the Unit beforehand, so we can try to find the secret by matching client_id
        $clientId = $input['client_id'];
        $secretKey = null;
        if ($clientId === getenv('VA_CLIENT_ID_ALIYAH')) $secretKey = getenv('VA_SECRET_KEY_ALIYAH');
        elseif ($clientId === getenv('VA_CLIENT_ID_TSANAWIYAH')) $secretKey = getenv('VA_SECRET_KEY_TSANAWIYAH');
        elseif ($clientId === getenv('VA_CLIENT_ID_MAHAD_ALY')) $secretKey = getenv('VA_SECRET_KEY_MAHAD_ALY');

        if (!$secretKey) {
            echo json_encode(['status' => 'error', 'message' => 'Unknown client_id']);
            return;
        }

        try {
            // Decrypt Data
            $decrypted = \App\Libraries\BniEncryption::decrypt($input['data'], $clientId, $secretKey);

            if (!$decrypted || empty($decrypted['trx_id'])) {
                throw new \Exception("Failed to decrypt or missing trx_id");
            }

            $trxId = $decrypted['trx_id'];
            $vaNumber = $decrypted['virtual_account'];
            $paymentAmount = $decrypted['payment_amount'];
            $datetimePayment = $decrypted['datetime_payment']; // ISO8601 usually

            $db = Database::getInstance();
            $va = $db->query("SELECT * FROM payment_virtual_account WHERE trx_id = ? AND va_number = ?", [$trxId, $vaNumber])->fetch();

            if (!$va) {
                throw new \Exception("Virtual Account not found in database");
            }

            $db->getConnection()->beginTransaction();

            // Determine status
            $paymentStatus = ($paymentAmount == $va['trx_amount']) ? 'PAID' : 'PAIR_PAYMENT';
            $dbDate = date('Y-m-d H:i:s', strtotime($datetimePayment));

            // Update VA Table
            $db->query("UPDATE payment_virtual_account SET status = ?, payment_amount = ?, datetime_payment = ? WHERE id = ?", [
                $paymentStatus, $paymentAmount, $dbDate, $va['id']
            ]);

            // Update Invoice
            $db->query("UPDATE payment_invoices SET status = ?, paid_at = ?, updated_at = NOW() WHERE invoice_number = ?", [
                $paymentStatus, $dbDate, $va['order_id']
            ]);

            // Get Invoice ID to link
            $invoice = $db->query("SELECT id, student_id FROM payment_invoices WHERE invoice_number = ?", [$va['order_id']])->fetch();
            if ($invoice) {
                $db->query("INSERT INTO payment_payment_histories (invoice_id, created_at, title, description) VALUES (?, ?, 'Pembayaran BNI VA', 'Pembayaran via BNI VA sejumlah {$paymentAmount}')", [
                    $invoice['id'], $dbDate
                ]);
            }

            $db->getConnection()->commit();

            // Must reply with standard JSON for BNI callback success
            echo json_encode(['status' => '000']);

        } catch (\Exception $e) {
            if ($db->getConnection()->inTransaction()) {
                $db->getConnection()->rollBack();
            }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
