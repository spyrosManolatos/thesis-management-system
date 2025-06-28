<?php
require_once '../../config/db_config.php';
// $startDate = $_GET['startDate'];
// $endDate = $_GET['endDate'];
try {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("
            SELECT
            thesis_assignments.thesis_assignment_id,
            physical_presense,
            meeting_room_or_link,
            DATE_FORMAT(meeting_hour, '%d/%m/%Y %H:%i') AS meeting_hour,
            student.name AS student_name,
            student.username,
            thesis_topics.title
            FROM student_presentation
            INNER JOIN thesis_assignments
            ON student_presentation.thesis_assignment_id=thesis_assignments.thesis_assignment_id
            INNER JOIN student
            ON student.student_id = thesis_assignments.student_id
            INNER JOIN thesis_topics
            ON thesis_topics.id = thesis_assignments.topic_id
            INNER JOIN committee
            ON thesis_assignments.thesis_assignment_id=committee.thesis_assignment_id
            INNER JOIN committee_members
            ON committee_members.com_id=committee.com_id
            INNER JOIN teacher
            ON teacher.teacher_id = committee_members.teacher_id
            GROUP BY student_presentation.thesis_assignment_id");

    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($announcements);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["Db error" => $e->getMessage()]);
}
