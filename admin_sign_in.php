<?php
session_start();

// Check if the admin is already logged in
if (isset($_SESSION['admin_id'], $_SESSION['admin_username'])) {
    header("Location: pages/admin/admin_dashboard.php");
    exit();
}

// Include database connection
include 'db_connection/connection.php';
$conn = OpenConnection();

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch admin details from the database
    $query = "SELECT * FROM `users` WHERE `username` = ? AND `user_type` = 'admin'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && $password === $admin['password']) {
        // Set session variables
        $_SESSION['admin_id'] = $admin['user_id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: pages/admin/admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="static/img/icon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="static/css/sign_up.css">
    <title>Admin Sign In</title>
    <style>
        .right_section .container {
            max-height: 520px;
            height: auto;
        }
        .password_toggle_icon {
            display: flex !important;
        }
        @media (max-width: 900px) {
            .left_section {
                display: none;
            }
            .right_section {
                flex: 1 1 100%;
            }
            .modal-content {
                min-width: 0;
                max-width: 100%;
                padding: 18px 4vw 12px 4vw;
            }
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="right_section">
        <div class="container">
            <h2 id="form-title">Admin Sign In</h2>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <form id="adminSignInForm" action="admin_sign_in.php" method="POST">
                <div class="form_group">
                    <input type="text" name="username" placeholder="Admin Username" required>
                </div>
                <div class="form_group">
                    <div class="input_wrapper">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <span id="togglePassword" class="password_toggle_icon">
                            <img id="passwordToggleImage" src="static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                        </span>
                    </div>
                </div>
                <button type="submit">Sign In</button>
            </form>
        </div>
    </div>
    <div class="left_section">
        <img src="static/img/icon/logo_admin.png" alt="Hire Path Logo">
    </div>
    <script>
        // Eye toggle for password field
        function setupPasswordToggle(inputId, toggleId, imgId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            const img = document.getElementById(imgId);
            if (toggle && input && img) {
                toggle.addEventListener('click', function () {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    img.src = type === 'password'
                        ? 'static/img/icon/hidden.png'
                        : 'static/img/icon/visible.png';
                });
            }
        }
        setupPasswordToggle('password', 'togglePassword', 'passwordToggleImage');
    </script>
</body>
</html>
