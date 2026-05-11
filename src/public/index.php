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
    AuthController, DashboardController, MenuController, UserController, RoleController, SettingsController,
    ScopeController, WhatsappController, LetterController, SupportController,
    PpdbPublicController, PpdbAdminController, PpdbSettingsController,
    StudentController, StudentAffairsController, ParentsController, GuardianController, AlumniController,
    LibraryController,
    AcademicController, CurriculumController, SyllabusController, KbmPermitController,
    TeachingAssignmentController, AcademicSupportController, KitabController, ClassroomManageController,
    HomeroomController, HomeroomReportController,
    DisciplineController, StudentTrackingController, ExtracurricularController,
    BoardingController, BoardingActivityController, BoardingSupervisorController,
    BoardingMutationController, BoardingReportController, BoardingMapController,
    StaffController, StaffPositionController, StaffAttendanceController, SchoolStructureController,
    FinanceController, InventoryController, ReportController, PoskestrenController, AnnouncementController, TeacherController,
    PasswordResetController, GuidelineController
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
$router->get('/prosedur',           [PpdbPublicController::class, 'prosedur']);

$router->post('/payment/va/create',   [\App\Controllers\PaymentController::class, 'createVa']);
$router->post('/payment/va/inquiry',  [\App\Controllers\PaymentController::class, 'inquiry']);
$router->post('/payment/va/callback', [\App\Controllers\PaymentController::class, 'callback']);
$router->get('/cek-status',         [PpdbPublicController::class, 'checkStatus']);
$router->post('/cek-status',        [PpdbPublicController::class, 'checkStatus']);

// Auth
$router->get('/login',              [AuthController::class, 'login']);
$router->post('/login',             [AuthController::class, 'authenticate']);
$router->get('/logout',             [AuthController::class, 'logout']);

// Password Reset via OTP WhatsApp
$router->get('/forgot-password',          [PasswordResetController::class, 'form']);
$router->post('/forgot-password/send',    [PasswordResetController::class, 'sendOtp']);
$router->get('/forgot-password/verify',   [PasswordResetController::class, 'verifyForm']);
$router->post('/forgot-password/verify',  [PasswordResetController::class, 'verifyOtp']);
$router->get('/forgot-password/reset',    [PasswordResetController::class, 'resetForm']);
$router->post('/forgot-password/reset',   [PasswordResetController::class, 'resetPassword']);

// Dashboard
$router->get('/dashboard',          [DashboardController::class, 'index']);
$router->post('/change-scope',      [ScopeController::class, 'change']);


// ============================================================================
// 2. SYSTEM SETTINGS (ADMINISTRATOR)
// ============================================================================

// School Identity (Dipindahkan ke Menu Sekolah)
$router->get('/school/profile',            [SettingsController::class, 'school']);
$router->post('/school/profile/update',    [SettingsController::class, 'updateSchool']);

// User Management
$router->get('/settings/users',             [UserController::class, 'index']);
$router->get('/settings/users/create',      [UserController::class, 'create']);
$router->post('/settings/users/store',      [UserController::class, 'store']);
$router->get('/settings/users/edit',        [UserController::class, 'edit']);
$router->post('/settings/users/update-role',     [UserController::class, 'updateRole']);
$router->get('/settings/users/delete',      [UserController::class, 'delete']);
$router->post('/settings/users/reset',      [SettingsController::class, 'resetPassword']);

$router->post('/settings/users/toggle',     [UserController::class, 'toggle']);

// Roles & Permissions
$router->get('/settings/roles',                    [RoleController::class, 'index']);
$router->post('/settings/roles/store',             [RoleController::class, 'store']);
$router->post('/settings/roles/update',            [RoleController::class, 'update']);
$router->post('/settings/roles/delete',            [RoleController::class, 'delete']);
$router->post('/settings/roles/toggle',            [RoleController::class, 'toggle']);
$router->get('/settings/roles/permissions',        [RoleController::class, 'permissions']);
$router->post('/settings/roles/permissions/update',[RoleController::class, 'updatePermissions']);

