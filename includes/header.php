<div class="container-fluid bg-primary text-white py-3 mb-3">
    <div class="container">
        <div class="d-flex justify-content-center py-4">
            <a href="#" class="text-decoration-none text-white">
                <h1>Σύστημα Διαχείρισης Διπλωματικών Εργασιών </h1>
            </a>
        </div>

        <?php
        if(session_status()===PHP_SESSION_NONE){
            session_start();
        }
        // dashboard because the username is set
        if (isset($dashboard)) {
            echo '
            <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Καλώς ήρθατε, ' . $_SESSION['username'] . '!</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <form action="../../includes/auth/logout.php" method="post" class="m-0">
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Αποσύνδεση
                    </button>
                </form>
            </div>
            </div>';
        }
        // start page because nothing is set
        elseif(isset($start_page)){
            echo '<div class="text-md-end">
            <a href="../auth/login.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-person-fill me-1"></i>Σύνδεση
            </a>
        </div>';
        }
        elseif(isset($login_page)){
            echo '<div class="text-center">
                <h4 class="mb-3">Έλεγχος ταυτοποίησης και εξουσιοδότησης</h4>
            </div>';
        }
        ?>
    </div>
</div>