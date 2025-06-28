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
    $stmt = $conn->prepare("
	SELECT
        thesis_assignments.thesis_assignment_id,
        thesis_topics.description,
        thesis_topics.title,
        thesis_topics.pdf_file_path,
        thesis_assignments.status,
        supervisor.name AS supervisor_name,
        (SELECT DATE_FORMAT(thesis_logs.change_timestamp, '%d-%m-%Y')
        FROM thesis_logs
        WHERE thesis_logs.thesis_assignment_id = thesis_assignments.thesis_assignment_id and thesis_logs.change_log = 'ΕΠΙΣΗΜΗ ΑΝΑΘΕΣΗ ΘΕΜΑΤΟΣ'
        ) AS official_assignment_date
    FROM thesis_assignments 
    INNER JOIN thesis_topics
    ON thesis_topics.id=thesis_assignments.topic_id
    INNER JOIN student 
    ON student.student_id=thesis_assignments.student_id
    INNER JOIN teacher as supervisor
    ON supervisor.teacher_id=thesis_topics.supervisor_id
    WHERE student.username = :st AND thesis_assignments.status !='cancelled';
    ");
	$stmt->bindParam(':st',$username,PDO::PARAM_STR);
    $stmt->execute();
    $theses = $stmt->fetchAll(PDO::FETCH_ASSOC);    
    echo json_encode($theses, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Σφάλμα βάσης δεδομένων: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>