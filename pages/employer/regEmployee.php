<?php
session_start();
include '../../db_connection/connection.php';

$conn = OpenConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $email = trim($_POST['email']);
    $user_type = 'employer';

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, user_type, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $username, $password, $email, $user_type);

        if (!$stmt->execute()) {
            throw new Exception("Failed to register user.");
        }

        $stmt->close();

        header("Location: Employee_dashboard.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/icon/favicon.png" type="image/x-icon">
    <title>Register - Employer Portal</title>
</head>
<body>
    <div class="container">
        <h1>Register as an Employer</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="regEmployee.php">
            <div class="form_group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form_group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form_group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="employee_sign_in.php">Log in here</a>.</p>
    </div>
</body>
</html>
