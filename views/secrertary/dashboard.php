<?php $userType="secrertary"; include "../../includes/auth/illegal_redirection.php"?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../../favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ΣΥΣΤΗΜΑ ΔΙΑΧΕΙΡΙΣΗΣ ΔΙΠΛΩΜΑΤΙΚΩΝ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="../../assets/js/secretary.js" defer></script>
</head>
<?php $dashboard = true;
include '../../includes/header.php'; ?>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Πίνακας Γραμματείας</h3>

                        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="view-thesis-tab" data-bs-toggle="tab" data-bs-target="#view-thesis" type="button" role="tab" aria-controls="view-thesis" aria-selected="true">Προβολή Διπλωματικών</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="import-data-tab" data-bs-toggle="tab" data-bs-target="#import-data" type="button" role="tab" aria-controls="import-data" aria-selected="false">Εισαγωγή Δεδομένων</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="manage-thesis-tab" data-bs-toggle="tab" data-bs-target="#manage-thesis" type="button" role="tab" aria-controls="manage-thesis" aria-selected="false">Διαχείριση Διπλωματικών</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <!-- Προβολή Διπλωματικών -->
                            <div class="tab-pane fade show active" id="view-thesis" role="tabpanel" aria-labelledby="view-thesis-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Προβολή Διπλωματικών Εργασιών</h4>
                                    <div id="thesisList" class="text-center py-5 text-muted">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Φόρτωση διπλωματικών...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Εισαγωγή Δεδομένων -->
                            <div class="tab-pane fade" id="import-data" role="tabpanel" aria-labelledby="import-data-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Εισαγωγή Δεδομένων</h4>
                                    <form id="importDataForm" action="import_data.php" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="jsonFile" class="form-label">Αρχείο JSON</label>
                                            <input type="file" class="form-control" id="jsonFile" name="jsonFile" accept=".json" required>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">Εισαγωγή Δεδομένων</button>
                                        </div>
                                    </form>
                                    <div id="importResult" class="mt-3"></div>
                                </div>
                            </div>

                            <!-- Διαχείριση Διπλωματικών -->
                            <div class="tab-pane fade" id="manage-thesis" role="tabpanel" aria-labelledby="manage-thesis-tab">
                                <div class="p-4">
                                    <h4 class="mb-4">Διαχείριση Διπλωματικών Εργασιών</h4>
                                    <div id="manageThesisList" class="text-center py-5 text-muted">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Φόρτωση διπλωματικών...</p>
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
    <script>
        
    </script>
</body>

</html>