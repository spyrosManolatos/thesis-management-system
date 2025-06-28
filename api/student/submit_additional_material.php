<?php $userType="student";
include ("../../includes/auth/illegal_redirection.php");?>
<?php
session_start();
require_once "../../config/db_config.php";
$connect = getDbConnection();
$username = $_SESSION['username'];
$additional_material_file = $_POST['additional_material_file'];
$description = $_POST['additional_material_description'];
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
    
    if(!isset($assignment_id) || empty($assignment_id)) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Assignment ID is required."));
        exit;
    }
    
    // Insert additional material into the database
    $query = $connect->prepare("
    INSERT INTO additional_student_material (thesis_assignment_id, st_material_link,description)
    VALUES (:assignment_id, :additional_material_file, :description)
    ");
    $query->bindParam(':assignment_id', $assignment_id);
    $query->bindParam(':additional_material_file', $additional_material_file);
    $query->bindParam(':description', $description);
    $query->execute();
    echo json_encode(array("success" => true, "message" => "Additional material submitted successfully.")); 
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => $e->getMessage()));
}