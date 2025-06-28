<?php
$userType = 'student';
include '../../includes/auth/illegal_redirection.php';?>
<?php
require_once '../../config/db_config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
ob_clean(); // Καθαρίζουμε buffer

$username = $_SESSION['username'];

try {
    $conn = getDbConnection();
    
    // Βρες το student_id από το username
    $stmt = $conn->prepare("SELECT student_id FROM student WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo json_encode(["error" => "Student not found."]);
        exit;
    }

    $student_id = $student['student_id'];
    // Δεδομένα από POST
    $area = $_POST['residence'] ?? '';
    $email = $_POST['email'] ?? '';
    $mobile_phone = $_POST['phone'] ?? '';

    // Ενημέρωση
    $stmt = $conn->prepare("UPDATE student SET area = :area, email = :email, mobile_phone = :phone WHERE student_id = :student_id");
    $stmt->bindParam(':area', $area, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $mobile_phone, PDO::PARAM_STR);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>