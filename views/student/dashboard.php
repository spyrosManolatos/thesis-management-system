<?php $userType = 'student';include '../../includes/auth/illegal_redirection.php'?>

<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Φοιτητή</title>
    <link rel="icon" href="../../favicon.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Custom JS -->
    <script src="../../assets/js/student.js"></script>
</head>

<body>

    <div class="container mt-4">
        <!-- Κεφαλίδα -->
        <header class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h1 class="h4">Καλώς ήρθες, <span id="studentName">Φοιτητής</span></h1>
            <a href="#" id="logoutBtn" class="btn btn-danger">Αποσύνδεση</a>

        </header>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 rounded shadow-sm">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNavbar" aria-controls="studentNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="studentNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item me-2">
                            <a class="nav-link" href="#" id="view-theses">Η Διπλωματική μου εργασία</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="nav-link" href="#" id="view-profile">Προφίλ</a>
                        </li>
                        <!-- <li class="nav-item me-2">
                            <a class="nav-link" href="#" id="view-grades">Διαχείριση Διπλωματικής</a>
                        </li> -->
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Περιεχόμενο -->
        <div id="content-area">
            <!-- Ενότητα: Διαθέσιμες διπλωματικές -->
            <div id="availableThesesSection" style="display: none;">
                <h3 class="mb-4">Η διπλωματική εργασία μου</h3>
                <div id="thesisList" class="row g-4">
                    <!-- Οι διπλωματικές θα φορτωθούν εδώ με JavaScript -->

                </div>
            </div>
            <!-- Ενότητα: Προφίλ Φοιτητή -->
            <div id="profileSection" style="display: none;">
                <h3 class="mb-4">Προφίλ Φοιτητή</h3>
                <form id="studentProfileForm">
                    <div class="mb-3">
                        <label for="studentNameInput" class="form-label">Ονοματεπώνυμο</label>
                        <input type="text" class="form-control" id="studentNameInput" name ="student" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="studentAreaInput" class="form-label">Περιοχή κατοικίας</label>
                        <input type="text" class="form-control" id="studentAreaInput" name="residence" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentEmailInput" class="form-label">Email</label>
                        <input type="email" class="form-control" id="studentEmailInput" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="studentPhoneInput" class="form-label">Τηλέφωνο</label>
                        <input type="text" class="form-control" id="studentPhoneInput" name="phone" required>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="studentPasswordInput" class="form-label">Κωδικός</label>
                        <input type="password" class="form-control" id="studentPasswordInput">
                    </div> -->
                    
                    <button type="submit" class="btn btn-primary">Αποθήκευση Αλλαγών</button>
                    <div id="profileMessage" class="mt-3"></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>