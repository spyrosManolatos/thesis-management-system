<?php $userType="secrertary"; include "../../includes/auth/illegal_redirection.php"?>
<?php
require_once '../../config/db_config.php';
try {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("
            SELECT 
                assignment_date,
                student.name,
                thesis_topics.title,
                thesis_topics.description,
                thesis_assignments.status,
                thesis_assignments.thesis_assignment_id
            FROM 
                thesis_assignments
            INNER JOIN
                thesis_topics on thesis_topics.id = thesis_assignments.topic_id
            INNER JOIN
                student on student.student_id = thesis_assignments.student_id
            WHERE status='Active' or (status='Under Examination' or status='Under Grading');");
    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the list of topics as JSON
    echo json_encode($assignments);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error:' . $e->getMessage()]);
}
