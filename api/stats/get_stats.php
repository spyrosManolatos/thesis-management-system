<?php $userType = "teacher"; include '../../includes/auth/illegal_redirection.php'; ?>
<?php
session_start();
$username = $_SESSION['username'];
$statsValue = $_GET['stats'];
require_once '../../config/db_config.php';
try {
    $pdo = getDbConnection();
    // supervisor assignments (reminder: topic creator is the supervisor)
    if ($statsValue == 'thesis_quantity') {
        $stmt = $pdo->prepare('
            SELECT COUNT(*)
            FROM thesis_assignments
            INNER JOIN thesis_topics
            ON thesis_topics.id = thesis_assignments.topic_id
            INNER JOIN teacher
            ON thesis_topics.supervisor_id = teacher.teacher_id
            WHERE username = :teacher_username and thesis_assignments.status != :canc_status;');
        $stmt->bindParam(":teacher_username", $username, PDO::PARAM_STR);
        $canc_status = "Cancelled";
        $stmt->bindParam(":canc_status", $canc_status, PDO::PARAM_STR);
        $stmt->execute();
        $supervisor_assignments = $stmt->fetchColumn();
        // committee assignments (AND NOT SUPERVISOR )
        $stmt = $pdo->prepare('
            SELECT COUNT(*)
            FROM committee_members
            INNER JOIN teacher
            ON teacher.teacher_id = committee_members.teacher_id
            WHERE teacher.username = :teacher_username and committee_members.is_supervisor = false');
        $stmt->bindParam(":teacher_username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $committee_assignments = $stmt->fetchColumn();
        echo json_encode(["supervisor_assignments" => $supervisor_assignments, "committee_assignments" => $committee_assignments]);
    } elseif ($statsValue == 'average_thesis_mark') {
        // first for supervisors
        $stmt = $pdo->prepare("
                SELECT IFNULL(avg(final_mark),0)
                FROM marks
                INNER JOIN committee_members
                ON marks.mark_id = committee_members.mark_id
                INNER JOIN teacher
                ON committee_members.teacher_id = teacher.teacher_id
                WHERE teacher.username = :t_username and committee_members.is_supervisor = :is_supervisor
                GROUP BY teacher.teacher_id
            ");
        $stmt->bindParam(":t_username", $username, PDO::PARAM_STR);
        $is_supervisor = true;
        $stmt->bindParam(":is_supervisor", $is_supervisor, PDO::PARAM_STR);
        $stmt->execute();
        $supervisor_average = $stmt->fetchColumn();
        if ($supervisor_average == false) {
            $supervisor_average = 0;
        }
        $stmt = $pdo->prepare("
                SELECT IFNULL(avg(final_mark),0)
                FROM marks
                INNER JOIN committee_members
                ON marks.mark_id = committee_members.mark_id
                INNER JOIN teacher
                ON committee_members.teacher_id = teacher.teacher_id
                WHERE teacher.username = :t_username and committee_members.is_supervisor = :is_supervisor
                GROUP BY teacher.teacher_id
            ");
        $stmt->bindParam(":t_username", $username, PDO::PARAM_STR);
        $is_supervisor = false;
        $stmt->bindParam(":is_supervisor", $is_supervisor, PDO::PARAM_STR);
        $stmt->execute();
        $committe_member_average = $stmt->fetchColumn();
        if ($committe_member_average == false) {
            $committe_member_average = 0;
        }
        echo json_encode(["supervisor_average" => $supervisor_average, "committee_average" => $committe_member_average]);
    } elseif ($statsValue == 'thesis_average_time') {
        //supervisor (average time from assignment to completion)
        $stmt = $pdo->prepare("
                SELECT 
                AVG(DATEDIFF(
                    change_timestamp, 
                    (SELECT MAX(response_date) 
                    FROM committee_invitations 
                    WHERE committee_invitations.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    AND committee_invitations.status = 'accepted')
                )) as avg_completion_days
                FROM thesis_logs
                INNER JOIN thesis_assignments ON thesis_assignments.thesis_assignment_id = thesis_logs.thesis_assignment_id
                INNER JOIN committee ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                INNER JOIN committee_members ON committee_members.com_id = committee.com_id
                INNER JOIN teacher ON teacher.teacher_id = committee_members.teacher_id
                WHERE 
                    change_log LIKE '%Ολοκληρωμένη' 
                    AND teacher.username = :t_username
                    AND committee_members.is_supervisor = true
            ");
        $stmt->bindParam(":t_username",$username,PDO::PARAM_STR);
        $stmt->execute();
        $stats_for_completion_supervisor = $stmt->fetchColumn();
        //committee member
        $stmt = $pdo->prepare("
                SELECT 
                AVG(DATEDIFF(
                    change_timestamp, 
                    (SELECT MAX(response_date) 
                    FROM committee_invitations 
                    WHERE committee_invitations.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    AND committee_invitations.status = 'accepted')
                )) as avg_completion_days
                FROM thesis_logs
                INNER JOIN thesis_assignments ON thesis_assignments.thesis_assignment_id = thesis_logs.thesis_assignment_id
                INNER JOIN committee ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                INNER JOIN committee_members ON committee_members.com_id = committee.com_id
                INNER JOIN teacher ON teacher.teacher_id = committee_members.teacher_id
                WHERE 
                    change_log LIKE '%Ολοκληρωμένη' 
                    AND teacher.username = :t_username
                    AND committee_members.is_supervisor = false
            ");
        $stmt->bindParam(":t_username",$username,PDO::PARAM_STR);
        $stmt->execute();
        $stats_for_completion_com_member= $stmt->fetchColumn();
        echo json_encode(["committee_member_completion"=>$stats_for_completion_com_member,"supervisor_completion"=>$stats_for_completion_supervisor]);


    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    http_response_code(500);
    
}





?>