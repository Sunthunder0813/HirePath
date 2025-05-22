<?php
session_start();
include '../../db_connection/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $conn = OpenConnection();

        if (!$conn) {
            $error = "Database connection failed.";
        } else {
            $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
            if (!$stmt) {
                $error = "Database query failed.";
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($user_id, $stored_password);
                    $stmt->fetch();

                    if (password_verify($password, $stored_password)) {
                        $_SESSION['user_id'] = $user_id;
                        header("Location: ../../index.php");
                        exit;
                    } else {
                        $error = "Invalid password.";
                    }
                } else {
                    $error = "No user found with that email.";
                }
                $stmt->close();
            }
            CloseConnection($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../static/css/sign_in.css">
    <link rel="shortcut icon" href="../../static/img//icon/favicon.png" type="image/x-icon">
    <title>Sign In - Job Portal</title>
</head>
<body>
    <div class="container">
        <h2>Sign In</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="sign_in.php" method="POST">
            <div class="input-wrapper">
                <input type="email" name="email" placeholder="Your Email" required>
            </div>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span id="togglePassword" class="password_toggle_icon">
                    <img id="passwordToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                </span>
            </div>
            <button type="submit">Sign In</button>
        </form>
        <div class="footer">
            <p>Don't have an account? <a href="sign_up.php">Sign Up</a></p>
        </div>
    </div>
    <script src="../../static/js/sign_in.js"></script>
</body>
</html>