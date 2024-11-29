<?php

//fullname validation
if (empty($_POST["name"])){
  die("Name is required!");
}

//email validation
if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
  die("Valid Email is required");
}

//password validation
if (strlen($_POST["password"]) < 8){
  die("Password must be at least 8 Characters");
}
if (! preg_match("/[a-z]/i", $_POST["password"])){
  die("Password must contain at least one letter");
}
if (! preg_match("/[0-9]/i", $_POST["password"])){
  die("Password must contain at least one number");
}

if($_POST["password"] != $_POST["confirm-password"]){
  die("Password must match");
}


$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$role = 'user';

// $role = $_POST["role"];
// if ($role !== 'user' && $role !== 'admin') {
//   die("Invalid role selected");
// }


//requires a directory
$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO users (fullname, email, password_hash, role)
        VALUES (?, ?, ?, ?)";

$stmt = $mysqli -> stmt_init();

if ( ! $stmt -> prepare($sql)){
  die("SQL error: " . $mysqli -> error);
}


$stmt -> bind_param("ssss",
                    $_POST["name"],
                    $_POST["email"],
                    $password_hash,
                    $role);

if ($stmt -> execute()){

  header("Location: signup-success.html");
  exit;

} else {
  // Email duplication handling (not needed here, as it's checked via frontend)
  if ($mysqli->errno === 1062) {
      die("Email already taken");
  } else {
      die($mysqli->error . "" . $mysqli->errno);
  }
}

