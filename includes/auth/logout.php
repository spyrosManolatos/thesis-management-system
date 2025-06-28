<?php
// Logout script to clear session and redirect to index page
session_start();
session_unset(); // Clear session variables
session_destroy(); // Clear session data from the server
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
header('Location: ../../index.php');
exit();
?>