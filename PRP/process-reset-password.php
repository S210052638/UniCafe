<?php

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";


$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found. Please request a new password reset.");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired. Please request a new password reset.");
}

// Password validation
if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters long.");
}

if (!preg_match("/[a-zA-Z]/", $_POST["password"])) {
    die("Password must contain at least one letter.");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number.");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords do not match. Please try again.");
}

// Hash the password
$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Update the user's password
    sql = "UPDATE users 
    SET password_hash = ?, 
        reset_token_hash = NULL, 
        reset_token_expires_at = NULL 
    WHERE userID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $password_hash, $user["userID"]);
$stmt->execute();

echo "Password updated successfully. You can now log in.";