// Menus
$router->get('/settings/menus',             [MenuController::class, 'index']);
$router->post('/settings/menus/store',      [MenuController::class, 'store']);
$router->post('/settings/menus/update',     [MenuController::class, 'update']);
$router->post('/settings/menus/toggle',     [MenuController::class, 'toggle']);
$router->post('/settings/menus/delete',     [MenuController::class, 'delete']);

// Notifications & Letters
$router->get('/settings/whatsapp',           [WhatsappController::class, 'index']);
$router->get('/settings/whatsapp/status',    [WhatsappController::class, 'status']);
$router->post('/settings/whatsapp/send',     [WhatsappController::class, 'sendManual']);
$router->post('/settings/whatsapp/blast',    [WhatsappController::class, 'blast']);
$router->post('/settings/whatsapp/logout',   [WhatsappController::class, 'logout']);

$router->get('/settings/letters',           [LetterController::class, 'index']);
$router->post('/settings/letters/store',    [LetterController::class, 'store']);
$router->get('/settings/letters/edit',      [LetterController::class, 'edit']);
$router->post('/settings/letters/update',   [LetterController::class, 'update']);
$router->get('/settings/letters/delete',    [LetterController::class, 'delete']);
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

// New School PPDB Routes
$router->get('/school/ppdb',                   [PpdbAdminController::class, 'settings']);
$router->post('/school/ppdb/path/store',       [PpdbAdminController::class, 'storePath']);
$router->post('/school/ppdb/path/toggle/(.*)', [PpdbAdminController::class, 'togglePath']);
$router->post('/school/ppdb/batch/store',      [PpdbAdminController::class, 'storeBatch']);
$router->post('/school/ppdb/batch/activate/(.*)', [PpdbAdminController::class, 'activateBatch']);

// PPDB Master Data (Admin)
$router->get('/ppdb/periods',           function(){ header('Location: /ppdb/settings?tab=periode'); exit; });
$router->post('/ppdb/periods/store',    [PpdbAdminController::class, 'storePeriod']);
$router->post('/ppdb/periods/update',   [PpdbAdminController::class, 'updatePeriod']);
$router->post('/ppdb/periods/delete',   [PpdbAdminController::class, 'deletePeriod']);
$router->get('/ppdb/periods/activate',  [PpdbAdminController::class, 'activatePeriod']);

$router->get('/ppdb/tracks',            function(){ header('Location: /ppdb/settings?tab=jalur'); exit; });
$router->post('/ppdb/tracks/store',     [PpdbAdminController::class, 'storeTrack']);
$router->post('/ppdb/tracks/update',    [PpdbAdminController::class, 'updateTrack']);
$router->post('/ppdb/tracks/delete',    [PpdbAdminController::class, 'deleteTrack']);

// Registration & Processing
$router->get('/ppdb/registrations',                [PpdbAdminController::class, 'index']);
$router->get('/ppdb/registrations/detail',         [PpdbAdminController::class, 'detail']);
$router->post('/ppdb/registrations/store',         [PpdbAdminController::class, 'storeCandidate']);
$router->post('/ppdb/registrations/set-status',    [PpdbAdminController::class, 'setStatus']);
$router->post('/ppdb/registrations/delete',        [PpdbAdminController::class, 'deleteCandidate']);
$router->post('/ppdb/registrations/notify',        [PpdbAdminController::class, 'notifyCandidate']);
$router->post('/ppdb/verify/payment',       [PpdbAdminController::class, 'verifyPayment']);
$router->post('/ppdb/verify/document',      [PpdbAdminController::class, 'verifyDocument']);
$router->post('/ppdb/verify/graduation',    [PpdbAdminController::class, 'setGraduation']);
$router->post('/ppdb/promote',              [PpdbAdminController::class, 'promoteToStudent']);


// ============================================================================
// 4. KESISWAAN (STUDENT AFFAIRS)
// ============================================================================

