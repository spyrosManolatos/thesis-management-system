<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../favicon.png">
    <link rel="shortcut icon" href="../../favicon.png">
    <title>Καλωσήρθατε στο Σύστημα Διαχείρισης Διπλωματικών</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="../../assets/js/main_screen.js" defer></script>
</head>

<body>
    <!-- Navigation menu with blue background -->
    <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-3">
        
        <li class="nav-item">
            <a class="nav-link btn btn-outline-light" href="../auth/login.php">
                <i class="bi bi-person-fill me-1"></i>Σύνδεση
            </a>
        </li>
    </nav> -->
    <?php $start_page = true;
    include "../../includes/header.php" ?>

    <div class="container">
        <div class="text-center py-3 bg-primary text-white rounded shadow mb-3">
            <h1>Καλωσήρθατε στο Σύστημα Διαχείρισης Διπλωματικών Εργασιών</h1>
            <p class="mb-0">Ηλεκτρονικό σύστημα για ανάθεση/βαθμολόγηση/εγκυροποίηση διπλωματικών εργασιών</p>
        </div>

        <div class="text-center py-3 bg-primary text-white rounded shadow">
            <h2 class="h3">Λίστα Ανακοινώσεων Διπλωματικών Εργασιών</h2>
            <p>Ανακοινώσεις από παρουσιάσεις φοιτητών του τμήματος μας</p>
            <div class="date-filter bg-white p-3 rounded shadow-sm mb-3">
                <form id="dateRangeForm" class="row g-2 align-items-center">
                    <div class="col-sm-5 col-md-4">
                        <label for="startDate" class="text-dark form-label mb-1">Από:</label>
                        <input type="date" class="form-control" id="startDate" name="startDate">
                    </div>
                    <div class="col-sm-5 col-md-4">
                        <label for="endDate" class="text-dark form-label mb-1">Έως:</label>
                        <input type="date" class="form-control" id="endDate" name="endDate">
                    </div>
                    <div class="col-sm-2 col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1">Φίλτρο</i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="table-responsive mt-2">
                <table class="table table-striped table-hover bg-white mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Φυσική Παρουσία</th>
                            <th>Αίθουσα Σύναντησης</th>
                            <th>Μαθητής</th>
                            <th>Ώρα Συνάντησης</th>
                            <th>Επέκταση;</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark" id="announcementDetails">
                        <tr>
                            <td colspan="5" class="text-center py-3">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="spinner-border text-primary me-2" role="status">
                                        <span class="visually-hidden">Φόρτωση...</span>
                                    </div>
                                    <span>Φόρτωση ανακοινώσεων...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>