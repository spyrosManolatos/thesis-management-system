<?php $userType= "teacher";include '../../includes/auth/illegal_redirection.php'; ?>
<?php
require_once '../../config/db_config.php';

try {
    $pdo = getDbConnection();
    session_start();
    // Check if the user has permission to view thesis topics
    $stmt = $pdo->prepare("
        SELECT thesis_topics.id
        FROM thesis_topics
        INNER JOIN teacher ON supervisor_id = teacher_id
        WHERE teacher.username = :supervisor_id");
    $stmt->bindParam(':supervisor_id', $_SESSION['username'], PDO::PARAM_STR);
    $stmt->execute();

    $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$supervisor) {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'You do not have permission to view thesis topics.']);
        exit;
    }
    // for the assignment dropdown we only care about the non-assigned or cancelled
    if ($_GET['thesis_topic_dropdown'] == 'true') {
        $stmt = $pdo->prepare("
            SELECT 
                distinct title
            FROM 
                thesis_topics
            INNER JOIN
                teacher on supervisor_id=teacher_id
            INNER JOIN
                user_det on user=username
            LEFT JOIN 
                thesis_assignments on thesis_assignments.topic_id = thesis_topics.id
            WHERE 
                user_det.USER = :supervisor_id AND (thesis_assignments.status IS NULL OR thesis_assignments.status = 'Cancelled' OR thesis_assignments.status = 'Pending')");
       
        $stmt->bindParam(':supervisor_id', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->execute();
        $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($topics);
    }
    // Fetch all thesis topics for this supervisor and that are not assigned
    else {
        $stmt = $pdo->prepare("
        SELECT 
                distinct id,
                title,
                description,
                pdf_file_path
            FROM 
                thesis_topics
            INNER JOIN
                teacher on supervisor_id=teacher_id
            INNER JOIN
                user_det on user=username
            LEFT JOIN 
                thesis_assignments on thesis_assignments.topic_id = thesis_topics.id
            WHERE 
                user_det.USER = :supervisor_id AND (thesis_assignments.status='Pending' or thesis_assignments.status is NULL OR thesis_assignments.status = 'Cancelled')
        ");

        $stmt->bindParam(':supervisor_id', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->execute();
        $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($topics);
    }
} catch (PDOException $e) {
    http_response_code(500); // Server error
    echo json_encode(['error' => $e->getMessage()]);
}