// Data Siswa & Wali
$router->get('/students',                           [StudentAffairsController::class, 'index']);
$router->post('/students/assign-class',             [StudentAffairsController::class, 'assignClass']);
$router->get('/student-affairs/students',              [StudentAffairsController::class, 'index']);
$router->get('/student-affairs/students/print',        [StudentAffairsController::class, 'printBiodata']);
$router->post('/student-affairs/students/store',       [StudentAffairsController::class, 'store']);
$router->post('/student-affairs/students/update',      [StudentAffairsController::class, 'update']);
$router->get('/student-affairs/students/delete',       [StudentAffairsController::class, 'delete']);
$router->get('/student-affairs/students/export',       [StudentAffairsController::class, 'export']);
$router->post('/student-affairs/students/import',      [StudentAffairsController::class, 'import']);
$router->get('/student-affairs/students/detail',       [StudentAffairsController::class, 'detail']);

$router->get('/parents',                        [ParentsController::class, 'index']);
$router->get('/parents/edit',                   [ParentsController::class, 'edit']);
$router->post('/parents/update',                [ParentsController::class, 'update']);
$router->get('/student-affairs/parents',        [ParentsController::class, 'index']);
$router->get('/student-affairs/parents/edit',   [ParentsController::class, 'edit']);
$router->post('/student-affairs/parents/update',[ParentsController::class, 'update']);

$router->get('/guardians',      [GuardianController::class, 'index']);

// Data Alumni (Dipindahkan ke Menu Sekolah)
$router->get('/school/alumni',              [AlumniController::class, 'index']);
$router->get('/library',                    [LibraryController::class, 'index']);
$router->post('/library/store',             [LibraryController::class, 'store']);
$router->post('/library/return',            [LibraryController::class, 'returnBook']);
$router->post('/library/books/store',       [LibraryController::class, 'storeBook']);
$router->get('/library/books/delete',       [LibraryController::class, 'deleteBook']);
$router->get('/school/alumni/create',       [AlumniController::class, 'create']);
$router->post('/school/alumni/store',       [AlumniController::class, 'store']);
$router->get('/school/alumni/edit',         [AlumniController::class, 'edit']);
$router->post('/school/alumni/update',      [AlumniController::class, 'update']);
$router->get('/school/alumni/delete',       [AlumniController::class, 'delete']);
$router->get('/school/alumni/print-letter', [AlumniController::class, 'printLetter']);

// Absensi (Dipindahkan ke Menu Absensi)
$router->get('/attendance/students',         [StudentAffairsController::class, 'attendance']);
$router->get('/attendance/students/create',  [StudentAffairsController::class, 'createAttendance']);
$router->post('/attendance/students/store',  [StudentAffairsController::class, 'storeAttendance']);
$router->get('/attendance/students/delete',  [StudentAffairsController::class, 'deleteAttendance']);

$router->get('/attendance/teachers',         [StaffAttendanceController::class, 'teachers']);
$router->get('/attendance/staff',            [StaffAttendanceController::class, 'staff']);
$router->post('/attendance/staff/store',     [StaffAttendanceController::class, 'store']);
$router->get('/attendance/staff/delete',     [StaffAttendanceController::class, 'delete']);

$router->get('/attendance/kbm-permits',      [KbmPermitController::class, 'index']);
$router->post('/attendance/kbm-permits/store',[KbmPermitController::class, 'store']);
$router->get('/attendance/kbm-permits/delete',[KbmPermitController::class, 'delete']);
$router->get('/academic/kbm-permits',        [KbmPermitController::class, 'index']);
$router->post('/academic/kbm-permits/store', [KbmPermitController::class, 'store']);
$router->get('/academic/kbm-permits/delete', [KbmPermitController::class, 'delete']);

