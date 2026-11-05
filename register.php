<?php
require "db.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8) {
        $message = "Enter a name, valid email and password of at least 8 characters.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $hash);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $message = "Registration failed. The email may already exist.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="menu" style="max-width:500px;margin:auto">
    <h2>Create Account</h2>
    <p><?= htmlspecialchars($message) ?></p>
    <form method="post">
        <label>Name</label>
        <input name="name" required maxlength="100">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password (minimum 8 characters)</label>
        <input type="password" name="password" minlength="8" required>
        <button type="submit">Register</button>
    </form>
    <a href="login.php">Already registered? Login</a>
</main>
</body>
</html>