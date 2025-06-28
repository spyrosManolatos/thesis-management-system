<?php
$host = "localhost";
$username_db = "root";
$database = "diplomacy_system";
$password = "";
function getDbConnection()
{
    global $host, $username_db, $database, $password;
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username_db, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
        exit;
    }
}