// Kedisiplinan (Dipindahkan ke Menu Kedisiplinan Khusus)
$router->get('/discipline/master-violations',       [DisciplineController::class, 'master']);
$router->post('/discipline/master-violations/store',[DisciplineController::class, 'storeMaster']);
$router->get('/discipline/student-violations',      [DisciplineController::class, 'index']);
$router->post('/discipline/student-violations/store',[DisciplineController::class, 'storeViolation']);
$router->get('/discipline/student-violations/delete',[DisciplineController::class, 'deleteViolation']);

$router->get('/discipline/dorm-mutations',          [BoardingMutationController::class, 'index']);
$router->post('/discipline/dorm-mutations/store',   [BoardingMutationController::class, 'store']);

$router->get('/discipline/tracking',                [StudentTrackingController::class, 'index']);
$router->post('/discipline/tracking/store',         [StudentTrackingController::class, 'store']);
$router->post('/discipline/tracking/update',        [StudentTrackingController::class, 'update']);
$router->get('/discipline/tracking/delete',         [StudentTrackingController::class, 'delete']);

$router->get('/discipline/homeroom-reports',        [HomeroomReportController::class, 'index']);

// Kedisiplinan Lainnya (Prestasi & BK)
$router->get('/achievements',           [DisciplineController::class, 'achievements']);
$router->post('/achievements/store',    [DisciplineController::class, 'storeAchievement']);
$router->post('/achievements/update',   [DisciplineController::class, 'updateAchievement']);
$router->get('/achievements/delete',    [DisciplineController::class, 'deleteAchievement']);

$router->get('/counseling',             [DisciplineController::class, 'counseling']);
$router->post('/counseling/store',      [DisciplineController::class, 'storeCounseling']);
$router->post('/counseling/update',     [DisciplineController::class, 'updateCounseling']);
$router->get('/counseling/delete',      [DisciplineController::class, 'deleteCounseling']);

// ============================================================================
// X. RAPOR (REPORTS)
// ============================================================================
$router->get('/reports/students', [ReportController::class, 'students']);
$router->get('/report/print',    [ReportController::class, 'students']); // alias menu
$router->get('/reports/boarding', [BoardingReportController::class, 'index']);

// ============================================================================
// Y. POSKESTREN (HEALTH)
// ============================================================================
$router->get('/poskestren/patients',        [PoskestrenController::class, 'patients']);
$router->post('/poskestren/patients/store', [PoskestrenController::class, 'storePatient']);
$router->post('/poskestren/patients/delete',[PoskestrenController::class, 'deletePatient']);
$router->get('/poskestren/staff',           [PoskestrenController::class, 'staff']);

// ============================================================================
// Z. LAIN-LAIN (OTHERS)
// ============================================================================
$router->get('/announcements',           [AnnouncementController::class, 'index']);
$router->post('/announcements/store',    [AnnouncementController::class, 'store']);
$router->post('/announcements/update',   [AnnouncementController::class, 'update']);
$router->post('/announcements/delete',   [AnnouncementController::class, 'delete']);


// ============================================================================
// 5. AKADEMIK (ACADEMIC)
// ============================================================================

// Master Data Akademik (Tahun & Kelas)
$router->get('/academic/years',             [AcademicController::class, 'years']);
$router->post('/academic/years/store',      [AcademicController::class, 'storeYear']);
$router->get('/academic/years/activate',    [AcademicController::class, 'activateYear']);

$router->get('/academic/classrooms',          [ClassroomManageController::class, 'index']);
$router->post('/academic/classrooms/store',   [ClassroomManageController::class, 'store']);
$router->post('/academic/classrooms/update',  [ClassroomManageController::class, 'update']);
$router->get('/academic/classrooms/delete',   [ClassroomManageController::class, 'delete']);

// Skeleton Akademik Baru
$router->get('/academic/subject-teachers',  [AcademicController::class, 'subjectTeachers']);
$router->get('/academic/homeroom-teachers', [AcademicController::class, 'homeroomTeachers']);
$router->get('/academic/calendar-view',     [AcademicController::class, 'calendarView']);
$router->get('/academic/syllabus-view',     [AcademicController::class, 'syllabusView']);

