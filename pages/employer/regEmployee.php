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

        header("Location: employee_sign_in.php?reg=1");
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
    <link rel="stylesheet" href="../../static/css/employee_sign_in.css">
    <title>Register - Employer Portal</title>
    <style>
        body {
            display: flex;
            height: 100vh;
            background-color: #0A2647;
        }
        .left_section {
            flex: 0.5; 
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .left_section img {
            max-width: 500px;   
            margin-bottom: 80px;
        }
        .left_section h1 {
            font-size: 24px;
            text-align: center;
        }
        .right_section {
            flex: 1.5; 
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('../../static/img/icon/login.jpg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }
        .container {
            background: rgba(255, 255, 255, 0.92);
            padding: 48px 36px 36px 36px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(10, 38, 71, 0.18);
            width: 90%;
            height: 100%;
            max-width: 600px;
            max-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        h2 {
            color: #0A2647;
            margin-bottom: 28px;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-align: center;
        }
        .form_group {
            margin-bottom: 18px;
            width: 100%;
        }
        label {
            font-size: 1rem;
            color: #0A2647;
            margin-bottom: 6px;
            font-weight: 500;
            display: block;
        }
        input {
            background: rgba(255, 255, 255, 0.98);
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #bfc9d1;
            border-radius: 7px;
            font-size: 1rem;
            margin-top: 4px;
            outline: none;
            transition: border 0.2s;
            box-shadow: 0 1px 2px rgba(10, 38, 71, 0.04);
        }
        input:focus {
            border: 1.5px solid #0A2647;
            background: #f7fbff;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #0A2647 60%, #144272 100%);
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(10, 38, 71, 0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        button:hover {
            background: linear-gradient(90deg, #144272 60%, #0A2647 100%);
            box-shadow: 0 4px 16px rgba(10, 38, 71, 0.13);
        }
        .footer {
            text-align: center;
            margin-top: 28px;
            margin-bottom: 0;
            width: 100%;
            position: static;
            background: none;
            box-shadow: none;
            font-size: 1rem;
            color: #333;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .footer a {
            color: #0A2647;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s, text-decoration 0.2s, background 0.2s;
            padding: 2px 8px;
            border-radius: 4px;
            background: transparent;
            outline: none;
            margin-left: 4px;
        }
        .footer a:hover,
        .footer a:focus {
            color: #144272;
            text-decoration: underline;
            background: #eaf1fa;
        }
        @media (max-width: 600px) {
            .container {
                padding: 24px 8px 16px 8px;
                max-width: 98vw;
            }
            .footer-content {
                flex-direction: column;
                gap: 4px;
            }
        }
        .error {
            color: #d8000c;
            background: #ffd2d2;
            border: 1px solid #d8000c;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
            text-align: center;
        }
        .input_wrapper {
            position: relative;
            width: 100%;
        }
        .password_toggle_icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            height: 22px;
            width: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password_toggle_icon img {
            width: 22px;
            height: 22px;
            user-select: none;
        }
        /* Adjust label spacing for consistency */
        .form_group label {
            margin-bottom: 6px;
        }
        /* Remove default button margin-top for better alignment */
        form button[type="submit"] {
            margin-top: 16px;
        }
        .popup-notification {
            position: fixed;
            bottom: 32px;
            right: 32px;
            min-width: 260px;
            max-width: 350px;
            padding: 18px 32px 18px 18px;
            border-radius: 8px;
            color: #fff;
            font-size: 1.1em;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s, transform 0.5s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.13);
            text-align: center;
        }
        .popup-notification.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }
        .popup-notification.success {
            background: #28a745;
        }
        .popup-notification.error {
            background: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Popup Notification -->
    <div id="popupNotification" class="popup-notification">
        <span id="popupMessage"></span>
    </div>
    <div class="left_section">
        <img src="../../static/img/icon/logo_employ.png" alt="Hire Path Logo">
    </div>
    <div class="right_section">
        <div class="container">
            <h2>Register as an Employer</h2>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="regEmployee.php" autocomplete="off">
                <div class="form_group">
                    <input type="text" id="username" name="username" placeholder="Enter your Username" required autocomplete="username">
                </div>
                <div class="form_group">
                    <input type="email" id="email" name="email" placeholder="Enter your Email" required autocomplete="email">
                </div>
                <div class="form_group">
                    <div class="input_wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your Password" required autocomplete="new-password">
                        <span id="togglePassword" class="password_toggle_icon">
                            <img id="passwordToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                        </span>
                    </div>
                </div>
                <button type="submit">Register</button>
            </form>
            <p class="footer">
                <span class="footer-content">
                    <span>Already have an account?</span>
                    <a href="employee_sign_in.php">Sign In</a>
                </span>
            </p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const toggleImage = document.getElementById('passwordToggleImage');

            passwordField.addEventListener('focus', function () {
                if (passwordField.value.length > 0) {
                    togglePassword.classList.add('show');
                }
            });

            passwordField.addEventListener('input', function () {
                if (passwordField.value.length > 0) {
                    togglePassword.classList.add('show');
                } else {
                    togglePassword.classList.remove('show');
                }
            });

            passwordField.addEventListener('blur', function () {
                if (passwordField.value.length === 0) {
                    togglePassword.classList.remove('show');
                }
            });

            togglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    toggleImage.src = "../../static/img/icon/visible.png"; 
                } else {
                    passwordField.type = "password";
                    toggleImage.src = "../../static/img/icon/hidden.png"; 
                }
            });
        });

        // Popup notification logic
        function showPopup(message, type, redirectUrl = null) {
            const popup = document.getElementById('popupNotification');
            const msg = document.getElementById('popupMessage');
            popup.className = 'popup-notification ' + type;
            msg.textContent = message;
            popup.classList.add('show');
            setTimeout(() => {
                popup.classList.remove('show');
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            }, 3000);
        }
        <?php if (!empty($error)): ?>
            showPopup(<?php echo json_encode($error); ?>, 'error');
        <?php endif; ?>
    </script>
</body>
</html>
