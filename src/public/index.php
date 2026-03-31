<?php
// Start Session Global
session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// Import All Controllers
use App\Core\Router;
use App\Controllers\{
    AuthController, DashboardController, MenuController, UserController, SettingsController,
    ScopeController, WhatsappController, LetterController, SupportController,
    PpdbPublicController, PpdbAdminController, PpdbSettingsController,
    StudentController, StudentAffairsController, ParentsController, AlumniController,
    AcademicController, CurriculumController, SyllabusController, KbmPermitController,
    TeachingAssignmentController, AcademicSupportController, KitabController, ClassroomManageController,
    HomeroomController, HomeroomReportController,
    DisciplineController, StudentTrackingController, ExtracurricularController,
    BoardingController, BoardingActivityController, BoardingSupervisorController,
    BoardingMutationController, BoardingReportController,
    StaffController, StaffPositionController, StaffAttendanceController, SchoolStructureController,
    FinanceController, InventoryController, ReportController, TeacherController
};

$router = new Router();

// ============================================================================
// 1. AUTHENTICATION & PUBLIC ROUTES
// ============================================================================

// Public Landing & PPDB
$router->get('/',                   [PpdbPublicController::class, 'index']);
$router->get('/register',           [PpdbPublicController::class, 'register']);
$router->post('/register/process',  [PpdbPublicController::class, 'processRegister']);
$router->post('/register/store',    [PpdbPublicController::class, 'store']);
$router->get('/register/success',   [PpdbPublicController::class, 'success']);
$router->get('/prosedur',           function() { echo "Halaman Prosedur (Coming Soon)"; });
$router->get('/cek-status',         [PpdbPublicController::class, 'checkStatus']);
$router->post('/cek-status',        [PpdbPublicController::class, 'checkStatus']);

// Auth
$router->get('/login',              [AuthController::class, 'login']);
$router->post('/login',             [AuthController::class, 'authenticate']);
$router->get('/logout',             [AuthController::class, 'logout']);

// Dashboard
$router->get('/dashboard',          [DashboardController::class, 'index']);
$router->post('/change-scope',      [ScopeController::class, 'change']);


// ============================================================================
// 2. SYSTEM SETTINGS (ADMINISTRATOR)
// ============================================================================

// School Identity
$router->get('/settings/school',            [SettingsController::class, 'school']);
$router->post('/settings/school/update',    [SettingsController::class, 'updateSchool']);

// User Management
$router->get('/settings/users',             [UserController::class, 'index']);
$router->get('/settings/users/create',      [UserController::class, 'create']);
$router->post('/settings/users/store',      [UserController::class, 'store']);
$router->get('/settings/users/edit',        [UserController::class, 'edit']);
$router->post('/settings/users/update',     [UserController::class, 'update']);
$router->get('/settings/users/delete',      [UserController::class, 'delete']);
$router->post('/settings/users/reset',      [SettingsController::class, 'resetPassword']);

// Menus
$router->get('/settings/menus',             [MenuController::class, 'index']);
$router->post('/settings/menus/store',      [MenuController::class, 'store']);

// Notifications & Letters
$router->get('/settings/whatsapp',          [WhatsappController::class, 'index']);
$router->post('/settings/whatsapp/update',  [WhatsappController::class, 'update']);
$router->post('/settings/whatsapp/test',    [WhatsappController::class, 'test']);

$router->get('/settings/letters',           [LetterController::class, 'index']);
$router->get('/settings/letters/edit',      [LetterController::class, 'edit']);
$router->post('/settings/letters/update',   [LetterController::class, 'update']);
$router->get('/settings/letters/print',     [LetterController::class, 'print']);


// ============================================================================
// 3. PPDB MANAGEMENT (ADMIN)
// ============================================================================

// PPDB Configuration
$router->get('/ppdb/settings',                  [PpdbSettingsController::class, 'index']);
$router->post('/ppdb/settings/period/store',    [PpdbSettingsController::class, 'storePeriod']);
$router->post('/ppdb/settings/period/update',   [PpdbSettingsController::class, 'updatePeriod']);
$router->get('/ppdb/settings/period/activate',  [PpdbSettingsController::class, 'activatePeriod']);
$router->post('/ppdb/settings/track/store',     [PpdbSettingsController::class, 'storeTrack']);
$router->post('/ppdb/settings/track/update',    [PpdbSettingsController::class, 'updateTrack']);

