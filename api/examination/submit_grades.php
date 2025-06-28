<?php $userType = 'teacher'; include '../../includes/auth/illegal_redirection.php';?>
<?php
    require_once '../../config/db_config.php';
    $username = $_SESSION['username'];
    $quality_grade= $_POST['quality_grade'];
    $quality_completeness_grade = $_POST['quality_completeness_grade'];
    $readable_thesis_grade = $_POST['readable_thesis_grade'];
    $assignment_id = $_GET['assignment_id'];

    $time_mark = 10;
    try {
        $pdo = getDbConnection();
        // Check if the assignment exists and the user has permission to view it
        $stmt = $pdo->prepare('
       SELECT thesis_assignments.thesis_assignment_id
            FROM thesis_assignments
            INNER JOIN thesis_topics
                ON thesis_assignments.topic_id = thesis_topics.id
            LEFT JOIN teacher AS supervisor
                ON supervisor.teacher_id = thesis_topics.supervisor_id
            LEFT JOIN committee
                ON committee.thesis_assignment_id = thesis_assignments.thesis_assignment_id
            LEFT JOIN committee_members
                ON committee_members.com_id = committee.com_id
            LEFT JOIN teacher AS committee_member
                ON committee_member.teacher_id = committee_members.teacher_id
            INNER JOIN assembly_decisions
                ON assembly_decisions.thesis_assignment_id = thesis_assignments.thesis_assignment_id
            WHERE thesis_assignments.thesis_assignment_id = :assignmentId AND (committee_member.username = :username OR supervisor.username = :username) AND assembly_decisions.assembly_decision = "ΕΠΙΣΗΜΗ ΑΝΑΘΕΣΗ ΘΕΜΑΤΟΣ" AND thesis_assignments.status != "Cancelled"
        ');
        $stmt->bindParam(":assignmentId", $assignment_id, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$assignment) {
            http_response_code(404);
            echo json_encode(["success" => false, "reason" => "Assignment not found or you do not have permission to grade this assignment."]);
            exit;
        }
        $stmt = $pdo->prepare('
        SELECT teacher_id
        from teacher
        WHERE username = :username_teacher 
        ');
        $stmt->bindParam(':username_teacher',$username,PDO::PARAM_STR);
        $stmt->execute();
        $t_id = $stmt->fetchColumn();
        $stmt = $pdo->prepare('
        SELECT committee.com_id
        from committee
        INNER JOIN committee_members
        ON committee.com_id = committee_members.com_id
        WHERE committee_members.teacher_id = :t_id AND committee.thesis_assignment_id = :th_as_id;
        ');
        $stmt->bindParam(':t_id',$t_id,PDO::PARAM_INT);
        $stmt->bindParam(':th_as_id',$assignment_id,PDO::PARAM_INT);
        $stmt->execute();
        $com_id = $stmt->fetchColumn();

        $stmt = $pdo->prepare('
        SELECT assignment_date
        FROM thesis_assignments
        WHERE thesis_assignment_id = :my_thesis_assignment_id
        
        ');
        $stmt->bindParam(':my_thesis_assignment_id',$assignment_id,PDO::PARAM_INT);
        $stmt->execute();
        $assignment_date = $stmt->fetchColumn();
        // calculate time mark
        $current_date = new DateTime();
        $submission_date = new DateTime($assignment_date);
        $date_diff = $current_date->diff($submission_date);
        $days_diff = $date_diff->days;
        if($days_diff > 2*365){
            $time_mark = 0;
        }
        $stmt = $pdo->prepare("
            insert into marks
            (
                targets_fulfiled,
                quality_completeness,
                readable_thesis,
                time_satisfied
            )
            values(
                :quality_grade,
                :quality_complete,
                :rt_grade,
                :ts_grade
            );
        ");
        $stmt->bindParam(':quality_grade', $quality_grade, PDO::PARAM_STR);
        $stmt->bindParam(':quality_complete', $quality_completeness_grade, PDO::PARAM_STR);
        $stmt->bindParam(':rt_grade', $readable_thesis_grade, PDO::PARAM_STR);
        $stmt->bindParam(':ts_grade', $time_mark, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $pdo->prepare("
            SELECT MAX(mark_id)
            FROM marks;
        ");
        $stmt->execute();
        $max_mark_id = $stmt->fetchColumn();
        $stmt = $pdo->prepare("
            UPDATE committee_members
            SET mark_id = :max_mark_id
            WHERE 
                teacher_id = :t_id AND
                com_id = :committee_id;
        ");
        $stmt->bindParam(":max_mark_id",$max_mark_id,PDO::PARAM_INT);
        $stmt->bindParam(":t_id",$t_id,PDO::PARAM_INT);
        $stmt->bindParam(":committee_id",$com_id,PDO::PARAM_INT);
        $stmt->execute();
        
        // check if we are ready to upload the final grade
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM marks
            INNER JOIN committee_members
            ON committee_members.mark_id = marks.mark_id
            GROUP BY com_id
            HAVING com_id = :committee_id
        ");
        $stmt->bindParam(":committee_id",$com_id,PDO::PARAM_INT);
        $stmt->execute();
        $final_grading = $stmt->fetchColumn();
        if($final_grading == 3){
            // 3 has already mareked
            $stmt = $pdo->prepare("
            SELECT avg(marks.final_mark)
            FROM marks
            INNER JOIN committee_members
            ON committee_members.mark_id = marks.mark_id
            GROUP BY com_id
            HAVING com_id = :committee_id;
            ");
            $stmt->bindParam(":committee_id",$com_id,PDO::PARAM_INT);
            $stmt->execute();
            $final_grade = $stmt->fetchColumn();
            $stmt = $pdo->prepare("
            UPDATE committee
            SET avg_mark = :st_avg_mark 
            WHERE com_id = :committee_id");
            $stmt->bindParam(":st_avg_mark",$final_grade,PDO::PARAM_INT);
            $stmt->bindParam(":committee_id",$com_id,PDO::PARAM_INT);
            $stmt->execute();
            // practical of examination is ready
            // fetch students name
            $stmt = $pdo->prepare("
                SELECT 
                    student.name as student_name, 
                    meeting_room_or_link,
                    teacher.name as supervisor_name,
                    thesis_topics.title as thesis_title
                FROM student
                INNER JOIN thesis_assignments
                ON thesis_assignments.student_id = student.student_id
                INNER JOIN student_presentation
                ON student_presentation.thesis_assignment_id = thesis_assignments.thesis_assignment_id
                INNER JOIN thesis_topics
                ON thesis_topics.id = thesis_assignments.topic_id
                INNER JOIN teacher
                ON teacher.teacher_id = thesis_topics.supervisor_id
                WHERE thesis_assignments.thesis_assignment_id = :assignment_id 
            ");
            $stmt->bindParam(":assignment_id", $assignment_id , PDO::PARAM_INT);
            $stmt->execute();
            $thesis_student_details = $stmt->fetch(PDO::FETCH_ASSOC);
            // fetch committee members alongiside their avergae marks
            $stmt = $pdo->prepare("
                SELECT 
                    teacher.name as teacher_name,
                    committee_members.is_supervisor,
                    final_mark,
                    committee.avg_mark as avg_mark
                FROM committee_members
                INNER JOIN marks
                ON marks.mark_id = committee_members.mark_id
                INNER JOIN committee
                ON committee.com_id = committee_members.com_id
                INNER JOIN teacher
                ON teacher.teacher_id = committee_members.teacher_id
                WHERE committee.thesis_assignment_id = :assignmentId
                order by teacher.name;
            ");
            $stmt->bindParam(":assignmentId", $assignment_id , PDO::PARAM_INT);
            $stmt->execute();
            $committeeMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Get current date and time
            $current_date = date('d-m-Y');
            $current_time = date('H:i');
            $days = [
                'Monday' => 'Δευτέρα',
                'Tuesday' => 'Τρίτη',
                'Wednesday' => 'Τετάρτη',
                'Thursday' => 'Πέμπτη',
                'Friday' => 'Παρασκευή',
                'Saturday' => 'Σάββατο',
                'Sunday' => 'Κυριακή'
            ];
            $current_day = $days[date('l')];
            // Create HTML content
            $htmlContent = "<!DOCTYPE html>
            <html lang=\"el\">
            <head>
                <meta charset=\"UTF-8\">
                <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                <title>ΠΡΑΚΤΙΚΟ ΕΞΕΤΑΣΗΣ</title>
                <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
            </head>
            <body class=\"font-serif\">
                <div class=\"container my-4\">
                    <div class=\"row\">
                        <div class=\"col-12\">
                            <h2 class=\"text-center mb-3\">ΠΡΟΓΡΑΜΜΑ ΣΠΟΥΔΩΝ</h2>
                            <h3 class=\"text-center mb-4\">«ΤΜΗΜΑΤΟΣ ΜΗΧΑΝΙΚΩΝ, ΗΛΕΚΤΡΟΝΙΚΩΝ ΥΠΟΛΟΓΙΣΤΩΝ ΚΑΙ ΠΛΗΡΟΦΟΡΙΚΗΣ»</h3>
                            
                            <div class=\"text-center mb-5\">
                                <h4>ΠΡΑΚΤΙΚΟ ΣΥΝΕΔΡΙΑΣΗΣ</h4>
                                <h5>ΤΗΣ ΤΡΙΜΕΛΟΥΣ ΕΠΙΤΡΟΠΗΣ</h5>
                                <h5>ΓΙΑ ΤΗΝ ΠΑΡΟΥΣΙΑΣΗ ΚΑΙ ΚΡΙΣΗ ΤΗΣ ΔΙΠΛΩΜΑΤΙΚΗΣ ΕΡΓΑΣΙΑΣ</h5>
                            </div>
                            
                            <div class=\"mb-4 text-center\">
                                <h6>του/της φοιτητή/φοιτήτριας <strong>" . $thesis_student_details["student_name"] . "</strong></h6>
                            </div>
                            
                            <div class=\"card mb-4\">
                                <div class=\"card-body\">
                                    <p>Η συνεδρίαση πραγματοποιήθηκε στην αίθουσα/σύνδεσμος ". $thesis_student_details["meeting_room_or_link"]." στις ". $current_date ." ημέρα ". $current_day." και ώρα ". $current_time ."</p>
                                    
                                    <p class=\"mt-3\">Στη συνεδρίαση είναι παρόντα τα μέλη της Τριμελούς Επιτροπής, κ.κ:</p>
                                    
                                    <div class=\"my-3\">
                                        <ol class=\"list-group list-group-numbered\">
                                            <li class=\"list-group-item border-0\">
                                                <u class=\"px-3\">" . $committeeMembers[0]['teacher_name'] . "</u>
                                            </li>
                                            <li class=\"list-group-item border-0\">
                                                <u class=\"px-3\">" . $committeeMembers[1]['teacher_name'] . "</u>
                                            </li>
                                            <li class=\"list-group-item border-0\">
                                                <u class=\"px-3\">" . $committeeMembers[2]['teacher_name']  . "</u>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <div class=\"card mb-4\">
                                <div class=\"card-body\">
                                    <p>
                                        Ο/Η φοιτητής/φοιτήτρια κ. <u class=\"mx-2 px-2\">" . $thesis_student_details["student_name"] . "</u> ανέπτυξε το θέμα της Διπλωματικής του/της Εργασίας, με τίτλο «<u class=\"mx-2 px-2\">" . $thesis_student_details["thesis_title"]  . "</u>».
                                    </p>
                                    
                                    <p class=\"mt-3\">
                                        Στην συνέχεια υποβλήθηκαν ερωτήσεις στον υποψήφιο από τα μέλη της Τριμελούς Επιτροπής και τους άλλους παρευρισκόμενους, προκειμένου να διαμορφώσουν σαφή άποψη για το περιεχόμενο της εργασίας, για την επιστημονική συγκρότηση του μεταπτυχιακού φοιτητή. Μετά το τέλος της ανάπτυξης της εργασίας του και των ερωτήσεων, ο υποψήφιος αποχωρεί.
                                    </p>
                                    
                                    <p class=\"mt-3\">
                                        Ο Επιβλέπων καθηγητής κ. <u class=\"mx-2 px-2\">" . $thesis_student_details["supervisor_name"] . "</u>, προτείνει στα μέλη της Τριμελούς Επιτροπής, να ψηφίσουν για το αν εγκρίνεται η διπλωματική εργασία του <u class=\"mx-2 px-2\">" . $thesis_student_details["student_name"] . "</u>.
                                    </p>
                                </div>
                            </div>
                            
                            <div class=\"card mb-4\">
                                <div class=\"card-body\">
                                    <p>Τα μέλη της Τριμελούς Επιτροπής, ψηφίζουν κατ'αλφαβητική σειρά:</p>
                                    
                                    <ol class=\"list-group list-group-numbered mb-3\">
                                        <li class=\"list-group-item border-0\">
                                            <u class=\"px-3\">" . $committeeMembers[0]['teacher_name'] . "</u>
                                        </li>
                                        <li class=\"list-group-item border-0\">
                                            <u class=\"px-3\">" . $committeeMembers[1]['teacher_name'] . "</u>
                                        </li>
                                        <li class=\"list-group-item border-0\">
                                            <u class=\"px-3\">" . $committeeMembers[2]['teacher_name'] . "</u>
                                        </li>
                                    </ol>
                                    
                                    <p>
                                        υπέρ της εγκρίσεως της Διπλωματικής Εργασίας του φοιτητή <u class=\"mx-2 px-2\">" . $thesis_student_details['student_name'] . "</u>, επειδή θεωρούν επιστημονικά επαρκή και το περιεχόμενό της ανταποκρίνεται στο θέμα που του δόθηκε.
                                    </p>
                                </div>
                            </div>
                            
                            <div class=\"card mb-4\">
                                <div class=\"card-body\">
                                    <p>
                                        Μετά την έγκριση, ο εισηγητής κ. <u class=\"mx-2 px-2\">" . $thesis_student_details["supervisor_name"] . "</u>, προτείνει στα μέλη της Τριμελούς Επιτροπής, να απονεμηθεί στο/στη φοιτητή/τρια κ. <u class=\"mx-2 px-2\">" . $thesis_student_details['student_name'] . "</u> ο βαθμός <u class=\"mx-2 px-2\">" . $committeeMembers[0]['avg_mark'] . "</u>
                                    </p>
                                    
                                    <p class=\"mt-3\">Τα μέλη της Τριμελούς Επιτροπής, απονέμουν την παρακάτω Βαθμολογία:</p>
                                    
                                    <table class=\"table table-bordered my-3\">
                                        <thead class=\"table-light\">
                                            <tr>
                                                <th>ΟΝΟΜΑΤΕΠΩΝΥΜΟ</th>
                                                <th>ΒΑΘΜΟΛΟΓΙΑ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>" . ($committeeMembers[0]['teacher_name'] ?? '') . "</td>
                                                <td>" . ($committeeMembers[0]['final_mark'] ?? '') . "</td>
                                            </tr>
                                            <tr>
                                                <td>" . ($committeeMembers[1]['teacher_name'] ?? '') . "</td>
                                                <td>" . ($committeeMembers[1]['final_mark'] ?? '') . "</td>
                                            </tr>
                                            <tr>
                                                <td>" . ($committeeMembers[2]['teacher_name'] ?? '') . "</td>
                                                <td>" . ($committeeMembers[2]['final_mark'] ?? '') . "</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class=\"card mb-4\">
                                <div class=\"card-body\">
                                    <p>
                                        Μετά την έγκριση και την απονομή του βαθμού <u class=\"mx-2 px-2\">" . $committeeMembers[0]['avg_mark']  . "</u>, η Τριμελής Επιτροπή, προτείνει να προχωρήσει στην διαδικασία για να ανακηρύξει τον κ. <u class=\"mx-2 px-2\">" . $thesis_student_details["student_name"] . "</u>, σε διπλωματούχο του Προγράμματος Σπουδών του «ΤΜΗΜΑΤΟΣ ΜΗΧΑΝΙΚΩΝ, ΗΛΕΚΤΡΟΝΙΚΩΝ ΥΠΟΛΟΓΙΣΤΩΝ ΚΑΙ ΠΛΗΡΟΦΟΡΙΚΗΣ ΠΑΝΕΠΙΣΤΗΜΙΟΥ ΠΑΤΡΩΝ» και να του απονέμει το Δίπλωμα Μηχανικού Η/Υ το οποίο αναγνωρίζεται ως Ενιαίος Τίτλος Σπουδών Μεταπτυχιακού Επιπέδου.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </body>
            </html>";

            // Create directory if it doesn't exist
            $examDir = '../../uploads/exam_protocol';
            if (!file_exists($examDir)) {
                mkdir($examDir, 0755, true);
            }

            // Generate filename and save the file
            $filename = $examDir . '/' . $assignment_id  . '_' . date('Ymd') . '.html';
            if(!file_put_contents($filename, $htmlContent)){
                echo json_encode(["success" => false,"reason" => "not uploaded pdf for πρακτικό εξέτασης"]);
                exit;
            }
            $stmt = $pdo->prepare("
            UPDATE student_presentation
            SET examination_protocol_path = :file_path
            WHERE thesis_assignment_id = :th_as_id");
            $stmt->bindParam(":file_path", $filename, PDO::PARAM_STR);
            $stmt->bindParam(":th_as_id", $assignment_id , PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["success"=>true ,"status" => "average mark is uploaded and the html file!!"]);
        }
        else echo json_encode(["success"=>true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success"=>false,"error"=>$e->getMessage()]);
    }


?>