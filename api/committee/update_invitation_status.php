<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php';?>
<?php
    require_once '../../config/db_config.php';
    session_start();
    $invitationId = $_GET['invitation_id'];
    $status = $_GET['status'];

    $session_username = $_SESSION['username']; // Renamed to avoid conflict


    try {
        $pdo = getDbConnection();
        
        // Get teacher ID from session username
        $stmt = $pdo->prepare("
            SELECT teacher_id
            FROM teacher
            WHERE teacher.username = :username
        ");
        $stmt->bindParam(":username", $session_username, PDO::PARAM_STR);
        $stmt->execute();
        $t_id = $stmt->fetchAll();
        
        // Update invitation status
        $stmt = $pdo->prepare("
            UPDATE committee_invitations
            SET status = :new_status,
                response_date = CURRENT_DATE
            WHERE invitation_id = :assign_id 
        ");
        $stmt->bindParam(":new_status", $status, PDO::PARAM_STR); // Added missing parameter binding
        $stmt->bindParam(":assign_id", $invitationId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Get thesis assignment ID from invitation
        $stmt = $pdo->prepare("
            SELECT thesis_assignments.thesis_assignment_id
            FROM committee_invitations
            INNER JOIN thesis_assignments
                ON committee_invitations.thesis_assignment_id = thesis_assignments.thesis_assignment_id
            WHERE committee_invitations.invitation_id = :inv_id
        ");
        $stmt->bindParam(":inv_id", $invitationId, PDO::PARAM_INT);
        $stmt->execute();
        $thesis_assignment_id = $stmt->fetchAll();
        
        // Count accepted invitations
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM committee_invitations
            WHERE thesis_assignment_id = :assign_id AND status = :accepted_status
        ");
        $stmt->bindParam(":assign_id", $thesis_assignment_id[0]['thesis_assignment_id'], PDO::PARAM_INT);
        $accepted_status = "accepted"; // Define variable before binding
        $stmt->bindParam(":accepted_status", $accepted_status, PDO::PARAM_STR);
        $stmt->execute();
        $accepted_invitations = $stmt->fetchColumn();
        
        // If 2 accepted invitations, create committee
        if($accepted_invitations == 2){
            // Create committee
            $stmt = $pdo->prepare('
                INSERT INTO committee(
                    thesis_assignment_id
                )
                VALUES(:assignment_id);
            ');
            $stmt->bindParam(":assignment_id", $thesis_assignment_id[0]['thesis_assignment_id'], PDO::PARAM_INT);
            $stmt->execute();
            
            // Get professor IDs who accepted invitations
            $stmt = $pdo->prepare("
                SELECT professor_id
                FROM committee_invitations
                WHERE thesis_assignment_id = :assign_id AND status = :accepted_status
            ");
            $stmt->bindParam(":assign_id", $thesis_assignment_id[0]['thesis_assignment_id'], PDO::PARAM_INT);
            $stmt->bindParam(":accepted_status", $accepted_status, PDO::PARAM_STR);
            $stmt->execute();
            $teacher_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Update committee invitation that are invited to rejected
            $stmt = $pdo->prepare("
                UPDATE committee_invitations
                SET status = 'rejected'
                WHERE thesis_assignment_id = :assign_id AND status = 'invited'
            ");
            $stmt->bindParam(":assign_id", $thesis_assignment_id[0]['thesis_assignment_id'], PDO::PARAM_INT);
            $stmt->execute();
            
            
            // Add committee members
            foreach($teacher_ids as $teacher) {
                $stmt = $pdo->prepare('
                    INSERT INTO committee_members(
                        com_id,
                        teacher_id,
                        is_supervisor
                    )
                    VALUES (LAST_INSERT_ID(), :teacher_id, :is_superv);
                ');
                $stmt->bindParam(":teacher_id", $teacher['professor_id'], PDO::PARAM_INT);
                $is_not_supervisor = false;
                $stmt->bindParam(":is_superv", $is_not_supervisor, PDO::PARAM_BOOL);
                $stmt->execute();
            }
            
            // Get supervisor ID
            $stmt = $pdo->prepare("
                SELECT supervisor_id
                FROM thesis_assignments
                INNER JOIN thesis_topics
                    ON thesis_assignments.topic_id = thesis_topics.id
                WHERE thesis_assignments.thesis_assignment_id = :assignment_id
            ");
            $stmt->bindParam(":assignment_id", $thesis_assignment_id[0]['thesis_assignment_id'], PDO::PARAM_INT);
            $stmt->execute();
            $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Add supervisor as committee member
            $stmt = $pdo->prepare('
                INSERT INTO committee_members(
                    com_id,
                    teacher_id,
                    is_supervisor
                )
                VALUES (LAST_INSERT_ID(), :teacher_id, :is_superv);
            ');
            $stmt->bindParam(":teacher_id", $supervisor['supervisor_id'], PDO::PARAM_INT);
            $is_supervisor = true;
            $stmt->bindParam(":is_superv", $is_supervisor, PDO::PARAM_BOOL);
            $stmt->execute();
            $stmt = $pdo->prepare('
                UPDATE thesis_assignments
                SET status = :act_status
                WHERE thesis_assignment_id = :th_as_id
            ');
            $active_status = "active";
            $stmt->bindParam(":act_status", $active_status, PDO::PARAM_STR);
            $stmt->bindParam(":th_as_id", $thesis_assignment_id[0]['thesis_assignment_id'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $pdo->prepare("
        INSERT INTO thesis_logs(
            thesis_assignment_id,
            change_log
        )
        VALUES(
            :th_as_id,
            :log_text)");
        $log_text = "Η ΑΝΑΘΕΣΗ ΕΝΕΡΓΟΠΟΙΗΘΗΚΕ";
        $stmt->bindParam(":th_as_id",$thesis_assignment_id[0]['thesis_assignment_id'],PDO::PARAM_INT);
        $stmt->bindParam(":log_text",$log_text,PDO::PARAM_STR);
        $stmt->execute();
            echo json_encode([
                "success" => true,
                "committee_created" => true
            ]);
        } else {
            echo json_encode([
                "success" => true,
                "committee_created" => false
            ]);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
?>