
<?php
require_once '../../config/db_config.php';
header('Content-Type: application/json; charset=utf-8');
$thesisId = $_GET['thesis_id'];
try {
    $conn = getDBConnection();
    // for safety reason, we check only the assignments with a presentation
    $stmt = $conn->prepare("
        SELECT 
            teacher.name,
            committee_members.is_supervisor
        FROM 
            committee_members
        INNER JOIN 
            teacher ON committee_members.teacher_id = teacher.teacher_id
        INNER JOIN 
            committee ON committee_members.com_id = committee.com_id
        INNER JOIN 
            thesis_assignments ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
        INNER JOIN
            student_presentation ON thesis_assignments.thesis_assignment_id = student_presentation.thesis_assignment_id
        WHERE 
            thesis_assignments.thesis_assignment_id = :thesisId;
        order by committee_members.is_supervisor DESC;
    ");
    $stmt->bindParam(':thesisId', $thesisId, PDO::PARAM_INT);
    $stmt->execute();
    $committeeMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($committeeMembers, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Σφάλμα βάσης δεδομένων: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