// Mata Pelajaran (Subjects)
$router->get('/academic/subjects',          [AcademicController::class, 'subjects']);
$router->post('/academic/subjects/store',   [AcademicController::class, 'storeSubject']);
$router->post('/academic/subjects/update',  [AcademicController::class, 'updateSubject']);
$router->get('/academic/subjects/delete',   [AcademicController::class, 'deleteSubject']);

// Jadwal Pelajaran
$router->get('/academic/schedules',         [AcademicController::class, 'schedules']);
$router->get('/academic/schedules/print',   [AcademicController::class, 'printSchedule']);
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

$router->get('/academic/calendar',          [AcademicSupportController::class, 'calendar']);
$router->post('/academic/calendar/store',   [AcademicSupportController::class, 'storeEvent']);
$router->post('/academic/calendar/update',  [AcademicSupportController::class, 'updateEvent']);
$router->post('/academic/calendar/delete',   [AcademicSupportController::class, 'deleteEvent']);
$router->get('/academic/calendar/print',    [AcademicSupportController::class, 'printCalendar']);
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
$router->get('/homeroom/print-recap',   [HomeroomReportController::class, 'printRecap']);


// ============================================================================
// 7. KEPEGAWAIAN (STAFF & TEACHERS)
// ============================================================================

// Data Guru (Dipindahkan ke Menu Sekolah)
$router->get('/school/teachers',       [TeacherController::class, 'index']);
$router->get('/school/teachers/create',[TeacherController::class, 'create']);
$router->post('/school/teachers/store',[TeacherController::class, 'store']);
$router->get('/school/teachers/edit',  [TeacherController::class, 'edit']);
$router->post('/school/teachers/update',[TeacherController::class, 'update']);
$router->get('/school/teachers/toggle',[TeacherController::class, 'toggleStatus']);
$router->get('/school/teachers/detail',[TeacherController::class, 'detail']);

// Data Staff (Dipindahkan ke Menu Sekolah)
$router->get('/school/staff',          [StaffController::class, 'index']);
$router->post('/school/staff/store',   [StaffController::class, 'store']);
$router->post('/school/staff/update',  [StaffController::class, 'update']);
$router->get('/school/staff/delete',   [StaffController::class, 'delete']);

$router->get('/school/staff-positions',        [StaffPositionController::class, 'index']);
$router->post('/school/staff-positions/store', [StaffPositionController::class, 'store']);
$router->post('/school/staff-positions/update',[StaffPositionController::class, 'update']);
$router->get('/school/staff-positions/delete', [StaffPositionController::class, 'delete']);

$router->get('/school/structure',        [SchoolStructureController::class, 'index']);
$router->get('/staff/structure',         [SchoolStructureController::class, 'index']);
$router->post('/school/structure/store', [SchoolStructureController::class, 'store']);
$router->post('/staff/structure/store',  [SchoolStructureController::class, 'store']);
$router->get('/school/structure/delete', [SchoolStructureController::class, 'delete']);
$router->get('/staff/structure/delete',  [SchoolStructureController::class, 'delete']);


// ============================================================================
// 8. KEUANGAN & INVENTARIS (FINANCE)
// ============================================================================

// Keuangan (Updated)


$router->get('/finance/other-fees',         [FinanceController::class, 'otherFees']);
$router->get('/finance/treasurer-reports',  [FinanceController::class, 'treasurerReports']);
$router->post('/finance/notify-bills',      [FinanceController::class, 'notifyBills']);
$router->get('/finance/facilities',         [FinanceController::class, 'facilities']);

