<?php $userType="student"; include "../../includes/auth/illegal_redirection.php"?>
<?php
session_start();
$username = $_SESSION['username'];
// Get form data
$draft_pdf = isset($_FILES['draft_text_file']) ? $_FILES['draft_text_file'] : null;
if(!$draft_pdf || $draft_pdf['error'] != 0) {
    echo json_encode(['success' => false, 'message' => 'No draft file uploaded or file upload error.']);
    exit;
}
require_once '../../config/db_config.php';
try {
    // Create a PDO instance
    $pdo = getDbConnection();

    // Get the thesis assignment id for the logged-in student
    $studentStmt = $pdo->prepare("
        SELECT thesis_assignment_id
        FROM student 
        INNER JOIN thesis_assignments
        ON thesis_assignments.student_id = student.student_id
        WHERE student.username = :username AND status != 'Cancelled'");
    $studentStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $studentStmt->execute();
    $assignment_id = $studentStmt->fetchColumn();
    if (!$assignment_id) {
        echo json_encode(['success' => false, 'message' => 'No thesis assignment found for this student.']);
        exit;
    }
    // See if the student has already submitted a draft
    $draftCheckStmt = $pdo->prepare("
        SELECT thesis_draft_text_pdf_path 
        FROM thesis_material_student 
        WHERE thesis_assignment_id = :thesis_assignment_id
    ");
    $draftCheckStmt->bindParam(':thesis_assignment_id', $assignment_id, PDO::PARAM_INT);
    $draftCheckStmt->execute();
    $existingDraft = $draftCheckStmt->fetchColumn();
    if ($existingDraft) {
        echo json_encode(['success' => false, 'message' => 'Έχετε ήδη υποβάλει ένα προσχέδιο για αυτήν την εργασία.']);
        exit;
    }
    // Handle file upload
    $pdf_file_path = null;
    if ($draft_pdf && $draft_pdf['error'] == 0) {
        // Ensure it's a PDF file
        $fileType = strtolower(pathinfo($draft_pdf['name'], PATHINFO_EXTENSION));
        if ($fileType != "pdf") {
            echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed.']);
            exit;
        }

        // Create upload directory if it doesn't exist
        $uploadDir = '../../uploads/student_material/' . $username . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a unique filename using timestamp and random string
        $timestamp = time();
        $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', 'draft'));
        
        $fileName = $timestamp . '_' . $randomString . '_' . $cleanTitle . '.pdf';
        $pdf_file_path = $uploadDir . $fileName;
        // Move the uploaded file
        if (!move_uploaded_file($draft_pdf['tmp_name'], $pdf_file_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to transfer the file']);
            exit;
        }
    }

    // Insert draft submission into database
    $stmt = $pdo->prepare("
        INSERT INTO thesis_material_student (thesis_assignment_id, thesis_draft_text_pdf_path)
        VALUES (:thesis_assignment_id, :draft_pdf_path)
    ");
    $stmt->bindParam(':thesis_assignment_id', $assignment_id, PDO::PARAM_INT);
    $stmt->bindParam(':draft_pdf_path', $pdf_file_path, PDO::PARAM_STR);
    $stmt->execute();
    $draft_id = $pdo->lastInsertId();
    if ($draft_id) {
        echo json_encode(['success' => true, 'message' => 'Μεγάλη Επιτυχία', 'draft_id' => $draft_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit draft']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>