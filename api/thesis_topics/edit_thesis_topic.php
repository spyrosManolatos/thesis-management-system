<?php $userType = "teacher"; include '../../includes/auth/illegal_redirection.php'; ?>
<?php
    require_once '../../config/db_config.php';
    session_start();
    $teacherUsername = $_SESSION['username'];
    $topic_id = $_POST['id'];
    
    try {
        $pdo = getDbConnection();
        // Check if the topic exists and the user has permission to update it(only the supervisor can update)
        $stmt = $pdo->prepare("
            SELECT id
            FROM thesis_topics
            INNER JOIN teacher ON supervisor_id = teacher_id
            WHERE id = :topic_id AND teacher.username = :teacher_username
        ");
        $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);   
        $stmt->bindParam(':teacher_username', $teacherUsername, PDO::PARAM_STR);
        $stmt->execute();
        $topic = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$topic) {
            http_response_code(404);
            echo json_encode(["error" => "Topic not found or you do not have permission to edit it."]);
            exit;
        }   
        if(!isset($_FILES['pdf_file']['name']) || $_FILES['pdf_file']['name'] == ''){
            $stmt = $pdo->prepare("
            UPDATE thesis_topics
            SET title = :topic_title,
                description = :topic_description
            WHERE id = :thesis_topic_id ");
            $stmt->bindParam(':topic_title',$_POST['title'],PDO::PARAM_STR);
            $stmt->bindParam(':topic_description',$_POST['description'],PDO::PARAM_STR);
            $stmt->bindParam(':thesis_topic_id', $topic_id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['success'=>true,'message'=>"Successfull update for thesis_assignment:".$topic_id]);

        }
        else{
            $stmt = $pdo->prepare("SELECT pdf_file_path FROM thesis_topics WHERE id = :thesis_topic_id");
            $stmt->bindParam(':thesis_topic_id', $topic_id, PDO::PARAM_INT);
            $stmt->execute();
            $oldPdfPath = $stmt->fetchColumn();

            if ($oldPdfPath && file_exists($oldPdfPath)) {
                unlink($oldPdfPath);
            }
            $stmt = $pdo->prepare("SELECT teacher_id FROM teacher WHERE username = :t_username");
            $stmt->bindParam(':t_username',$teacherUsername, PDO::PARAM_STR);
            $stmt->execute();
            $teacher_id = $stmt->fetchColumn();
            $randomString = bin2hex(random_bytes(8));
            $newPdfPath = "../../uploads/thesis_topics/teacher$teacher_id/" . $randomString . "_" . basename($_FILES['pdf_file']['name']);
            // $newPdfPath = "uploads/pdf/teacher$teacher_id/pdf/" . basename($_FILES['pdf_file']['name']);
            move_uploaded_file($_FILES['pdf_file']['tmp_name'], $newPdfPath);

            $stmt = $pdo->prepare("
                UPDATE thesis_topics
                SET title = :topic_title,
                    description = :topic_description,
                    pdf_file_path = :pdf_path
                WHERE id = :thesis_topic_id
            ");
            $stmt->bindParam(':topic_title', $_POST['title'], PDO::PARAM_STR);
            $stmt->bindParam(':topic_description', $_POST['description'], PDO::PARAM_STR);
            $stmt->bindParam(':pdf_path', $newPdfPath, PDO::PARAM_STR);
            $stmt->bindParam(':thesis_topic_id', $topic_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => "Successfully updated thesis_assignment(withpdf): " . $topic_id]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success"=>false,"error"=>"db error: ".$e->getMessage()]);
    }

?>