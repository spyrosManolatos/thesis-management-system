<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php'; ?>
<?php
    require_once '../../config/db_config.php';
    $username = $_SESSION['username'];
    $assignment_id = $_GET['assignment_id'];
    try {
        $pdo = getDbConnection();
        // Check if the assignment exists and the user has permission to update it (only the supervisor can submit the presentation announcement)
        $stmt = $pdo->prepare("
            SELECT *
            FROM thesis_assignments
            INNER JOIN thesis_topics
            	ON thesis_topics.id = thesis_assignments.topic_id
            INNER JOIN teacher AS supervisor
            	ON supervisor.teacher_id = thesis_topics.supervisor_id
            WHERE  supervisor.username = :username AND thesis_assignments.thesis_assignment_id = :assignmentId;
        ");
        $stmt->bindParam(":assignmentId", $assignment_id, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$assignment) {
            http_response_code(404);
            echo json_encode(["error" => "Assignment not found"]);
            exit;
        }
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM student_presentation
            WHERE thesis_assignment_id = :th_as_id
        ");
        $stmt->bindParam(":th_as_id",$assignment_id,PDO::PARAM_INT);
        $stmt->execute();
        $has_presentation = $stmt->fetchColumn();
        if($has_presentation ==1){
            // fetch file
            $uploadDir = '../../uploads/thesis_presentations/'.$username.'/assignment'.$assignment_id . '/';
            if(!file_exists($uploadDir)){
                mkdir($uploadDir,0777,true);
            }
            
            $timestamp = time();
            $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
            $fileName = $timestamp . '_' . $randomString . '.pdf';
            $supervisor_announcement_path = $uploadDir . $fileName;
            if (!move_uploaded_file($_FILES['presentation']['tmp_name'], $supervisor_announcement_path)) {
                echo json_encode(['success' => false, 'message' => 'Can not tranfer that file']);
                exit;
            }

            // put pdf path into student_presentation
            $stmt = $pdo->prepare("
                UPDATE student_presentation
                SET supervisor_announcement_presentation_path = :s_pr_path
                WHERE thesis_assignment_id = :th_as_id
            ");
            $stmt->bindParam(':s_pr_path',$supervisor_announcement_path,PDO::PARAM_STR);
            $stmt->bindParam(":th_as_id",$assignment_id,PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount()>0){
                echo json_encode(["success"=>true]);
            }
            else echo json_encode(["success"=>false]);
        }
        else{
            echo json_encode(["success"=>false,"reason"=>"student has noyt uploaded"]);
        }
     }catch(PDOException $e){
        http_response_code(500);
        echo json_encode(["error"=>$e->getMessage()]);
     }
?>