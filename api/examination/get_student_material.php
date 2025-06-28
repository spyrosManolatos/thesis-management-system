<?php $userType="teacher";
include ("../../includes/auth/illegal_redirection.php");?>
<?php
session_start();
require_once "../../config/db_config.php";
$connect = getDbConnection();
$username = $_SESSION['username'];
$assignment_id=$_GET['assignment_id'];
try {
    $query = $connect->prepare("
    SELECT thesis_assignments.thesis_assignment_id
    FROM thesis_assignments
    INNER JOIN committee ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
    INNER JOIN committee_members ON committee_members.com_id = committee.com_id
    INNER JOIN teacher ON teacher.teacher_id = committee_members.teacher_id
    WHERE teacher.username = :username AND thesis_assignments.thesis_assignment_id = :assignment_id AND thesis_assignments.status != 'Cancelled'
    ");
    $query->bindParam(':username', $username);
    $query->bindParam(':assignment_id', $assignment_id);
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $query->execute();
    $assignment_id = $query->fetchColumn();
    if(!isset($assignment_id) || empty($assignment_id)) {
        http_response_code(400);
        echo json_encode(array("message" => "Assignment ID is required."));
        exit;
    }
    // fetch student material for the given assignment
    $query = $connect->prepare("
    SELECT 
    st_material_link,
    description
    FROM additional_student_material
     WHERE thesis_assignment_id = :assignment_id");
    $query->bindParam(':assignment_id', $assignment_id);
    $query->execute();
    $student_material = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($student_material);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => $e->getMessage()));
}