// Keuangan Legacy (Still needed for system logic/redirects but removed from sidebar)
$router->get('/finance',                    [FinanceController::class, 'index']);
$router->get('/finance/fee-types',          [FinanceController::class, 'feeTypes']);
$router->post('/finance/fee-types/store',   [FinanceController::class, 'storeFeeType']);
$router->get('/finance/fee-types/delete',   [FinanceController::class, 'deleteFeeType']);
$router->get('/finance/billing',            [FinanceController::class, 'billing']); 
$router->post('/finance/billing/create',    [FinanceController::class, 'createBill']);
$router->post('/finance/billing/mark-paid', [FinanceController::class, 'markAsPaid']);
$router->post('/finance/billing/verify',    [FinanceController::class, 'verifyPayment']);
$router->get('/finance/billing/delete',     [FinanceController::class, 'deleteBill']);
$router->post('/finance/generate-bill',     [FinanceController::class, 'generateBill']); 
$router->get('/finance/reports',            [FinanceController::class, 'reports']);
$router->get('/finance/reports/export',     [FinanceController::class, 'exportReports']);
$router->get('/finance/receipt',            [FinanceController::class, 'printReceipt']);
$router->post('/finance/pay',               [FinanceController::class, 'pay']);

// Inventaris Aset
$router->get('/finance/inventory',                  [InventoryController::class, 'index']);
$router->post('/finance/inventory/store',           [InventoryController::class, 'store']);
$router->post('/finance/inventory/update',          [InventoryController::class, 'update']);
$router->get('/finance/inventory/delete',           [InventoryController::class, 'delete']);
$router->get('/finance/inventory/mutations',        [InventoryController::class, 'mutations']);
$router->get('/finance/inventory/loans',            [InventoryController::class, 'loans']);
$router->post('/finance/inventory/loans/store',     [InventoryController::class, 'storeLoan']);
$router->post('/finance/inventory/loans/return',    [InventoryController::class, 'returnLoan']);
$router->get('/finance/inventory/export',           [InventoryController::class, 'export']);
$router->post('/finance/inventory/notify-damaged',  [InventoryController::class, 'notifyDamaged']);


// ============================================================================
// 9. KEPESANTRENAN (BOARDING SCHOOL)
// ============================================================================

// Asrama (Dipindahkan ke Menu Asrama)
$router->get('/asrama/dorms',               [BoardingController::class, 'dorms']);
$router->post('/asrama/dorms/store',        [BoardingController::class, 'storeDorm']);
$router->post('/asrama/dorms/delete',       [BoardingController::class, 'deleteDorm']);
$router->get('/asrama/dorms/students',      [BoardingController::class, 'dormStudents']);
$router->post('/asrama/assign',             [BoardingController::class, 'assignDorm']);
$router->post('/asrama/move',               [BoardingController::class, 'moveDorm']);

$router->get('/asrama/activities',          [BoardingActivityController::class, 'index']);
$router->post('/asrama/activities/store',   [BoardingActivityController::class, 'store']);
$router->get('/asrama/activities/delete',   [BoardingActivityController::class, 'delete']);

$router->get('/asrama/supervisors',         [BoardingSupervisorController::class, 'index']);
$router->post('/asrama/supervisors/store',  [BoardingSupervisorController::class, 'store']);
$router->get('/asrama/supervisors/delete',  [BoardingSupervisorController::class, 'delete']);

// Routing Baru untuk Menu Asrama
$router->get('/asrama/units',               [BoardingController::class, 'units']);
$router->get('/asrama/tilawah-attendance',  [BoardingController::class, 'tilawah']);
$router->get('/asrama/map',                 [BoardingMapController::class, 'map']);
$router->get('/asrama/map/print',           [BoardingMapController::class, 'printMap']);
$router->get('/asrama/violations',          [BoardingMapController::class, 'violations']);
$router->get('/asrama/violations/print',    [BoardingMapController::class, 'printViolations']);

