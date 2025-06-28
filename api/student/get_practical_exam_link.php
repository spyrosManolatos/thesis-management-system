<?php $userType="student";
include ("../../includes/auth/illegal_redirection.php");?>
<?php
session_start();
require_once "../../config/db_config.php";
$connect = getDbConnection();
$username = $_SESSION['username'];
try {
    $query = $connect->prepare("
        SELECT thesis_assignments.thesis_assignment_id
        FROM thesis_assignments
        INNER JOIN student ON student.student_id = thesis_assignments.student_id
        WHERE student.username = :username AND thesis_assignments.status != 'Cancelled'
    ");
    $query->bindParam(':username', $username);
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $query->execute();
    $assignment_id = $query->fetchColumn();

    if (!isset($assignment_id) || empty($assignment_id)) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Assignment ID is required."));
        exit;
    }

    // Fetch practical exam link for the given assignment
    $query = $connect->prepare("
        SELECT examination_protocol_path as protocol
        FROM student_presentation
        WHERE thesis_assignment_id = :assignment_id
    ");
    $query->bindParam(':assignment_id', $assignment_id);
    $query->execute();
    $practical_exam_link = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($practical_exam_link);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => $e->getMessage()));
}