// PPDB Master Data (Admin)
$router->get('/ppdb/periods',           [PpdbAdminController::class, 'periods']);
$router->post('/ppdb/periods/store',    [PpdbAdminController::class, 'storePeriod']);
$router->post('/ppdb/periods/update',   [PpdbAdminController::class, 'updatePeriod']);
$router->post('/ppdb/periods/delete',   [PpdbAdminController::class, 'deletePeriod']);
$router->get('/ppdb/periods/activate',  [PpdbAdminController::class, 'activatePeriod']);

$router->get('/ppdb/tracks',            [PpdbAdminController::class, 'tracks']);
$router->post('/ppdb/tracks/store',     [PpdbAdminController::class, 'storeTrack']);
$router->post('/ppdb/tracks/update',    [PpdbAdminController::class, 'updateTrack']);
$router->post('/ppdb/tracks/delete',    [PpdbAdminController::class, 'deleteTrack']);

// Registration & Processing
$router->get('/ppdb/registrations',         [PpdbAdminController::class, 'index']);
$router->get('/ppdb/registrations/detail',  [PpdbAdminController::class, 'detail']);
$router->post('/ppdb/registrations/store',  [PpdbAdminController::class, 'storeCandidate']);
$router->post('/ppdb/verify/payment',       [PpdbAdminController::class, 'verifyPayment']);
$router->post('/ppdb/verify/document',      [PpdbAdminController::class, 'verifyDocument']);
$router->post('/ppdb/verify/graduation',    [PpdbAdminController::class, 'setGraduation']);
$router->post('/ppdb/promote',              [PpdbAdminController::class, 'promoteToStudent']);


// ============================================================================
// 4. KESISWAAN (STUDENT AFFAIRS)
// ============================================================================

// Data Siswa & Wali
$router->get('/student-affairs/students',       [StudentAffairsController::class, 'index']);
$router->post('/student-affairs/assign-class',  [StudentAffairsController::class, 'assignClass']);
$router->get('/student-affairs/parents',        [ParentsController::class, 'index']);
$router->get('/student-affairs/parents/edit',   [ParentsController::class, 'edit']);
$router->post('/student-affairs/parents/update',[ParentsController::class, 'update']);

// Data Alumni
$router->get('/student-affairs/alumni',         [AlumniController::class, 'index']);
$router->get('/student-affairs/alumni/create',  [AlumniController::class, 'create']);
$router->post('/student-affairs/alumni/store',  [AlumniController::class, 'store']);
$router->get('/student-affairs/alumni/edit',    [AlumniController::class, 'edit']);
$router->post('/student-affairs/alumni/update', [AlumniController::class, 'update']);
$router->get('/student-affairs/alumni/delete',  [AlumniController::class, 'delete']);

// Absensi Siswa
$router->get('/student-affairs/attendance',         [StudentAffairsController::class, 'attendance']);
$router->get('/student-affairs/attendance/create',  [StudentAffairsController::class, 'createAttendance']);
$router->post('/student-affairs/attendance/store',  [StudentAffairsController::class, 'storeAttendance']);
$router->get('/student-affairs/attendance/delete',  [StudentAffairsController::class, 'deleteAttendance']);

// Kedisiplinan: Pelanggaran
$router->get('/student-affairs/discipline',         [DisciplineController::class, 'index']);
$router->post('/student-affairs/discipline/store',  [DisciplineController::class, 'storeViolation']);
$router->get('/student-affairs/discipline/delete',  [DisciplineController::class, 'deleteViolation']);

// Kedisiplinan: Prestasi
$router->get('/student-affairs/achievements',           [DisciplineController::class, 'achievements']);
$router->post('/student-affairs/achievements/store',    [DisciplineController::class, 'storeAchievement']);
$router->post('/student-affairs/achievements/update',   [DisciplineController::class, 'updateAchievement']);
$router->get('/student-affairs/achievements/delete',    [DisciplineController::class, 'deleteAchievement']);

// Kedisiplinan: Bimbingan Konseling (BK)
$router->get('/student-affairs/counseling',         [DisciplineController::class, 'counseling']);
$router->post('/student-affairs/counseling/store',  [DisciplineController::class, 'storeCounseling']);
$router->post('/student-affairs/counseling/update', [DisciplineController::class, 'updateCounseling']);
$router->get('/student-affairs/counseling/delete',  [DisciplineController::class, 'deleteCounseling']);

