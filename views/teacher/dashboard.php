<?php $userType ="teacher"; include '../../includes/auth/illegal_redirection.php'; ?>

<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../favicon.png">
    <link rel="shortcut icon" href="../../favicon.png">
    <title>ΔΑΣΚΑΛΟΣ | ΣΥΣΤΗΜΑ ΔΙΑΧΕΙΡΙΣΗΣ ΔΙΠΛΩΜΑΤΙΚΩΝ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="../../assets/js/teacher.js" defer></script>
</head>
<?php $dashboard = true;
include '../../includes/header.php'; ?>

<body class="bg-light">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Πίνακας Καθηγητή</h3>

                        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="thesis-tab" data-bs-toggle="tab" data-bs-target="#thesis" type="button" role="tab" aria-controls="thesis" aria-selected="true">Προσθήκη Θέματος</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab" aria-controls="list" aria-selected="false">Λίστα Θεμάτων</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="assign-tab" data-bs-toggle="tab" data-bs-target="#assign" type="button" role="tab" aria-controls="assign" aria-selected="false">Ανάθεση Διπλωματικής</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab" aria-controls="assignments" aria-selected="false">Αναθέσεις</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="invitations-tab" data-bs-toggle="tab" data-bs-target="#invitations" type="button" role="tab" aria-controls="invitations" aria-selected="false">Προσκλήσεις για 3μελή επιτροπή</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#statistics" type="button" role="tab" aria-controls="statistics" aria-selected="false">Προβολή στατιστικών</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <!-- Thesis Submission Tab -->
                            <div class="tab-pane fade show active" id="thesis" role="tabpanel" aria-labelledby="thesis-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Υποβολή νέου θέματος διπλωματικής</h4>

                                    <form id="thesisForm" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Τίτλος Θέματος</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Περιγραφή</label>
                                            <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label for="pdf_file" class="form-label">PDF αρχείο</label>
                                            <input class="form-control" type="file" id="pdf_file" name="pdf_file" accept=".pdf">
                                            <div class="form-text">Προαιρετικά: Ανεβάστε ένα PDF με αναλυτικές πληροφορίες.</div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Υποβολή Θέματος</button>
                                        </div>
                                    </form>

                                    <div id="thesisSubmitResult" class="mt-3"></div>
                                </div>
                            </div>

                            <!-- List Tab -->
                            <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Τα θέματα διπλωματικών σας</h4>
                                    <div id="thesisList" class="text-center py-5 text-muted">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Φόρτωση θεμάτων...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Assign Thesis Tab -->
                            <div class="tab-pane fade" id="assign" role="tabpanel" aria-labelledby="assign-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Ανάθεση θέματος σε φοιτητή</h4>

                                    <form id="assignThesisForm" method="post">
                                        <div class="mb-3">
                                            <label for="topic_id" class="form-label">Θέμα Διπλωματικής</label>
                                            <select class="form-select" id="topic_id" name="topic_id" required>
                                                <!-- Options will be loaded via AJAX -->
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label for="student_id" class="form-label">Φοιτητής</label>
                                            <select class="form-select" id="student_id" name="student_id" required>
                                                <!-- Options will be loaded via AJAX -->
                                            </select>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Ανάθεση Διπλωματικής</button>
                                        </div>
                                    </form>

                                    <div id="assignmentResult" class="mt-3"></div>
                                </div>
                            </div>

                            <!-- Assignments Tab -->
                            <div class="tab-pane fade" id="assignments" role="tabpanel" aria-labelledby="assignments-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Αναθέσεις Διπλωματικών</h4>
                                    <div class="mb-3">
                                        <label for="supervisorStatusFilter" class="form-label">Φιλτράρισμα κατά ρόλο:</label>
                                        <select class="form-select" id="supervisorStatusFilter">
                                            <option value="supervisor">Ως επιβλέπων</option>
                                            <option value="committee_member">Ως μέλος της επιτροπής</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="assignmentStatusFilter" class="form-label">Φιλτράρισμα κατά κατάσταση:</label>
                                        <select class="form-select" id="assignmentStatusFilter">
                                            <option value="all" selected>Όλες οι αναθέσεις</option>
                                            <option value="Pending">Εκκρεμείς</option>
                                            <option value="Active">Ενεργές</option>
                                            <option value="Under Examination">Υπό εξέταση</option>
                                            <option value="Completed">Ολοκληρωμένες</option>
                                            <option value="Cancelled">Ακυρωμένες</option>
                                        </select>
                                    </div>

                                    <div id="assignmentsList" class="text-center py-5 text-muted">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Φόρτωση αναθέσεων...</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Invitiations for 3-members counclil -->
                            <div class="tab-pane fade" id="invitations" role="tabpanel" aria-labelledby="assignments-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Προσκλήσεις για 3μελη επιτροπή</h4>

                                    <div id="invitationsList" class="text-center py-5 text-muted">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Φόρτωση προσκλήσεων...</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Statistics Display -->
                            <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="stats-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Στατιστικά στοιχεία</h4>
                                    <div class="mb-3">
                                        <label for="statsStatusFilter" class="form-label">Φιλτράρισμα :</label>
                                        <select class="form-select" id="statsStatusFilter">
                                            <option value="thesis_quantity" selected>Πλήθος διπλωματικών</option>
                                            <option value="average_thesis_mark">Μέσος βαθμός διπλωματικών</option>
                                            <option value="thesis_average_time">Μέσος χρόνος περάτωσης διπλωματικών</option>
                                        </select>
                                    </div>
                                    <div id="statsCharts" class="text-center py-5 text-muted">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Φόρτωση διαγραμμάτων...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>