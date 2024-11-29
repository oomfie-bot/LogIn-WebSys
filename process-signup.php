<?php

// Fullname validation
if (empty($_POST["name"])) {
    die("Name is required!");
}

// Email validation
if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid Email is required");
}

// Password validation
if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 Characters");
}
if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}
if (!preg_match("/[0-9]/i", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] != $_POST["confirm-password"]) {
    die("Password must match");
}
$role = 'user';

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Requires a directory
$mysqli = require __DIR__ . "/database.php";

// Check if the email is already taken
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $mysqli->stmt_init();
if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}
$stmt->bind_param("s", $_POST["email"]);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    die("Email already taken");
}

$role = 'user'; // Set default role to 'user'

$sql = "INSERT INTO users (fullname, email, password_hash, role) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->stmt_init();
if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}
$stmt->bind_param("ssss", $_POST["name"], $_POST["email"], $password_hash, $role);

if ($stmt->execute()) {
    header("Location: signup-success.html");
    exit;
} else {
    die($mysqli->error . " " . $mysqli->errno);
}
