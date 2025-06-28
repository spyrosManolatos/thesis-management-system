<?php
session_start();
require_once "../../config/db_config.php";
$name = $_POST["name"];
$user_password = $_POST["password"];
$pdo = getDbConnection();
$stmt = $pdo->prepare("SELECT * from user_det where USER= :username and password= :user_password");
$stmt->bindParam(":username", $name, PDO::PARAM_STR);
$stmt->bindParam(":user_password", $user_password, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($result) > 0) {
    $_SESSION["username"] = $result[0]["USER"];
    $_SESSION["userType"] = $result[0]["userType"];

    $userType = $_SESSION["userType"];
    echo json_encode(['success' => true, 'redirect' => "../$userType/dashboard.php"]);
} else {
    echo json_encode(['success' => false]);
}
