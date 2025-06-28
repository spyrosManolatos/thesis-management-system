<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php'; ?>
<?php
require_once '../../config/db_config.php';
session_start();
$status = $_GET["status"];
$is_supervisor = $_GET['isSupervisor'];

$user_username = $_SESSION["username"];
retrieveFromThesisAssignments($status, $is_supervisor, $user_username);
function retrieveFromThesisAssignments($status, $is_supervisor, $user_username)
{
    try {
        $pdo = getDbConnection();
        if ($status == "all" && $is_supervisor == "supervisor") {
            // if status is under_examination or complete we should put the presentation path and the nemertis link
            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                    student.name,
                    thesis_topics.title,
                    thesis_assignments.status,
                    thesis_assignments.thesis_assignment_id,
                    thesis_nemertis_links.nemertis_link as nemertis_link,
                    thesis_material_student.thesis_draft_text_pdf_path as thesis_student_text,
                    student_presentation.supervisor_announcement_presentation_path as supervisor_presentation_announcement,
                    student_presentation.examination_protocol_path as exam_protocol,
                    committee_members.is_supervisor as is_supervisor
                FROM 
                    thesis_assignments
                INNER JOIN
                    thesis_topics on thesis_topics.id = thesis_assignments.topic_id
                INNER JOIN
                    teacher as supervisor on thesis_topics.supervisor_id = supervisor.teacher_id
                INNER JOIN
                    student on student.student_id = thesis_assignments.student_id
                LEFT JOIN
                    thesis_nemertis_links on thesis_nemertis_links.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                LEFT JOIN
                    committee on committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                LEFT JOIN
                    committee_members on committee.com_id = committee_members.com_id
                LEFT JOIN
                    thesis_material_student on thesis_assignments.thesis_assignment_id =thesis_material_student.thesis_assignment_id
                LEFT JOIN
                    student_presentation on student_presentation.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                LEFT JOIN 
                    teacher on teacher.teacher_id = thesis_topics.supervisor_id
                WHERE 
                    supervisor.username = :supervisor_username and (committee_members.is_supervisor = true OR committee_members.is_supervisor is NULL);
                ");
            $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);
        } else if ($status != "all" && $is_supervisor == "supervisor") {
            if($status =='Cancelled'){
                $stmt = $pdo->prepare("
                    SELECT 
                        DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                        student.name,
                        thesis_topics.title,
                        thesis_assignments.status,
                        thesis_assignments.thesis_assignment_id
                    FROM 
                        thesis_assignments
                    INNER JOIN
                        thesis_topics on thesis_topics.id = thesis_assignments.topic_id
                    INNER JOIN
                        teacher as supervisor on thesis_topics.supervisor_id = supervisor.teacher_id
                    INNER JOIN
                        student on student.student_id = thesis_assignments.student_id
                    WHERE 
                        supervisor.username = :supervisor_username AND status = 'cancelled';
                ");
                $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);
            }
            if($status== 'Pending'){
                $stmt = $pdo->prepare("
                    SELECT 
                        DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                        student.name,
                        thesis_topics.title,
                        thesis_assignments.status,
                        thesis_assignments.thesis_assignment_id
                    FROM 
                        thesis_assignments
                    INNER JOIN
                        thesis_topics on thesis_topics.id = thesis_assignments.topic_id
                    INNER JOIN
                        teacher as supervisor on thesis_topics.supervisor_id = supervisor.teacher_id
                    INNER JOIN
                        student on student.student_id = thesis_assignments.student_id
                    WHERE 
                        supervisor.username = :supervisor_username AND status = 'pending';
                ");
                $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);
            }
            if ($status == 'Under Examination') {
                // if status is under_examination or complete we should put the presentation path and the nemertis link
                $stmt = $pdo->prepare("
                        SELECT 
                            DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                            student.name,
                            thesis_topics.title,
                            thesis_assignments.status,
                            thesis_assignments.thesis_assignment_id,
                            thesis_material_student.thesis_draft_text_pdf_path as thesis_student_text,
                            student_presentation.supervisor_announcement_presentation_path as supervisor_presentation_announcement,
                            committee_members.is_supervisor as is_supervisor
                        FROM 
                            thesis_assignments
                        INNER JOIN
                            thesis_topics on thesis_topics.id = thesis_assignments.topic_id
                        INNER JOIN
                            teacher as supervisor on thesis_topics.supervisor_id = supervisor.teacher_id
                        INNER JOIN
                            student on student.student_id = thesis_assignments.student_id
                        INNER JOIN
                            committee on committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                        INNER JOIN
                            committee_members on committee.com_id = committee_members.com_id
                        LEFT JOIN
                            student_presentation on student_presentation.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                        INNER JOIN
                            teacher on teacher.teacher_id = committee_members.teacher_id
                        LEFT JOIN
                            thesis_material_student on thesis_assignments.thesis_assignment_id =thesis_material_student.thesis_assignment_id
                        WHERE 
                            supervisor.username =:supervisor_username and (status =:my_status OR status =:under_pending_status) AND committee_members.is_supervisor = true;
                        GROUP BY thesis_assignments.thesis_assignment_id
                    ");
                $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);
                $stmt->bindParam(':my_status', $status, PDO::PARAM_STR);
                $under_grading_status = 'Under Grading';
                $stmt->bindParam(':under_pending_status', $under_grading_status, PDO::PARAM_STR);
            }
            if ($status == 'Completed') {
                $stmt = $pdo->prepare("
                    SELECT
                        DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                        student.name,
                        thesis_topics.title,
                        thesis_assignments.status,
                        thesis_assignments.thesis_assignment_id,
                        student_presentation.examination_protocol_path as exam_protocol,
                        thesis_nemertis_links.nemertis_link as nemertis_link,
                        committee_members.is_supervisor as is_supervisor
                    FROM thesis_assignments
                    INNER JOIN thesis_topics
                    ON thesis_topics.id = thesis_assignments.topic_id
                    INNER JOIN teacher as supervisor
                    ON supervisor.teacher_id = thesis_topics.supervisor_id
                    INNER JOIN student_presentation
                    ON student_presentation.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    INNER JOIN committee
                    ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    INNER JOIN committee_members
                    ON committee_members.com_id = committee.com_id
                    INNER JOIN teacher
                    ON teacher.teacher_id = committee_members.teacher_id
                    INNER JOIN student
                    ON student.student_id=thesis_assignments.student_id
                    INNER JOIN thesis_nemertis_links
                    ON thesis_nemertis_links.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    WHERE supervisor.username = :t_username and status='Completed' and committee_members.is_supervisor = true
                    GROUP BY thesis_assignments.thesis_assignment_id;
                ");
                $stmt->bindParam(':t_username', $user_username, PDO::PARAM_STR);
            } 
            if($status == 'Active'){
                $stmt = $pdo->prepare("
                    SELECT 
                        DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                        student.name,
                        thesis_topics.title,
                        thesis_assignments.status,
                        committee_members.is_supervisor as is_supervisor,
                        thesis_assignments.thesis_assignment_id
                    FROM 
                        thesis_assignments
                    INNER JOIN
                        thesis_topics on thesis_topics.id = thesis_assignments.topic_id
                    INNER JOIN
                        teacher as supervisor on thesis_topics.supervisor_id = supervisor.teacher_id
                    INNER JOIN
                        committee on committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    INNER JOIN
                        committee_members on committee.com_id = committee_members.com_id
                    INNER JOIN
                        teacher on teacher.teacher_id = committee_members.teacher_id
                    INNER JOIN
                        student on student.student_id = thesis_assignments.student_id
                    WHERE 
                        supervisor.username = :supervisor_username AND committee_members.is_supervisor is TRUE AND thesis_assignments.status = :status;
                ");
                $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            }
        } else if ($is_supervisor == 'committee_member' && $status == 'all') {
            $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                    student.name,
                    thesis_topics.title,
                    thesis_assignments.status,
                    thesis_assignments.thesis_assignment_id,
                    thesis_nemertis_links.nemertis_link as nemertis_link,
                    student_presentation.examination_protocol_path as exam_protocol,
                    committee_members.is_supervisor as is_supervisor,
                    thesis_material_student.thesis_draft_text_pdf_path as thesis_student_text,
                    student_presentation.supervisor_announcement_presentation_path as supervisor_presentation_announcement
                FROM 
                    committee_members
                INNER JOIN
                    teacher on committee_members.teacher_id = teacher.teacher_id
                INNER JOIN
                    committee on committee.com_id = committee_members.com_id
                INNER JOIN
                    thesis_assignments on thesis_assignments.thesis_assignment_id = committee.thesis_assignment_id
                INNER JOIN
                    student on student.student_id = thesis_assignments.student_id
                LEFT JOIN
                    thesis_material_student on thesis_assignments.thesis_assignment_id =thesis_material_student.thesis_assignment_id 
                INNER JOIN
                    thesis_topics on thesis_topics.id = thesis_assignments.topic_id
                LEFT JOIN
                    student_presentation on student_presentation.thesis_assignment_id = thesis_assignments.thesis_assignment_id 
                LEFT JOIN
                    thesis_nemertis_links on thesis_nemertis_links.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                WHERE 
                    teacher.username = :supervisor_username AND committee_members.is_supervisor = false
                GROUP BY thesis_assignments.thesis_assignment_id;
                ");
            $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);
        } else if ($is_supervisor == 'committee_member' && $status != 'all') {
            if ($status == 'Under Examination') {
                $stmt = $pdo->prepare("
                    SELECT 
                        DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                        student.name,
                        thesis_topics.title,
                        thesis_assignments.status,
                        thesis_assignments.thesis_assignment_id,
                        quality_completeness,
                        readable_thesis,
                        targets_fulfiled,
                        time_satisfied,
                        thesis_material_student.thesis_draft_text_pdf_path as thesis_student_text,
                        committee_members.is_supervisor as is_supervisor,
                        student_presentation.supervisor_announcement_presentation_path as supervisor_presentation_announcement
                    FROM 
                        committee_members
                    LEFT JOIN
                        marks on marks.mark_id = committee_members.mark_id
                    INNER JOIN
                        teacher on committee_members.teacher_id = teacher.teacher_id
                    INNER JOIN
                        committee on committee.com_id = committee_members.com_id
                    INNER JOIN
                        thesis_assignments on thesis_assignments.thesis_assignment_id = committee.thesis_assignment_id
                    LEFT JOIN
                        thesis_material_student on thesis_assignments.thesis_assignment_id =thesis_material_student.thesis_assignment_id
                    INNER JOIN
                        student on student.student_id = thesis_assignments.student_id 
                    INNER JOIN
                        thesis_topics on thesis_topics.id = thesis_assignments.topic_id 
                    LEFT JOIN
                        student_presentation on student_presentation.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    WHERE 
                        teacher.username = :supervisor_username AND (thesis_assignments.status = :status OR thesis_assignments.status = :under_grading_status) AND committee_members.is_supervisor = :false_st
                    
                    ");
                $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);

                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
                $under_grading_status = 'Under Grading';
                $stmt->bindParam(':under_grading_status', $under_grading_status, PDO::PARAM_STR);
                $false_st = 0;
                $stmt->bindParam(':false_st', $false_st, PDO::PARAM_INT);
            }
            elseif ($status == 'Completed') {
                $stmt = $pdo->prepare("
                    SELECT
                        DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                        student.name,
                        thesis_topics.title,
                        thesis_assignments.status,
                        thesis_assignments.thesis_assignment_id,
                        student_presentation.examination_protocol_path as exam_protocol,
                        thesis_nemertis_links.nemertis_link as nemertis_link,
                        committee_members.is_supervisor as is_supervisor
                    FROM thesis_assignments
                    INNER JOIN thesis_topics
                    ON thesis_topics.id = thesis_assignments.topic_id
                    INNER JOIN teacher as supervisor
                    ON supervisor.teacher_id = thesis_topics.supervisor_id
                    INNER JOIN student_presentation
                    ON student_presentation.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    INNER JOIN committee
                    ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    INNER JOIN committee_members
                    ON committee_members.com_id = committee.com_id
                    INNER JOIN teacher
                    ON teacher.teacher_id = committee_members.teacher_id
                    INNER JOIN student
                    ON student.student_id=thesis_assignments.student_id
                    LEFT JOIN thesis_nemertis_links
                    ON thesis_nemertis_links.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                    WHERE teacher.username = :t_username AND thesis_assignments.status='Completed' AND committee_members.is_supervisor = false;
                    ");
                $stmt->bindParam(':t_username', $user_username, PDO::PARAM_STR);
            } else {
                $stmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(assignment_date, '%d/%m/%Y') as assignment_date,
                    student.name,
                    thesis_topics.title,
                    thesis_assignments.status,
                    thesis_assignments.thesis_assignment_id,
                    thesis_material_student.thesis_draft_text_pdf_path as thesis_student_text
                FROM 
                    committee_members
                INNER JOIN
                    teacher on committee_members.teacher_id = teacher.teacher_id
                INNER JOIN
                    committee on committee.com_id = committee_members.com_id
                INNER JOIN
                    thesis_assignments on thesis_assignments.thesis_assignment_id = committee.thesis_assignment_id
                LEFT JOIN
                    thesis_material_student on thesis_assignments.thesis_assignment_id =thesis_material_student.thesis_assignment_id
                INNER JOIN
                    student on student.student_id = thesis_assignments.student_id 
                INNER JOIN
                    thesis_topics on thesis_topics.id = thesis_assignments.topic_id 
                WHERE 
                    teacher.username = :supervisor_username AND thesis_assignments.status = :status AND committee_members.is_supervisor = :false_st
                ");
                $stmt->bindParam(':supervisor_username', $user_username, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
                $false_st = false;
                $stmt->bindParam(':false_st', $false_st, PDO::PARAM_BOOL);
            }
        }

        $stmt->execute();
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($assignments);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error:' . $e->getMessage()]);
    }
}
?>