// Kedisiplinan: Pelacakan Santri
$router->get('/discipline/tracking',        [StudentTrackingController::class, 'index']);
$router->post('/discipline/tracking/store', [StudentTrackingController::class, 'store']);
$router->post('/discipline/tracking/update',[StudentTrackingController::class, 'update']);
$router->get('/discipline/tracking/delete', [StudentTrackingController::class, 'delete']);


// ============================================================================
// 5. AKADEMIK (ACADEMIC)
// ============================================================================

// Master Data Akademik (Tahun & Kelas)
$router->get('/academic/years',             [AcademicController::class, 'years']);
$router->post('/academic/years/store',      [AcademicController::class, 'storeYear']);
$router->get('/academic/years/activate',    [AcademicController::class, 'activateYear']);

$router->get('/master/classrooms',          [ClassroomManageController::class, 'index']);
$router->post('/master/classrooms/store',   [ClassroomManageController::class, 'store']);
$router->post('/master/classrooms/update',  [ClassroomManageController::class, 'update']);
$router->get('/master/classrooms/delete',   [ClassroomManageController::class, 'delete']);

// Mata Pelajaran (Subjects)
$router->get('/academic/subjects',          [AcademicController::class, 'subjects']);
$router->post('/academic/subjects/store',   [AcademicController::class, 'storeSubject']);
$router->post('/academic/subjects/update',  [AcademicController::class, 'updateSubject']);
$router->get('/academic/subjects/delete',   [AcademicController::class, 'deleteSubject']);

// Jadwal Pelajaran
$router->get('/academic/schedules',         [AcademicController::class, 'schedules']);
$router->post('/academic/schedules/store',  [AcademicController::class, 'storeSchedule']);
$router->post('/academic/schedules/update', [AcademicController::class, 'updateSchedule']);
$router->get('/academic/schedules/delete',  [AcademicController::class, 'deleteSchedule']);

// Jurnal Mengajar Guru
$router->get('/academic/journals',          [AcademicController::class, 'journals']);
$router->get('/academic/journals/history',  [AcademicController::class, 'journalHistory']);
$router->get('/academic/journals/create',   [AcademicController::class, 'journalCreate']);
$router->post('/academic/journals/store',   [AcademicController::class, 'journalStore']);
$router->post('/academic/journals/update',  [AcademicController::class, 'journalUpdate']);
$router->get('/academic/journals/delete',   [AcademicController::class, 'journalDelete']);

// Penilaian (Grades & Weights)
$router->get('/academic/grades',            [AcademicController::class, 'grades']);
$router->get('/academic/grades/manage',     [AcademicController::class, 'manageGrades']);
$router->post('/academic/grades/store',     [AcademicController::class, 'storeGrades']);
$router->get('/academic/weights',           [AcademicController::class, 'weights']);
$router->post('/academic/weights/store',    [AcademicController::class, 'storeWeights']);

// Kurikulum & Silabus
$router->get('/academic/curriculum',        [CurriculumController::class, 'index']);
$router->post('/academic/curriculum/store', [CurriculumController::class, 'store']);
$router->post('/academic/curriculum/update',[CurriculumController::class, 'update']);
$router->get('/academic/curriculum/delete', [CurriculumController::class, 'delete']);

$router->get('/academic/syllabus',          [SyllabusController::class, 'index']);
$router->post('/academic/syllabus/store',   [SyllabusController::class, 'store']);
$router->get('/academic/syllabus/delete',   [SyllabusController::class, 'delete']);
$router->get('/academic/syllabus/download', [SyllabusController::class, 'download']);

// Modul Akademik Lainnya
$router->get('/academic/assignments',       [TeachingAssignmentController::class, 'index']);
$router->post('/academic/assignments/store',[TeachingAssignmentController::class, 'store']);
$router->get('/academic/assignments/delete',[TeachingAssignmentController::class, 'delete']);

$router->get('/academic/kbm-permits',       [KbmPermitController::class, 'index']);
$router->post('/academic/kbm-permits/store',[KbmPermitController::class, 'store']);
$router->get('/academic/kbm-permits/delete',[KbmPermitController::class, 'delete']);

$router->get('/academic/calendar',          [AcademicSupportController::class, 'calendar']);
$router->post('/academic/calendar/store',   [AcademicSupportController::class, 'storeEvent']);
$router->get('/academic/exams',             [AcademicSupportController::class, 'examBank']);
$router->post('/academic/exams/store',      [AcademicSupportController::class, 'storeExam']);
$router->get('/academic/kitab',             [KitabController::class, 'index']);
$router->post('/academic/kitab/store',      [KitabController::class, 'store']);

