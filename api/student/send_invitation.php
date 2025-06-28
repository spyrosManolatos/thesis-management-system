<?php
$userType = "student";
include "../../includes/auth/illegal_redirection.php";
?>
<?php
session_start();
$committee_member_1 = $_POST['committee_member1'];
$committee_member_2 = $_POST['committee_member2'];
$username = $_SESSION['username'];
require_once '../../config/db_config.php';
try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
            SELECT thesis_assignment_id
            FROM thesis_assignments
            INNER JOIN student ON thesis_assignments.student_id = student.student_id
            WHERE student.username = :username AND thesis_assignments.status != 'Cancelled' 
        ");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $assignment_id = $stmt->fetchColumn();
    $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM thesis_assignments
            INNER JOIN committee_invitations 
            ON thesis_assignments.thesis_assignment_id = committee_invitations.thesis_assignment_id
            WHERE thesis_assignments.thesis_assignment_id = :assignment_id AND committee_invitations.status ='invited';
        ");
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    $count_of_invited = $stmt->fetchColumn();
    $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM committee_invitations
            WHERE (professor_id = :teacher1_id OR professor_id = :teacher2_id)
            AND thesis_assignment_id = :assignment_id AND status !='rejected';
        ");
    $stmt->bindParam(':teacher1_id', $committee_member_1, PDO::PARAM_INT);
    $stmt->bindParam(':teacher2_id', $committee_member_2, PDO::PARAM_INT);
    $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    if ($count_of_invited != 0) {
        http_response_code(400); // Bad request
        echo json_encode(["success" => false, "message" => "έχετε ήδη στείλει πρόσκληση σε μέλος της επιτροπής"]);
        exit;
    } 
    else if( $count != 0) {
        http_response_code(400); // Bad request
        echo json_encode(["success" => false, "message" => "έχετε ήδη στείλει πρόσκληση σε αυτό το μέλος της επιτροπής"]);
        exit;
    }
    else {
        $stmt = $pdo->prepare("
                INSERT INTO committee_invitations
                (professor_id,thesis_assignment_id)
                VALUES(:teacher1_id,:assignment_id);
            
            ");
        $stmt->bindParam(':teacher1_id', $committee_member_1, PDO::PARAM_INT);
        $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $pdo->prepare("
                INSERT INTO committee_invitations
                (professor_id,thesis_assignment_id)
                VALUES(:teacher2_id,:assignment_id);
            
            ");
        $stmt->bindParam(':teacher2_id', $committee_member_2, PDO::PARAM_INT);
        $stmt->bindParam(':assignment_id', $assignment_id, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "έχει σταλθεί πρόσκληση"]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Server error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
