<?php $userType = "teacher"; include '../../includes/auth/illegal_redirection.php'; ?>
<?php
// submit_thesis.php - Handles the submission of a new thesis topic

// Set header to return JSON
header('Content-Type: application/json');


session_start();
$username = $_SESSION['username'];



// Get form data
$title = $_POST['title'];
$description = isset($_POST['description']) ? $_POST['description'] : null;

// Handle file upload
$pdf_file_path = null;
if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
    // Ensure it's a PDF file
    $fileType = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
    if ($fileType != "pdf") {
        echo json_encode(['success' => false, 'message' => 'Μόνο αρχεία PDF επιτρέπονται.']);
        exit;
    }

    // Create upload directory if it doesn't exist
    $uploadDir = '../../uploads/thesis_topics/' . $username . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate a unique filename using timestamp, random string and username
    $timestamp = time();
    $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
    $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $title));

    $fileName = $timestamp . '_' . $randomString . '_' . $cleanTitle . '.pdf';
    $pdf_file_path = $uploadDir . $fileName;

    // Move the uploaded file
    if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdf_file_path)) {
        echo json_encode(['success' => false, 'message' => 'Can not tranfer that file']);
        exit;
    }
}
require_once '../../config/db_config.php';
try {
    // Create a PDO instance
    $pdo = getDbConnection();

    // Get the supervisor ID for the logged-in teacher
    $supervisorStmt = $pdo->prepare("
        SELECT teacher_id FROM teacher WHERE username = :username
    ");
    $supervisorStmt->bindParam(':username', $username, PDO::PARAM_STR);
    $supervisorStmt->execute();
    $supervisor_id = $supervisorStmt->fetchColumn();

    // $supervisor_id = $supervisor['teacher_id'];

    // Insert the new thesis topic
    $stmt = $pdo->prepare("
        INSERT INTO thesis_topics (
            title,
            description,
            pdf_file_path,
            supervisor_id
        ) VALUES (
            :title,
            :description,
            :pdf_file_path,
            :supervisor_id
        )
    ");

    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':pdf_file_path', $pdf_file_path, PDO::PARAM_STR);
    $stmt->bindParam(':supervisor_id', $supervisor_id, PDO::PARAM_INT);

    $stmt->execute();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Το θέμα διπλωματικής υποβλήθηκε με επιτυχία!'
    ]);
} catch (PDOException $e) {
    // If there's a database error, delete the uploaded file if it exists
    if ($pdf_file_path && file_exists($pdf_file_path)) {
        unlink($pdf_file_path);
    }

    http_response_code(500); // Server error
    echo json_encode(['success' => false, 'message' => 'DB error']);

    // Log the actual error but don't expose it to users
    error_log('Database error in submit_thesis.php: ' . $e->getMessage());
}
