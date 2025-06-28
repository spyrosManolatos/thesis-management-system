<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php';?>
<?php
    require_once '../../config/db_config.php';
    session_start();
    $assignmentId = $_GET['assignmentId'];
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
        SELECT 
            teacher.name as teacher_name,
            DATE_FORMAT(invitation_date, '%d-%m-%Y') as invitation_date,
            DATE_FORMAT(response_date, '%d-%m-%Y') as response_date,
            invitations.status as answer

        FROM
            committee_invitations as invitations
        INNER JOIN
            teacher ON teacher.teacher_id = invitations.professor_id
        WHERE
            thesis_assignment_id = :assignId;
        "
        );
        $stmt->bindParam(":assignId",$assignmentId,PDO::PARAM_INT);
        $stmt->execute();
        //mapper for answer status
        $statusMapper = [
            'invited' => 'Σε Αναμονή',
            'accepted' => 'Αποδοχή',
            'rejected' => 'Απόρριψη'
        ];
        $invitations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Map the status to human-readable format
        foreach ($invitations as &$invitation) {
            $invitation['answer'] = $statusMapper[$invitation['answer']] ?? 'Άγνωστο';
        }
        echo json_encode($invitations, JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error"=>"database_error:". $e->getMessage()]);
    }

?>