$router->get('/academic/promotion',                 [ClassroomManageController::class, 'promotion']);
$router->post('/academic/promotion/process',        [ClassroomManageController::class, 'processPromotion']);
$router->get('/academic/homeroom-assign',           [ClassroomManageController::class, 'assignHomeroomView']);
$router->post('/academic/homeroom-assign/update',   [ClassroomManageController::class, 'setHomeroom']);


// ============================================================================
// 6. WALI KELAS (HOMEROOM)
// ============================================================================
$router->get('/homeroom',               [HomeroomController::class, 'index']);
$router->get('/homeroom/report-all',    [HomeroomReportController::class, 'index']);
$router->get('/homeroom/print-recap',   [HomeroomReportController::class, 'printRecap']);


// ============================================================================
// 7. KEPEGAWAIAN (STAFF & TEACHERS)
// ============================================================================

// Data Guru
$router->get('/student-affairs/teachers',       [TeacherController::class, 'index']);
$router->get('/student-affairs/teachers/create',[TeacherController::class, 'create']);
$router->post('/student-affairs/teachers/store',[TeacherController::class, 'store']);
$router->get('/student-affairs/teachers/edit',  [TeacherController::class, 'edit']);
$router->post('/student-affairs/teachers/update',[TeacherController::class, 'update']);
$router->get('/student-affairs/teachers/toggle',[TeacherController::class, 'toggleStatus']);
$router->get('/student-affairs/teachers/detail',[TeacherController::class, 'detail']);

// Data Staff
$router->get('/staff/members',          [StaffController::class, 'index']);
$router->post('/staff/members/store',   [StaffController::class, 'store']);
$router->post('/staff/members/update',  [StaffController::class, 'update']);
$router->get('/staff/members/delete',   [StaffController::class, 'delete']);

$router->get('/staff/positions',        [StaffPositionController::class, 'index']);
$router->post('/staff/positions/store', [StaffPositionController::class, 'store']);
$router->post('/staff/positions/update',[StaffPositionController::class, 'update']);
$router->get('/staff/positions/delete', [StaffPositionController::class, 'delete']);

$router->get('/staff/structure',        [SchoolStructureController::class, 'index']);
$router->post('/staff/structure/store', [SchoolStructureController::class, 'store']);
$router->get('/staff/structure/delete', [SchoolStructureController::class, 'delete']);

// Absensi Pegawai
$router->get('/staff/attendance',       [StaffAttendanceController::class, 'index']);
$router->post('/staff/attendance/store',[StaffAttendanceController::class, 'store']);
$router->get('/staff/attendance/delete',[StaffAttendanceController::class, 'delete']);


// ============================================================================
// 8. KEUANGAN & INVENTARIS (FINANCE)
// ============================================================================

// Keuangan
$router->get('/finance',                    [FinanceController::class, 'index']);
$router->get('/finance/fee-types',          [FinanceController::class, 'feeTypes']);
$router->post('/finance/fee-types/store',   [FinanceController::class, 'storeFeeType']);
$router->get('/finance/fee-types/delete',   [FinanceController::class, 'deleteFeeType']);
$router->get('/finance/billing',            [FinanceController::class, 'billing']); 
$router->post('/finance/billing/create',    [FinanceController::class, 'createBill']);
$router->post('/finance/generate-bill',     [FinanceController::class, 'generateBill']); 
$router->get('/finance/reports',            [FinanceController::class, 'reports']);
$router->get('/finance/receipt',            [FinanceController::class, 'printReceipt']);
$router->post('/finance/pay',               [FinanceController::class, 'pay']);

// Inventaris
$router->get('/finance/inventory',          [InventoryController::class, 'index']);
$router->post('/finance/inventory/store',   [InventoryController::class, 'store']);
$router->post('/finance/inventory/update',  [InventoryController::class, 'update']);
$router->get('/finance/inventory/delete',   [InventoryController::class, 'delete']);


// ============================================================================
// 9. KEPESANTRENAN (BOARDING SCHOOL)
// ============================================================================

// Asrama
$router->get('/boarding/dorms',         [BoardingController::class, 'dorms']);
$router->post('/boarding/dorms/store',  [BoardingController::class, 'storeDorm']);
$router->post('/boarding/dorms/delete', [BoardingController::class, 'deleteDorm']);
$router->post('/boarding/assign',       [BoardingController::class, 'assignDorm']);
$router->get('/boarding/mutations',     [BoardingMutationController::class, 'index']);
$router->post('/boarding/mutations/store',[BoardingMutationController::class, 'store']);