// Program & Kesehatan (Kepesantrenan)

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
$router->get('/extracurricular/report',         [ExtracurricularController::class, 'report']);
$router->get('/extracurricular/report/print',   [ExtracurricularController::class, 'printReport']);
$router->get('/extracurricular/master',         [ExtracurricularController::class, 'index']);
$router->post('/extracurricular/store',         [ExtracurricularController::class, 'store']);
$router->post('/extracurricular/schedule/store', [ExtracurricularController::class, 'storeSchedule']);
$router->post('/extracurricular/schedule/update',[ExtracurricularController::class, 'updateSchedule']);
$router->get('/extracurricular/schedule/delete', [ExtracurricularController::class, 'deleteSchedule']);
$router->post('/extracurricular/coach/store',    [ExtracurricularController::class, 'storeCoach']);
$router->get('/extracurricular/coach/delete',    [ExtracurricularController::class, 'deleteCoach']);
$router->get('/extracurricular/delete',          [ExtracurricularController::class, 'delete']);

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

$router->get('/report/print',   [ReportController::class, 'printReport']);


// ============================================================================
// 12. STUDENT PORTAL
// ============================================================================
$router->get('/student/dashboard',      [StudentController::class, 'index']);
$router->get('/student/profile',        [StudentController::class, 'profile']);
$router->get('/student/biodata',        [StudentController::class, 'biodata']);
$router->get('/student/resume',         [StudentController::class, 'resume']);
$router->get('/student/schedule',       [StudentController::class, 'schedule']);
$router->get('/student/attendance',     [StudentController::class, 'attendance']);
$router->get('/student/grades',         [StudentController::class, 'grades']);

$router->get('/student/payment',        [StudentController::class, 'payment']);
$router->post('/student/payment/store', [StudentController::class, 'storePayment']);
$router->get('/student/billing',        [StudentController::class, 'billing']); 

$router->get('/student/documents',      [StudentController::class, 'documents']);
$router->post('/student/documents/store',[StudentController::class, 'storeDocument']);
$router->get('/student/exam-card',      [StudentController::class, 'printExamCard']);

$router->get('/student/announcements',  [StudentController::class, 'announcements']);
$router->get('/student/extracurricular',[StudentController::class, 'extracurricular']);
$router->get('/student/boarding',       [StudentController::class, 'boarding']);
$router->get('/student/discipline',     [StudentController::class, 'discipline']);
$router->get('/student/letter',         [StudentController::class, 'letter']);
$router->get('/student/letter/print',   [StudentController::class, 'printLetter']);
$router->get('/student/health',         [StudentController::class, 'health']);


// ============================================================================
// 13. API ENDPOINTS
// ============================================================================
$router->get('/api/wilayah/provinces', [\App\Controllers\Api\WilayahController::class, 'getProvinces']);
$router->get('/api/wilayah/regencies', [\App\Controllers\Api\WilayahController::class, 'getRegencies']);
$router->get('/api/wilayah/districts', [\App\Controllers\Api\WilayahController::class, 'getDistricts']);
$router->get('/api/wilayah/villages',  [\App\Controllers\Api\WilayahController::class, 'getVillages']);


// ============================================================================
// 14. ALIAS ROUTES (URL dari database menu → controller yang benar)
// ============================================================================
$router->get('/settings/school',                [SettingsController::class, 'school']);
$router->post('/settings/school/update',        [SettingsController::class, 'updateSchool']);

$router->get('/master/classrooms',              [ClassroomManageController::class, 'index']);
$router->post('/master/classrooms/store',       [ClassroomManageController::class, 'store']);
$router->post('/master/classrooms/update',      [ClassroomManageController::class, 'update']);
$router->get('/master/classrooms/delete',       [ClassroomManageController::class, 'delete']);

