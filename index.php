<?php

session_start();

if(isset($_SESSION["user_id"])){

  $mysqli = require __DIR__ ."/database.php";

  $sql = "SELECT * FROM users
      WHERE id = {$_SESSION["user_id"]}";

  $result = $mysqli->query($sql);
  
  $user = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <title>Home</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">

</head>
<body>

  <h1>Home</h1>

  <?php if (isset($user)): ?>
      <p>Hello <b><?= htmlspecialchars($user["fullname"]) ?></b></p>
      <p></p>
      <?php if ($user["role"] === 'admin'): ?>
          <p><a href="update-user.php?user_id=<?= $user["id"] ?>">Update Profile</a></p>
          <p><a href="delete-user.php?user_id=<?= $user["id"] ?>">Delete Account</a></p>
      <?php endif; ?>
      <p><a href="logout.php">Log out</a></p>
  <?php else: ?>
    <p><a href="login.php">Log in</a> or <a href="signup.html">Sign up</a></p>
  
  <?php endif; ?>
        
</body>
</>