// Program & Kesehatan
$router->get('/boarding/activities',        [BoardingActivityController::class, 'index']);
$router->post('/boarding/activities/store', [BoardingActivityController::class, 'store']);
$router->get('/boarding/activities/delete', [BoardingActivityController::class, 'delete']);
$router->get('/boarding/supervisors',       [BoardingSupervisorController::class, 'index']);
$router->post('/boarding/supervisors/store',[BoardingSupervisorController::class, 'store']);
$router->get('/boarding/supervisors/delete',[BoardingSupervisorController::class, 'delete']);

$router->get('/boarding/health',        [BoardingController::class, 'health']);
$router->post('/boarding/health/store', [BoardingController::class, 'storeHealth']);
$router->get('/boarding/tahfidz',       [BoardingController::class, 'tahfidz']);
$router->post('/boarding/tahfidz/store',[BoardingController::class, 'storeTahfidz']);

// Perizinan & Rapor
$router->get('/boarding/permits',           [BoardingController::class, 'permits']);
$router->post('/boarding/permits/store',    [BoardingController::class, 'storePermit']);
$router->get('/boarding/permits/approve',   [BoardingController::class, 'approvePermit']);
$router->get('/report/boarding',            [BoardingReportController::class, 'index']);
$router->post('/report/boarding/store',     [BoardingReportController::class, 'store']);
$router->get('/report/boarding/print',      [BoardingReportController::class, 'print']);


// ============================================================================
// 10. EKSTRAKURIKULER
// ============================================================================
$router->get('/extracurricular',                [ExtracurricularController::class, 'index']);
$router->get('/extracurricular/master',         [ExtracurricularController::class, 'index']);
$router->post('/extracurricular/store',         [ExtracurricularController::class, 'store']);
$router->post('/extracurricular/schedule/store',[ExtracurricularController::class, 'storeSchedule']);
$router->post('/extracurricular/coach/store',   [ExtracurricularController::class, 'storeCoach']);
$router->get('/extracurricular/delete',         [ExtracurricularController::class, 'delete']);

$router->get('/extracurricular/members',        [ExtracurricularController::class, 'members']);
$router->post('/extracurricular/members/add',   [ExtracurricularController::class, 'addMember']);
$router->post('/extracurricular/members/delete',[ExtracurricularController::class, 'deleteMember']);

$router->get('/extracurricular/attendance',     [ExtracurricularController::class, 'attendance']);
$router->post('/extracurricular/attendance/save',[ExtracurricularController::class, 'saveAttendance']);
$router->post('/extracurricular/grades/store',  [ExtracurricularController::class, 'storeGrade']);


// ============================================================================
// 11. LAIN-LAIN (SUPPORT & REPORT)
// ============================================================================
$router->get('/support',        [SupportController::class, 'index']);
$router->post('/support/create',[SupportController::class, 'create']);
$router->get('/support/detail', [SupportController::class, 'detail']);
$router->post('/support/reply', [SupportController::class, 'reply']);

$router->get('/report/print',   [ReportController::class, 'print']);


// ============================================================================
// 12. STUDENT PORTAL
// ============================================================================
$router->get('/student/dashboard',      [StudentController::class, 'index']);
$router->get('/student/profile',        [StudentController::class, 'profile']);
$router->get('/student/biodata',        function() { echo "Halaman Biodata (Coming Soon)"; });

$router->get('/student/payment',        [StudentController::class, 'payment']);
$router->post('/student/payment/store', [StudentController::class, 'storePayment']);
$router->get('/student/billing',        [StudentController::class, 'billing']); 

$router->get('/student/documents',      [StudentController::class, 'documents']);
$router->post('/student/documents/store',[StudentController::class, 'storeDocument']);
$router->get('/student/exam-card',      [StudentController::class, 'printExamCard']);


// ============================================================================
// 13. API ENDPOINTS
// ============================================================================
$router->get('/api/wilayah/provinces', [\App\Controllers\Api\WilayahController::class, 'getProvinces']);
$router->get('/api/wilayah/regencies', [\App\Controllers\Api\WilayahController::class, 'getRegencies']);
$router->get('/api/wilayah/districts', [\App\Controllers\Api\WilayahController::class, 'getDistricts']);
$router->get('/api/wilayah/villages',  [\App\Controllers\Api\WilayahController::class, 'getVillages']);


// Execute Router
$router->resolve();
