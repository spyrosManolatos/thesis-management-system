<?php $userType = "student";
include ("../../includes/auth/illegal_redirection.php");?>
<?php
session_start();
$date = $_POST['remote_exam_date'];
$time = $_POST['remote_exam_time'];
$location = $_POST['remote_exam_link'];

require_once "../../config/db_config.php";

$connect = getDbConnection();
$username = $_SESSION['username'];
try{
    $meeting_hour = $date . " " . $time;
    $query = $connect -> prepare("SELECT thesis_assignment_id FROM `thesis_assignments` INNER JOIN student ON student.student_id = thesis_assignments.student_id WHERE username = :username AND status!='Cancelled';");
    $query -> bindParam(':username', $username);
    $query -> execute();
    $result = $query -> fetchColumn();
    $assignment_id = $result;

    $query = $connect -> prepare("SELECT COUNT(*) FROM `student_presentation` WHERE thesis_assignment_id = :assignment_id");
    $query -> bindParam(':assignment_id', $assignment_id);
    $query -> execute();
    $result = $query -> fetchColumn();
    if($result >= 1){
        http_response_code(400);
        echo json_encode(["success"=> false,"message"=> "έχετε ήδη υποβάλλει αίτηση για παρουσίαση"]);
    } else {
        $query = $connect ->prepare("INSERT INTO student_presentation (meeting_hour,meeting_room_or_link,physical_presense,thesis_assignment_id) VALUES(:time,:location,0,:assignment_id)");
        $query -> bindParam(':time', $meeting_hour);
        $query -> bindParam(':location', $location);
        $query -> bindParam(':assignment_id', $assignment_id);
        $query -> execute();
        echo json_encode(["success"=> true,"message"=> "Η αίτηση παρουσίασης υποβλήθηκε με επιτυχία"]);
    }
}catch (PDOException $e){
    http_response_code(500);
    echo json_encode(array("message"=>$e->getMessage()));
}