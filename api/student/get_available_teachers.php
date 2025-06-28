<?php $userType = 'student';
include '../../includes/auth/illegal_redirection.php'?>
<?php
    require_once '../../config/db_config.php';
    session_start();
    header('Content-Type: application/json; charset=utf-8');
    $username = $_SESSION['username'];
    try {
        $conn = getDBConnection();
        // get thesis assignment id for the student
        $stmt = $conn->prepare("
            SELECT thesis_assignments.thesis_assignment_id FROM thesis_assignments
            INNER JOIN student ON student.student_id = thesis_assignments.student_id
            WHERE student.username = :username AND thesis_assignments.status!='Cancelled';");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $thesis_assignment_id = $stmt->fetchColumn();
        // exclude the supervisor of the thesis topic(which is the supervisor of the student)
        $stmt = $conn->prepare("
            SELECT teacher.teacher_id FROM thesis_assignments
            INNER JOIN student
                ON student.student_id=thesis_assignments.student_id
            INNER JOIN thesis_topics
                ON thesis_topics.id=thesis_assignments.topic_id
            INNER JOIN teacher
                ON teacher.teacher_id=thesis_topics.supervisor_id
            WHERE 
                student.username = :username;");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $conn->prepare("
            SELECT 
                teacher.teacher_id as teacher_id,
                teacher.name
            FROM 
                teacher
            WHERE teacher_id not in(
                SELECT professor_id FROM committee_invitations
                WHERE thesis_assignment_id = :thesis_assignment_id AND (status = 'invited' OR status = 'accepted')
            ) AND teacher.teacher_id != :supervisor_id
            ORDER BY teacher.name;
        ");
        $stmt->bindParam(':thesis_assignment_id', $thesis_assignment_id, PDO::PARAM_INT);
        $stmt->bindParam(':supervisor_id', $result['teacher_id'], PDO::PARAM_INT);
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($teachers, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Internal Server Error: " . $e->getMessage()]);
    }

?>