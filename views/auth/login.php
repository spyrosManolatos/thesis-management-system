<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="../../favicon.jpg">
    <link rel="shortcut icon" href="../../favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ΣΥΣΤΗΜΑ ΔΙΑΧΕΙΡΙΣΗΣ ΔΙΠΛΩΜΑΤΙΚΩΝ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../assets/js/authenticate.js" defer></script>
    <script></script>
</head>

<body class="bg-light">
    <?php
    $login_page = true;
    include "../../includes/header.php";
    ?>
    <div class="container mt-5 pt-5">

        <!-- <div class="title text-center py-5">
            <h1 class="display-4">Σύστημα Διαχείρισης Διπλωματικών Εργασιών</h1>
        </div> -->

        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <!-- Notice the extra id="loginForm" added -->
                        <form method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">Username:</label>
                                <input id="username" class="form-control form-control-lg" type="text" name="name" placeholder="Username:" autocomplete="off" />
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password:</label>
                                <input id="password" class="form-control form-control-lg" type="password" name="password" placeholder="Password:" autocomplete="off" />
                            </div>
                            <div id="alertContainer">
                                <!-- error of redirection -->
                                <?php
                                if (isset($_GET['userAuth'])) {
                                    echo '<div class="alert alert-danger mt-3">Ανακατεύθυνση λόγω μη εξουσιοδότησης</div>';
                                }
                                ?>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg">Σύνδεση</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>