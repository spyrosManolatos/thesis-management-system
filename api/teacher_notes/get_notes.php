<?php $userType="teacher"; include '../../includes/auth/illegal_redirection.php';?>
<?php
    session_start();
    require_once '../../config/db_config.php';
    $assignmentId = $_GET['assignment_id'];
    $username = $_SESSION['username'];

    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
        SELECT
            prof_note,
            note_content,
            DATE_FORMAT(date_created, '%d-%m-%Y') AS date_created,
            title
        FROM
            professor_notes
        INNER JOIN
            teacher
        ON teacher.teacher_id = professor_notes.professor_id
        WHERE
            teacher.username = :t_usr and assignment_id = :my_assignment_id
        ");
        $stmt->bindParam(":t_usr",$username,PDO::PARAM_STR);
        $stmt->bindParam(":my_assignment_id",$assignmentId,PDO::PARAM_INT);
        $stmt->execute();
        $prof_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($prof_notes);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error"=>$e->getMessage()]);
    }

?>