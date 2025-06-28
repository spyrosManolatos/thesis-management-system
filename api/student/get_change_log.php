<?php $userType = "student";
include ("../../includes/auth/illegal_redirection.php");?>

<?php
session_start();



require_once "../../config/db_config.php";

$connect = getDbConnection();
$username = $_SESSION['username'];
try{
    $query = $connect->prepare("SELECT tl.thesis_assignment_id FROM thesis_logs tl INNER JOIN thesis_assignments ta ON ta.thesis_assignment_id = tl.thesis_assignment_id INNER JOIN student s ON s.student_id = ta.student_id WHERE s.username = :username and ta.status!='Cancelled'");
    $query -> bindParam(':username', $username);
    $query -> execute();
    $result = $query -> fetchColumn();
    $assignment_id = $result;

    $query = $connect->prepare("SELECT DATE_FORMAT(change_timestamp, '%d-%m-%Y %H:%i') AS change_timestamp, change_log FROM thesis_logs WHERE thesis_assignment_id = :assignment_id ");
    $query -> bindParam(':assignment_id', $assignment_id);
    $query -> execute();
    $logs = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($logs);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => $e->getMessage()));
}