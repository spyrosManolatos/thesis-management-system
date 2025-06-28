<?php $userType="teacher"; include '../../includes/auth/illegal_redirection.php';?>
<?php
    $assignId = $_GET['assignment_id'];
    $notes = $_POST['notes'];
    $title = $_POST['title'];
    $username = $_SESSION['username'];
    require_once '../../config/db_config.php';
    try {
        $pdo = getDbConnection();
        // Check if the assignment exists and the user has permission to add notes
        $stmt = $pdo->prepare("
            SELECT thesis_assignments.thesis_assignment_id
            FROM thesis_assignments
            INNER JOIN thesis_topics
                ON thesis_assignments.topic_id = thesis_topics.id
            LEFT JOIN teacher AS supervisor
                ON supervisor.teacher_id = thesis_topics.supervisor_id
            LEFT JOIN committee
                ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
            LEFT JOIN committee_members
                ON committee_members.com_id = committee.com_id
            LEFT JOIN teacher AS committee_member
                ON committee_member.teacher_id = committee_members.teacher_id
            WHERE thesis_assignments.thesis_assignment_id = :assignmentId AND (committee_member.username = :username OR supervisor.username = :username) AND thesis_assignments.status != 'Cancelled'
        ");
        $stmt->bindParam(":assignmentId", $assignId, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$assignment) {
            http_response_code(404);
            echo json_encode(["error" => "Assignment not found"]);
            exit;
        }
        $stmt =$pdo->prepare("SELECT teacher_id FROM teacher WHERE username = :usr");
        $stmt->bindParam(":usr",$username,PDO::PARAM_STR);
        $stmt->execute();
        $t_id = $stmt->fetchColumn();
        $stmt = $pdo->prepare("
        INSERT INTO professor_notes(
            assignment_id,
            note_content,
            professor_id,
            title
        )
        values(
            :assign_id,
            :note_content,
            :teacher_id,
            :title
        
        );
        ");
        $stmt->bindParam(":assign_id",$assignId,PDO::PARAM_INT);
        $stmt->bindParam(":note_content",$notes,PDO::PARAM_STR);
        $stmt->bindParam(":teacher_id",$t_id,PDO::PARAM_INT);
        $stmt->bindParam(":title",$title,PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(["success"=>true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error_db"=>$e->getMessage()]);
    }


?>