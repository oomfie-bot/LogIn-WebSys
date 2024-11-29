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
    die("Access denied: Only admins can delete user information.");
}

// Fetch all users
$sql = "SELECT id, fullname, email, role FROM users";
$result = $mysqli->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id_to_delete = $_POST["user_id"];
    if ($user_id_to_delete == $_SESSION["user_id"]) {
        die("You cannot delete your own account.");
    }

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $mysqli->stmt_init();

    if (!$stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("i", $user_id_to_delete);

    if ($stmt->execute()) {
        header("Location: delete-user.php");
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
    <title>Delete User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Delete User</h1>
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
                    <td><?= htmlspecialchars($user["fullname"]) ?></td>
                    <td><?= htmlspecialchars($user["email"]) ?></td>
                    <td><?= htmlspecialchars($user["role"]) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?= $user["id"] ?>">
                            <button type="submit" <?= $user["id"] === $_SESSION["user_id"] ? 'disabled' : '' ?>>Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="index.php" class="button">Done</a>
</body>
</html>