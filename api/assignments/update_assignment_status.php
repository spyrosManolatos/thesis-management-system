<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php';?>
<?php
    session_start();
    $username = $_SESSION['username'];
    
    $status = $_GET['status'];
    $assignmentId = $_GET['assignment_id'];
    
    require_once '../../config/db_config.php';

    try {
        $pdo = getDbConnection();
        // Check if the assignment exists and the user has permission to update it
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
        $stmt->bindParam(":assignmentId", $assignmentId, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$assignment) {
            http_response_code(404);
            echo json_encode(["error" => "Assignment not found"]);
            exit;
        }
        $stmt = $pdo->prepare("
            SELECT status
            FROM thesis_assignments
            WHERE thesis_assignments.thesis_assignment_id = :getAssignmentId 
        ");
        $stmt->bindParam(":getAssignmentId",$assignmentId,PDO::PARAM_INT);
        $stmt->execute();
        $old_status = $stmt->fetchColumn();
        if($old_status == 'Pending' && $status == 'Cancelled'){
            $stmt = $pdo->prepare("
                UPDATE thesis_assignments
                SET status = :rej_status
                WHERE thesis_assignment_id = :getAssignmentId
            ");
            $rej_status = 'Cancelled';
            $stmt->bindParam(":rej_status",$rej_status,PDO::PARAM_STR);
            $stmt->bindParam(":getAssignmentId",$assignmentId,PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $pdo->prepare("
                UPDATE committee_invitations
                SET status = :rej_status
                WHERE thesis_assignment_id = :getAssignmentId
            ");
            $rej_status = 'Cancelled';
            $stmt->bindParam(":rej_status",$rej_status,PDO::PARAM_STR);
            $stmt->bindParam(":getAssignmentId",$assignmentId,PDO::PARAM_INT);
            $stmt->execute();
        }
        elseif($old_status == 'Active' && $status == 'Cancelled'){
            // when the last one agreed is the official assignment date
            $stmt = $pdo->prepare("
                SELECT MAX(response_date)
                FROM committee_invitations
                WHERE thesis_assignment_id = :getAssignmentId
            ");
            $stmt->bindParam(":getAssignmentId",$assignmentId,PDO::PARAM_INT);
            $stmt->execute();
            $assignment_date = $stmt->fetchColumn();
            $current_date = date('Y-m-d');
            $dateDifference = date_diff(date_create($assignment_date), date_create($current_date))->y;
            // 2 years after the assignment date we need to cancel it
            if ($dateDifference >= 2) {
                $stmt = $pdo->prepare("
                    UPDATE thesis_assignments
                    SET status = :canc_status
                    WHERE thesis_assignment_id = :getAssignmentId
                ");
                $canc_status = 'Cancelled';
                $stmt->bindParam(":canc_status", $canc_status, PDO::PARAM_STR);
                $stmt->bindParam(":getAssignmentId", $assignmentId, PDO::PARAM_INT);
                $stmt->execute();
            }
            else{
                echo json_encode(["success" => "false","assignmentId" => $assignmentId,"status" => $status]);
                exit;
            }
        }
        else{
            $stmt = $pdo->prepare("
                UPDATE thesis_assignments
                SET status = :new_status
                WHERE thesis_assignment_id = :getAssignmentId 
            ");
            $stmt->bindParam(":getAssignmentId",$assignmentId,PDO::PARAM_INT);
            $stmt->bindParam(":new_status",$status,PDO::PARAM_STR);
            $stmt->execute();
        }
        $stmt = $pdo->prepare("
        INSERT INTO thesis_logs(
            thesis_assignment_id,
            change_log
        )
        VALUES(
            :th_as_id,
            :log_text)");
        $statusTranslations = [
            "Pending" => "Υπό Ανάθεση",
            "Cancelled" => "Ακυρώση",
            "Active" => "Ενεργό",
            "Completed" => "Ολοκληρωμένη",
            "Under Examination" => "Υπό Εξέταση",
            "Under Grading" => "Υπό Βαθμολόγηση"
        ];
        $old_status_gr = isset($statusTranslations[$old_status]) ? $statusTranslations[$old_status] : $old_status;
        $status_gr = isset($statusTranslations[$status]) ? $statusTranslations[$status] : $status;
        $log_text = "Από $old_status_gr σε $status_gr";
        $stmt->bindParam(":th_as_id",$assignmentId,PDO::PARAM_INT);
        $stmt->bindParam(":log_text",$log_text,PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(["success" => true, "assignmentId" => $assignmentId, "old_status"=>$old_status ,"status" => $status]);
        
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success"=>"false","error" => "database_error: ".$e->getMessage()]);
    }





?>