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
$assemblyNumber = $_POST['assemblyNumber'] ?? null;
$assemblyYear = $_POST['assemblyYear'] ?? null;
if (!$assemblyNumber || !$assemblyYear) {
    echo json_encode(["success" => false, "message" => "Assembly number and year are required."]);
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

    // Insert the assignment into the database
    $stmt = $pdo->prepare("
        INSERT INTO assembly_decisions (thesis_assignment_id, assembly_number, assembly_year, secretary_id, assembly_decision)
        VALUES (:thesis_assignment_id, :assembly_number, :assembly_year, :secretary_id, 'ΑΚΥΡΩΣΗ ΘΕΜΑΤΟΣ ΛΟΓΩ ΦΟΙΤΗΤΗ')
    ");
    $stmt->bindParam(":thesis_assignment_id", $thesis_assignment_id, PDO::PARAM_INT);
    $stmt->bindParam(":assembly_number", $assemblyNumber, PDO::PARAM_INT);
    $stmt->bindParam(":assembly_year", $assemblyYear, PDO::PARAM_INT);
    $stmt->bindParam(":secretary_id", $s_id, PDO::PARAM_INT);
    $stmt->execute();

    // Update thesis logs
    $stmt = $pdo->prepare("
        INSERT INTO thesis_logs (thesis_assignment_id, change_log)
        VALUES (:thesis_assignment_id, 'ΑΚΥΡΩΣΗ ΘΕΜΑΤΟΣ ΛΟΓΩ ΦΟΙΤΗΤΗ')");
    $stmt->bindParam(":thesis_assignment_id", $thesis_assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    // Update the thesis assignment status to 'withdrawn'
    $stmt = $pdo->prepare("
        UPDATE thesis_assignments
        SET status = 'Cancelled'
        WHERE thesis_assignment_id = :thesis_assignment_id
    ");
    $stmt->bindParam(":thesis_assignment_id", $thesis_assignment_id, PDO::PARAM_INT);
    $stmt->execute();
    echo json_encode(['success' => true, 'message' => 'Thesis assignment withdrawn successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>