<?php $userType="secrertary"; include "../../includes/auth/illegal_redirection.php"?>
<?php
header('Content-Type: application/json');

include '../../config/database.php'; // Σύνδεση με τη βάση δεδομένων
//require_once 'database.php'; // Συμπερίληψη αρχείου με συναρτήσεις

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['jsonFile'])) {
        $file = $_FILES['jsonFile']['tmp_name'];
        $data = file_get_contents($file);
        $json = json_decode($data, true);

        if ($json) {
            try {
                $conn = getDatabaseConnection(); // Λειτουργία για σύνδεση με τη βάση δεδομένων
                // Εισαγωγή φοιτητών
                if (isset($json['students'])) {
                    foreach ($json['students'] as $student) {
                        $stmt = $conn->prepare("
                            INSERT INTO student (name, area, email, mobile_phone, username)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param("sssis", 
                            $student['name'], 
                            $student['area'], 
                            $student['email'], 
                            $student['mobile_phone'], 
                            $student['username']
                        );
                        $stmt->execute();
                    }
                }

                // Εισαγωγή καθηγητών
                if (isset($json['teachers'])) {
                    foreach ($json['teachers'] as $teacher) {
                        $stmt = $conn->prepare("
                            INSERT INTO teacher (name, username)
                            VALUES (?, ?)
                        ");
                        $stmt->bind_param("ss", 
                            $teacher['name'], 
                            $teacher['username']
                        );
                        $stmt->execute();
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Τα δεδομένα εισήχθησαν με επιτυχία!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Σφάλμα κατά την εισαγωγή δεδομένων: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Το αρχείο JSON δεν είναι έγκυρο.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Δεν επιλέχθηκε αρχείο.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρη μέθοδος αιτήματος.']);
}
?>