<?php
session_start();
// Check if user is not logged in or not a teacher
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== $userType) {
    header('Location: ../../views/auth/login.php?userAuth=false');
    exit();
}
session_abort();
