<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php';?>
<?php
    require_once '../../config/db_config.php';
    $assignmentId = $_GET['assignment_id'];
    

    try {
        $pdo = getDbConnection();
        // Check if the assignment exists and the user has permission to view it
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
            WHERE thesis_assignments.thesis_assignment_id = :assignmentId AND (committee_member.username = :username OR supervisor.username = :username)
        ");
        $stmt->bindParam(":assignmentId", $assignmentId, PDO::PARAM_INT);
        $stmt->bindParam(":username", $_SESSION['username'], PDO::PARAM_STR);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$assignment) {
            http_response_code(404);
            echo json_encode(["error" => "Assignment not found"]);
            exit;
        }
        $stmt = $pdo->prepare("
            SELECT 
                is_supervisor,
                teacher.name as teacher_name,
                marks.final_mark as mark,
                thesis_assignments.status as status,
                marks.quality_completeness,
                marks.readable_thesis,
                marks.targets_fulfiled,
                committee.avg_mark as avg_mark
            FROM committee_members
            INNER JOIN teacher
                ON teacher.teacher_id = committee_members.teacher_id
            INNER JOIN committee
                ON committee.com_id = committee_members.com_id
            LEFT JOIN marks
                ON committee_members.mark_id = marks.mark_id
            INNER JOIN thesis_assignments
                ON thesis_assignments.thesis_assignment_id = committee.thesis_assignment_id
            WHERE committee.thesis_assignment_id = :th_as_id;
        ");
        $stmt->bindParam(":th_as_id", $assignmentId, PDO::PARAM_INT);
        $stmt->execute();
        $invitations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        

        echo json_encode($invitations);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
?>