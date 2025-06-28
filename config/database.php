<?php

function getDatabaseConnection() {
    $dbServername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "diplomacy_system";

    try {
        $conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
        return $conn;
    } catch (mysqli_sql_exception) {
        echo "Connection failed";
        echo "<br>";
        return null;
    }
}
?>