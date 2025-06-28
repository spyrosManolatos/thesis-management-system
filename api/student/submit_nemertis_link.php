<?php $userType = "student";
include "../../includes/auth/illegal_redirection.php"; ?>
<?php
session_start();
require_once '../../config/db_config.php';
$connect = getDbConnection();
$username = $_SESSION['username'];
$nemertis_link = $_POST['nemertis_link'];
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
    // Check if already exists
    $checkQuery = $connect->prepare("
        SELECT COUNT(*) 
        FROM thesis_assignments
        INNER JOIN thesis_nemertis_links ON thesis_assignments.thesis_assignment_id = thesis_nemertis_links.thesis_assignment_id
        WHERE thesis_assignments.thesis_assignment_id = :assignment_id AND nemertis_link IS NOT NULL
       ");
    $checkQuery->bindParam(':assignment_id', $assignment_id);
    $checkQuery->execute();
    $exists = $checkQuery->fetchColumn();
    if ($exists > 0) {
        echo json_encode(array("success" => false, "message" => "Nemertis link already exists for this assignment."));
        exit;
    }

    // Update nemertis link in the database
    $query = $connect->prepare("
        INSERT INTO thesis_nemertis_links (thesis_assignment_id, nemertis_link)
        VALUES (:assignment_id, :nemertis_link)
    ");
    $query->bindParam(':assignment_id', $assignment_id);
    $query->bindParam(':nemertis_link', $nemertis_link);
    $query->execute();

    echo json_encode(array("success" => true, "message" => "Nemertis link submitted successfully."));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("message" => $e->getMessage()));
}
?>