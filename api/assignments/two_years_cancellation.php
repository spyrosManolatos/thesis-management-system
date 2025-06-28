<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php';?>
<?php
    session_start();
    $assignmentId = $_GET['assignment_id'];
    $assembly_number = $_POST['cancelAssembly'];
    $assembly_year = $_POST['yearAssembly'];

    require_once '../../config/db_config.php';

    
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('
            SELECT thesis_assignments.thesis_assignment_id
            FROM thesis_assignments
            INNER JOIN thesis_topics
                ON thesis_assignments.topic_id = thesis_topics.id
            INNER JOIN teacher AS supervisor
                ON supervisor.teacher_id = thesis_topics.supervisor_id
            LEFT JOIN committee
                ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
            LEFT JOIN committee_members
                ON committee_members.com_id = committee.com_id
            LEFT JOIN teacher AS committee_member
                ON committee_member.teacher_id = committee_members.teacher_id
            WHERE thesis_assignments.thesis_assignment_id = :assignmentId AND (supervisor.username = :username)');
        $stmt->bindParam(":assignmentId", $assignmentId, PDO::PARAM_INT);
        $stmt->bindParam(":username", $_SESSION['username'], PDO::PARAM_STR);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$assignment) {
            echo json_encode(["success" => false, "reason" => "Invalid assignment ID or user not authorized"]);
            exit;
        }
        $stmt = $pdo->prepare('
            SELECT assembly_number
            FROM assembly_decisions
            WHERE 
                thesis_assignment_id = :assignment_id AND 
                assembly_year= :assembly_yr AND 
                assembly_number = :assembly_number AND 
                assembly_decision = "Ακύρωση Ανάθεσης Λόγω Πάροδου Χρόνου"
        ');
        $stmt->bindParam(":assignment_id",$assignmentId,PDO::PARAM_INT);
        $stmt->bindParam(':assembly_yr', $assembly_year, PDO::PARAM_INT);
        $stmt->bindParam(':assembly_number', $assembly_number, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetchColumn() === false) {
            echo json_encode(["success" => false, "reason" => "No assembly found"]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT MAX(response_date)
            FROM committee_invitations
            WHERE thesis_assignment_id = :getAssignmentId
        ");
        $stmt->bindParam(":getAssignmentId", $assignmentId, PDO::PARAM_INT);
        $stmt->execute();
        $assignment_date = $stmt->fetchColumn();

        if (!$assignment_date) {
            echo json_encode(["success" => false, "reason" => "No response date found"]);
            exit;
        }

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
            $stmt = $pdo->prepare("
            INSERT INTO thesis_logs(
                thesis_assignment_id,
                change_log
            )
            VALUES(
                :th_as_id,
                'Ακύρωση λόγω παρόδου 2 ετών από την επίσημη ανάθεση(λόγω διδάσκοντα)')");
            $stmt->bindParam(":th_as_id",$assignmentId,PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["success" => true, "message" => "Assignment cancelled successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Two years have not passed"]);
        }
    }catch(PDOException $e){
        http_response_code(500);
        echo json_encode(["success"=>false,"reason"=>$e->getMessage()]);
    }



?>