<?php $userType = "teacher"; include '../../includes/auth/illegal_redirection.php'; ?>
<?php
    // fetch students lsit when a teacher wants to take a student list so he can pick the things he wants
    require_once '../../config/db_config.php';
    try{
        $pdo = getDbConnection();
        // in future we are gonna change the students that are availble (they have not assigned thesis)
        $stmt = $pdo->prepare("
            SELECT name
            FROM student
            LEFT JOIN thesis_assignments
            ON thesis_assignments.student_id = student.student_id
            GROUP BY student.student_id
            HAVING COUNT(CASE WHEN thesis_assignments.status != 'Cancelled' THEN 1 END) = 0;
        ");
        $stmt->execute();
        $student_list =$stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($student_list);
    }catch(PDOException $e){
        http_response_code(500);
        echo json_encode(["error"=>"Database error fetching students in upload thesis "]);
            // Log the actual error but don't expose it to users
        error_log('Database error in get_thesis_list.php: ' . $e->getMessage());
    }


?>