$router->get('/student-affairs/teachers',       [TeacherController::class, 'index']);
$router->get('/student-affairs/attendance',     [StudentAffairsController::class, 'attendance']);
$router->get('/student-affairs/discipline',     [DisciplineController::class, 'index']);
$router->get('/student-affairs/achievements',   [DisciplineController::class, 'achievements']);
$router->post('/student-affairs/achievements/store',  [DisciplineController::class, 'storeAchievement']);
$router->post('/student-affairs/achievements/update', [DisciplineController::class, 'updateAchievement']);
$router->get('/student-affairs/achievements/delete',  [DisciplineController::class, 'deleteAchievement']);
$router->get('/student-affairs/counseling',     [DisciplineController::class, 'counseling']);
$router->post('/student-affairs/counseling/store',    [DisciplineController::class, 'storeCounseling']);
$router->post('/student-affairs/counseling/update',   [DisciplineController::class, 'updateCounseling']);
$router->get('/student-affairs/counseling/delete',    [DisciplineController::class, 'deleteCounseling']);
$router->get('/student-affairs/alumni',         [AlumniController::class, 'index']);

$router->get('/homeroom/report-all',            [HomeroomReportController::class, 'index']);

$router->get('/boarding/dorms',                 [BoardingController::class, 'dorms']);
$router->post('/boarding/dorms/store',          [BoardingController::class, 'storeDorm']);
$router->get('/boarding/dorms/students',        [BoardingController::class, 'dormStudents']);
$router->post('/boarding/assign',               [BoardingController::class, 'assignDorm']);
$router->get('/boarding/supervisors',           [BoardingSupervisorController::class, 'index']);
$router->post('/boarding/supervisors/store',    [BoardingSupervisorController::class, 'store']);
$router->get('/boarding/supervisors/delete',    [BoardingSupervisorController::class, 'delete']);
$router->get('/boarding/activities',            [BoardingActivityController::class, 'index']);
$router->post('/boarding/activities/store',     [BoardingActivityController::class, 'store']);
$router->get('/boarding/activities/delete',     [BoardingActivityController::class, 'delete']);
$router->get('/boarding/mutations',             [BoardingMutationController::class, 'index']);
$router->post('/boarding/mutations/store',      [BoardingMutationController::class, 'store']);

$router->get('/staff/positions',                [StaffPositionController::class, 'index']);
$router->post('/staff/positions/store',         [StaffPositionController::class, 'store']);
$router->post('/staff/positions/update',        [StaffPositionController::class, 'update']);
$router->get('/staff/positions/delete',         [StaffPositionController::class, 'delete']);
$router->get('/staff/members',                  [StaffController::class, 'index']);
$router->post('/staff/members/store',           [StaffController::class, 'store']);
$router->post('/staff/members/update',          [StaffController::class, 'update']);
$router->post('/staff/members/reset-password',  [StaffController::class, 'resetPassword']);
$router->post('/staff/members/toggle-status',   [StaffController::class, 'toggleStatus']);
$router->get('/staff/members/delete',           [StaffController::class, 'delete']);
$router->get('/staff/attendance',               [StaffAttendanceController::class, 'staff']);
$router->post('/staff/attendance/store',        [StaffAttendanceController::class, 'store']);

$router->get('/student/profile',                [StudentController::class, 'profile']);

// Portal Orang Tua
$router->get('/portal/orangtua',                [ParentsController::class, 'portalIndex']);
$router->get('/portal/orangtua/anak',           [ParentsController::class, 'portalChild']);
$router->get('/portal/orangtua/absensi',        [ParentsController::class, 'portalAbsensi']);
$router->get('/portal/orangtua/nilai',          [ParentsController::class, 'portalNilai']);
$router->get('/portal/orangtua/pembayaran',     [ParentsController::class, 'portalPembayaran']);
$router->get('/portal/orangtua/kedisiplinan',   [ParentsController::class, 'portalKedisiplinan']);
$router->get('/portal/orangtua/asrama',         [ParentsController::class, 'portalAsrama']);
$router->get('/portal/orangtua/kesehatan',      [ParentsController::class, 'portalKesehatan']);
$router->get('/portal/orangtua/jadwal',         [ParentsController::class, 'portalJadwal']);
$router->get('/portal/orangtua/pengumuman',     [ParentsController::class, 'portalPengumuman']);

// Guideline
$router->get('/guideline', [GuidelineController::class, 'index']);

// Execute Router
$router->resolve();
