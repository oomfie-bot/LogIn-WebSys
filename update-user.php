<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

// Check if the user is an admin
$sql = "SELECT role FROM users WHERE id = ?";
$stmt = $mysqli->stmt_init();
if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$logged_in_user = $result->fetch_assoc();

if ($logged_in_user['role'] !== 'admin') {
    die("Access denied: Only admins can update user information.");
}

// Fetch all users
$sql = "SELECT id, fullname, email, role FROM users";
$result = $mysqli->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "UPDATE users SET fullname = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $mysqli->stmt_init();

    if (!$stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("sssi", $_POST["fullname"], $_POST["email"], $_POST["role"], $_POST["user_id"]);

    if ($stmt->execute()) {
        header("Location: update-user.php");
        exit;
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Update User</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <form method="post">
                        <td>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($user["fullname"]) ?>" <?= $user["id"] === $_SESSION["user_id"] ? 'disabled' : '' ?>>
                        </td>
                        <td>
                            <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" <?= $user["id"] === $_SESSION["user_id"] ? 'disabled' : '' ?>>
                        </td>
                        <td>
                            <select name="role" <?= $user["id"] === $_SESSION["user_id"] ? 'disabled' : '' ?>>
                                <option value="user" <?= $user["role"] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user["role"] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
                            <button type="submit" <?= $user["id"] === $_SESSION["user_id"] ? 'disabled' : '' ?>>Update</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="index.php" class="button">Done</a>
</body>
</html>