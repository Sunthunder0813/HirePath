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
        header("Location: employee_dashboard.php?login=1");
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
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
            position: relative; /* Add this line */
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
        .footer a,
        #backToSignInFooter {
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
        .footer a:focus,
        #backToSignInFooter:hover,
        #backToSignInFooter:focus {
            color: #144272;
            text-decoration: underline;
            background: #eaf1fa;
        }
        .footer-divider {
            display: inline-block;
            margin: 0 8px;
            color: #bfc9d1;
            font-size: 1.1em;
            user-select: none;
        }
        #backToSignInWrapper {
            display: inline;
        }
        @media (max-width: 600px) {
            .footer-content {
                flex-direction: column;
                gap: 4px;
            }
            .footer-divider {
                display: none;
            }
        }
        #forgotPasswordForm {
            position: relative;
        }
        .back-icon-btn {
            position: absolute;
            top: 56px; /* Aligns with input field */
            left: 12px;
            font-size: 1.7rem;
            color: #0A2647;
            background: none;
            border: none;
            outline: none;
            cursor: pointer;
            text-decoration: none;
            z-index: 10;
            padding: 2px 8px 2px 2px;
            border-radius: 50%;
            display: inline-block;
        }
        #forgotStep1 {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: center;
            gap: 18px;
            height: 100%;
            min-height: 0;
            margin-top: 30px;
        }
        #forgotStep1 .form_group {
            margin-bottom: 0;
        }
        #forgotStep1 input[type="text"] {
            font-size: 1.08rem;
            padding: 14px 16px;
            border: 1.5px solid #bfc9d1;
            border-radius: 7px;
            background: rgba(255,255,255,0.98);
            transition: border 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 2px rgba(10,38,71,0.04);
        }
        #forgotStep1 input[type="text"]:focus {
            border: 1.5px solid #0A2647;
            background: #f7fbff;
        }
        #forgotStep1 button {
            background: linear-gradient(90deg, #0A2647 60%, #144272 100%);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 13px 0;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            box-shadow: 0 2px 8px rgba(10,38,71,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        #forgotStep1 button:hover {
            background: linear-gradient(90deg, #144272 60%, #0A2647 100%);
            box-shadow: 0 4px 16px rgba(10,38,71,0.13);
        }
        #forgotStep2 {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: center;
            gap: 18px;
            padding-top: 12px;
            padding-bottom: 8px;
            background: rgba(10, 38, 71, 0.03);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(10,38,71,0.04);
        }
        #forgotStep2 .form_group {
            margin-bottom: 0;
            display: flex;
            flex-direction: row;
            gap: 12px;
            align-items: center;
            justify-content: center;
        }
        #userIdentity {
            font-size: 1.08rem;
            color: #0A2647;
            background: #f7fbff;
            border-radius: 6px;
            padding: 10px 16px;
            border: 1px solid #bfc9d1;
            margin-bottom: 0;
            display: inline-block;
            min-width: 180px;
            text-align: center;
        }
        #confirmIdentityBtn,
        #forgotBackBtn {
            background: linear-gradient(90deg, #0A2647 60%, #144272 100%);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 10px 18px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px rgba(10,38,71,0.07);
        }
        #confirmIdentityBtn:hover,
        #forgotBackBtn:hover {
            background: linear-gradient(90deg, #144272 60%, #0A2647 100%);
            box-shadow: 0 2px 8px rgba(10,38,71,0.13);
        }
        #forgotStep3 {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: center;
            gap: 18px;
            padding-top: 12px;
            padding-bottom: 8px;
            background: rgba(10, 38, 71, 0.03);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(10,38,71,0.04);
        }
        #forgotStep3 .form_group {
            margin-bottom: 0;
        }
        #forgotStep3 input[type="email"],
        #forgotStep3 input[type="password"] {
            font-size: 1.08rem;
            padding: 14px 16px;
            border: 1.5px solid #bfc9d1;
            border-radius: 7px;
            background: rgba(255,255,255,0.98);
            transition: border 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 2px rgba(10,38,71,0.04);
            margin-bottom: 10px;
        }
        #forgotStep3 input[type="email"]:focus,
        #forgotStep3 input[type="password"]:focus {
            border: 1.5px solid #0A2647;
            background: #f7fbff;
        }
        #forgotStep3 button[type="submit"] {
            background: linear-gradient(90deg, #0A2647 60%, #144272 100%);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 13px 0;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 6px;
            box-shadow: 0 2px 8px rgba(10,38,71,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        #forgotStep3 button[type="submit"]:hover {
            background: linear-gradient(90deg, #144272 60%, #0A2647 100%);
            box-shadow: 0 4px 16px rgba(10,38,71,0.13);
        }
        /* Optional: add a subtle label above the input */
        #forgotStep1 label {
            font-size: 1rem;
            color: #0A2647;
            margin-bottom: 6px;
            font-weight: 500;
            display: block;
        }
        /* Remove hiding of .forgot-step1-wrapper, etc. */
        .forgot-step1-wrapper,
        .forgot-title,
        .forgot-desc,
        .forgot-label,
        .forgot-next-btn {
            display: unset !important;
        }
        .forgot_password {
            text-align: right;
            margin-top: 4px;
        }
        .forgot_password a {
            color: #0A2647;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s, text-decoration 0.2s;
            padding: 2px 6px;
            border-radius: 4px;
            background: transparent;
            outline: none;
        }
        .forgot_password a:hover,
        .forgot_password a:focus {
            color: #144272;
            text-decoration: underline;
            background: #eaf1fa;
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
            <!-- Sign In header -->
            <h2 id="formHeader">Sign In</h2>
            <?php if (!empty($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form id="signInForm" method="POST" action="employee_sign_in.php">
                <div class="form_group">
                    <input type="text" id="username" name="username" placeholder="Enter your Username" required>
                </div>
                <div class="form_group">
                    <div class="input_wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your Password" required />
                        <span id="togglePassword" class="password_toggle_icon">
                            <img id="passwordToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                        </span>
                    </div>
                    <div class="forgot_password">
                        <a href="#" id="showForgotPassword">Forgot Password?</a>
                    </div>
                </div>
                <button type="submit">Sign In</button>
            </form>
            <form id="forgotPasswordForm" method="POST" action="forgot_password.php" style="display:none;">
                <div id="forgotStep1">
                    <div class="form_group">
                        <input type="text" id="forgot_username" name="username" placeholder="Enter your Username" required>
                    </div>
                    <button type="button" id="forgotNextBtn">Next</button>
                </div>
                <div id="forgotStep2" style="display:none;">
                    <div class="form_group">
                        <span id="userIdentity"></span>
                    </div>
                    <div class="form_group">
                        <button type="button" id="confirmIdentityBtn">Yes, that's me</button>
                        <button type="button" id="forgotBackBtn">No</button>
                    </div>
                </div>
                <div id="forgotStep3" style="display:none;">
                    <div class="form_group">
                        <input type="email" id="forgot_email" name="email" placeholder="Enter your Email" required readonly>
                    </div>
                    <div class="form_group">
                        <input type="password" id="forgot_new_password" name="new_password" placeholder="Enter New Password" required>
                    </div>
                    <div class="form_group">
                        <input type="password" id="forgot_confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
                    </div>
                    <button type="submit">Reset Password</button>
                </div>
            </form>
            <p class="footer">
                <span class="footer-content">
                    <span>Don't have an account?</span>
                    <a href="regEmployee.php">Sign Up</a>
                    <span class="footer-divider" id="footerDivider" style="display:none;">|</span>
                    <span id="backToSignInWrapper" style="display:none;">
                        <a href="#" id="backToSignInFooter">Back to Sign In</a>
                    </span>
                </span>
            </p>
        </div>
    </div>
    <script src="../../static/js/employee_sign_in.js"></script>
    <script>
        // Toggle between sign in and forgot password forms
        document.addEventListener('DOMContentLoaded', function () {
            var showForgot = document.getElementById('showForgotPassword');
            var backToSignInFooter = document.getElementById('backToSignInFooter');
            var backToSignInWrapper = document.getElementById('backToSignInWrapper');
            var signInForm = document.getElementById('signInForm');
            var forgotForm = document.getElementById('forgotPasswordForm');
            var forgotStep1 = document.getElementById('forgotStep1');
            var forgotStep2 = document.getElementById('forgotStep2');
            var forgotStep3 = document.getElementById('forgotStep3');
            var forgot_username = document.getElementById('forgot_username');
            var forgot_email = document.getElementById('forgot_email');
            var userIdentity = document.getElementById('userIdentity');
            var confirmIdentityBtn = document.getElementById('confirmIdentityBtn');
            var forgotBackBtn = document.getElementById('forgotBackBtn');
            var forgotNextBtn = document.getElementById('forgotNextBtn');
            var formHeader = document.getElementById('formHeader');

            function resetForgotPasswordSteps() {
                forgotStep1.style.display = "block";
                forgotStep2.style.display = "none";
                forgotStep3.style.display = "none";
                forgot_username.value = "";
                if (forgot_email) forgot_email.value = "";
                if (userIdentity) userIdentity.textContent = "";
                if (confirmIdentityBtn) confirmIdentityBtn.style.display = "";
            }

            function showFooterBackToSignIn(show) {
                if (backToSignInWrapper) {
                    backToSignInWrapper.style.display = show ? 'inline' : 'none';
                }
                var divider = document.getElementById('footerDivider');
                if (divider) {
                    divider.style.display = show ? 'inline-block' : 'none';
                }
            }

            if (showForgot) {
                showForgot.addEventListener('click', function(e) {
                    e.preventDefault();
                    signInForm.style.display = 'none';
                    forgotForm.style.display = 'block';
                    resetForgotPasswordSteps();
                    if (formHeader) formHeader.textContent = "Forgot Password";
                    showFooterBackToSignIn(true);
                });
            }
            if (backToSignInFooter) {
                backToSignInFooter.addEventListener('click', function(e) {
                    e.preventDefault();
                    forgotForm.style.display = 'none';
                    signInForm.style.display = 'block';
                    resetForgotPasswordSteps();
                    if (formHeader) formHeader.textContent = "Sign In";
                    showFooterBackToSignIn(false);
                });
            }

            // Forgot password step logic
            if (forgotNextBtn) {
                forgotNextBtn.addEventListener('click', function() {
                    var username = forgot_username.value.trim();
                    if (!username) return;
                    userIdentity.textContent = "Loading...";
                    forgotStep2.style.display = "block";
                    forgotStep1.style.display = "none";
                    fetch('forgot_password_lookup.php?username=' + encodeURIComponent(username))
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.email) {
                                userIdentity.textContent = "Is this you? " + data.username + " (" + data.email + ")";
                                forgot_email.value = data.email;
                                confirmIdentityBtn.style.display = "";
                            } else {
                                userIdentity.textContent = "User not found.";
                                confirmIdentityBtn.style.display = "none";
                            }
                        })
                        .catch(() => {
                            userIdentity.textContent = "Error fetching user.";
                            confirmIdentityBtn.style.display = "none";
                        });
                });
            }
            if (confirmIdentityBtn) {
                confirmIdentityBtn.addEventListener('click', function() {
                    forgotStep2.style.display = "none";
                    forgotStep3.style.display = "block";
                });
            }
            if (forgotBackBtn) {
                forgotBackBtn.addEventListener('click', function() {
                    forgotStep2.style.display = "none";
                    forgotStep1.style.display = "block";
                    if (forgot_username) forgot_username.focus();
                });
            }
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
        // Show registration success if redirected from registration
        (function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('reg') === '1') {
                showPopup('Registration successful! Please sign in.', 'success');
            }
        })();
        <?php if (!empty($error)): ?>
            showPopup(<?php echo json_encode($error); ?>, 'error');
        <?php endif; ?>
    </script>
</body>
</html>
