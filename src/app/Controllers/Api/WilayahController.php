<?php

namespace App\Controllers\Api;

use App\Core\Database;

class WilayahController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getProvinces() {
        try {
            $stmt = $this->db->query("SELECT id as code, name FROM provinces ORDER BY name ASC");
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_stringify(['data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_stringify(['error' => 'Database error']);
        }
    }

    public function getRegencies() {
        try {
            $provinceId = $_GET['province_id'] ?? '';
            $stmt = $this->db->query("SELECT id as code, name FROM cities WHERE province_id = ? ORDER BY name ASC", [$provinceId]);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_stringify(['data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_stringify(['error' => 'Database error']);
        }
    }

    public function getDistricts() {
        try {
            $cityId = $_GET['regency_id'] ?? '';
            $stmt = $this->db->query("SELECT id as code, name FROM districts WHERE city_id = ? ORDER BY name ASC", [$cityId]);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_stringify(['data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_stringify(['error' => 'Database error']);
        }
    }

    public function getVillages() {
        try {
            $districtId = $_GET['district_id'] ?? '';
            $stmt = $this->db->query("SELECT id as code, name FROM villages WHERE district_id = ? ORDER BY name ASC", [$districtId]);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_stringify(['data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_stringify(['error' => 'Database error']);
        }
    }
}
