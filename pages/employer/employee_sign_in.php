<?php
session_start();
include '../../db_connection/connection.php';

$conn = OpenConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ? AND user_type = 'employer'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $stored_password);
    $stmt->fetch();
    $stmt->close();

    if ($user_id && password_verify($password, $stored_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header("Location: employee_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/icon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../static/css/employee_sign_in.css">
    <title>Sign In - Employer Portal</title>
</head>
<body>
    <div class="left_section">
        <img src="../../static/img/icon/logo_employ.png" alt="Hire Path Logo">
        
    </div>
    <div class="right_section">
        <div class="container">
            <h2>Sign In</h2>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="employee_sign_in.php">
                <div class="form_group">
                    <input type="text" id="username" name="username" placeholder="Enter your Usename" required>
                </div>
                <div class="form_group">
                    <div class="input_wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your Password" required />
                        <span id="togglePassword" class="password_toggle_icon">
                            <img id="passwordToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                        </span>
                    </div>
                </div>
                <button type="submit">Sign In</button>
            </form>
            <p class="footer">Don't have an account? <a href="regEmployee.php">Sign Up</a></p>
        </div>
    </div>
    <script src="../../static/js/employee_sign_in.js"></script>
</body>
</html>
