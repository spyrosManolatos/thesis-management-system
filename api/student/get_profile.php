<?php
$userType = 'student';
include '../../includes/auth/illegal_redirection.php';?>
<?php
session_start();
require_once '../../config/db_config.php';

header('Content-Type: application/json');

$username = $_SESSION['username'];

// Χρησιμοποιώντας το PDO για σύνδεση
$conn = getDbConnection();

// Προετοιμάζουμε και εκτελούμε το query
$sql = "SELECT s.student_id, s.name, s.area, s.email, s.mobile_phone, s.username, u.password
        FROM student s
        JOIN user_det u ON s.username = u.USER
        WHERE s.username = :username";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo json_encode(['success' => true, 'profile' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Profile not found']);
}
?>