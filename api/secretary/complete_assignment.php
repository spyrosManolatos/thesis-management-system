<?php $userType="secrertary"; include "../../includes/auth/illegal_redirection.php"?>
<?php
session_start();
$username = $_SESSION['username'];
// Get form data
$thesis_assignment_id = $_GET['thesis_id'] ?? null;
if (!$thesis_assignment_id) {
    echo json_encode(["success" => false, "message" => "Thesis assignment ID is required."]);
    exit;
}
require_once '../../config/db_config.php';
try {
    // Create a PDO instance
    $pdo = getDbConnection();
    
    // Get the secretary ID from the session username
    $stmt = $pdo->prepare("
        SELECT secrertary_id
        FROM secrertary
        WHERE secrertary.username = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute(); 
    $s_id = $stmt->fetchColumn();
    if (!$s_id) {
        echo json_encode(['success' => false, 'message' => 'Secretary not found.']);
        exit;
    }
    
    // Check if we have nemertis link to the thesis assignment
    $stmt= $pdo->prepare("
        SELECT COUNT(*) AS count
        FROM thesis_nemertis_links
        WHERE thesis_assignment_id = :thesis_assignment_id");
    $stmt->bindParam(':thesis_assignment_id', $thesis_assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        echo json_encode(['success' => false, 'message' => 'Ο φοιτητής δεν έχει εισάγει σύνδεσμο Nemertis.']);
        exit;
    }
    // Check if it is marked
    $stmt = $pdo->prepare("
        SELECT avg_mark
        FROM committee
        WHERE thesis_assignment_id = :thesis_assignment_id");
    $stmt->bindParam(':thesis_assignment_id', $thesis_assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    $avg_mark = $stmt->fetchColumn();
    if ($avg_mark === null) {
        echo json_encode(['success' => false, 'message' => 'Η διπλωματική δεν έχει βαθμολογηθεί.']);
        exit;
    }
    if($avg_mark < 5){
        echo json_encode(['success' => false, 'message' => 'Η διπλωματική δεν έχει προβιβάσιμο βαθμό.']);
        exit;
    }
    

    // Mark the thesis assignment as completed
    $stmt = $pdo->prepare("
        UPDATE thesis_assignments
        SET status = 'Completed'
        WHERE thesis_assignment_id = :thesis_assignment_id");
    $stmt->bindParam(":thesis_assignment_id", $thesis_assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    // Update thesis logs
    $stmt = $pdo->prepare("
        INSERT INTO thesis_logs (thesis_assignment_id, change_log)
        VALUES (:thesis_assignment_id, 'ΕΠΙΣΗΜΗ ΟΛΟΚΛΗΡΩΣΗ ΘΕΜΑΤΟΣ')");
    $stmt->bindParam(":thesis_assignment_id", $thesis_assignment_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Η διπλωματική ολοκληρώθηκε επιτυχώς.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
