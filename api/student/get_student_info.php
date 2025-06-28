<?php
$userType = 'student';
include '../../includes/auth/illegal_redirection.php';?>
<?php
require_once '../../config/db_config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
$username=$_SESSION['username'];
ob_clean(); // Καθαρίζουμε buffer

try {
	$conn=getDBConnection();
    $stmt = $conn->prepare("SELECT name FROM student WHERE username = :st;");
	$stmt->bindParam(':st',$username,PDO::PARAM_STR);
    $stmt->execute();

    $theses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($theses, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Σφάλμα βάσης δεδομένων: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>