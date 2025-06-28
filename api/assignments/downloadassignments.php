<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php';?>
<?php
    require_once '../../config/db_config.php';
    session_start();
    $username = $_SESSION['username'];
    $status = $_GET['status'];
    $file_format = $_GET['file_format'];

    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT teacher_id FROM teacher WHERE username = :usr");
        $stmt->bindParam(':usr', $username, PDO::PARAM_STR);
        $stmt->execute();
        $t_id = $stmt->fetchColumn();
        $stmt = $pdo->prepare("
            SELECT 
            thesis_assignments.thesis_assignment_id,
            DATE_FORMAT(thesis_assignments.assignment_date, '%d-%m-%Y') AS assignment_date,
            thesis_assignments.status,
            supervisor.name AS supervisor_name,
            student.name AS student_name,
            thesis_topics.title AS thesis_title,
            committee.avg_mark AS average_grade,
            GROUP_CONCAT(DISTINCT CONCAT(teacher.name, ' (', teacher.username, ')') SEPARATOR ', ') AS committee_members
            FROM thesis_assignments
            INNER JOIN student ON thesis_assignments.student_id = student.student_id
            INNER JOIN thesis_topics ON thesis_assignments.topic_id = thesis_topics.id
            INNER JOIN committee ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
            INNER JOIN committee_members ON committee_members.com_id = committee.com_id
            INNER JOIN teacher ON committee_members.teacher_id = teacher.teacher_id
            INNER JOIN teacher as supervisor ON thesis_topics.supervisor_id = supervisor.teacher_id    
            WHERE committee.com_id IN (
                SELECT com_id FROM committee_members WHERE teacher_id = :t_id
            )
            AND thesis_assignments.status = :status
            GROUP BY thesis_assignments.thesis_assignment_id;
        
        
        ");
        $stmt->bindParam(':t_id', $t_id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
        $stmt->execute();
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($file_format === 'json'){
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="data.json"');
            echo json_encode($assignments, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        elseif($file_format === 'csv'){
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="data.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Ημερομηνία ανάθεσης', 'Κατάσταση', 'Όνομα Φοιτητή', 'Τίτλος Θέματος', 'Μέσος Βαθμός','Τριμελής Επιτροπή'));
            foreach ($assignments as $assignment) {
            fputcsv($output, $assignment);
            }
            fclose($output);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error"=>"".$e->getMessage()]);
    }
?>