<?php
session_start();
include '../../db_connection/connection.php';
include 'send_otp.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit();
}
$conn = OpenConnection(); 

$username = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
}

$error = '';
$success = '';
$otp_status = ''; // Add this line

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_otp'])) {
        $otp = rand(100000, 999999); 
        $_SESSION['otp'] = $otp; 

        $subject = "Your OTP for Password Change";
        $error = null;
        if (sendEmail($email, $subject, $otp, $username, $error)) {
            $otp_status = "OTP sent to your email.";
            // Removed alert
        } else {
            $otp_status = "Failed to send OTP. Please try again.";
            // Removed alert
        }
    }

    if (isset($_POST['change_with_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New password and confirm password do not match.";
        } else {
            $proper = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?=.*\d).{8,}$/';
            if (!preg_match($proper, $new_password)) {
                $error = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, 1 special character, and 1 number.";
            } else {
                $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->bind_result($hashed_password);
                $stmt->fetch();
                $stmt->close();

                if (!password_verify($current_password, $hashed_password)) {
                    $error = "Current password is incorrect.";
                } else {
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $new_hashed_password, $user_id);

                    if ($stmt->execute()) {
                        $success = "Password updated successfully.";
                    } else {
                        $error = "Failed to update password. Please try again.";
                    }
                    $stmt->close();
                }
            }
        }
    } elseif (isset($_POST['change_with_otp'])) {
        $otp = $_POST['otp'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($otp) || empty($new_password) || empty($confirm_password)) {
            $error = "All fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New password and confirm password do not match.";
        } else {
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?=.*\d).{8,}$/';
            if (!preg_match($regex, $new_password)) {
                $error = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, 1 special character, and 1 number.";
            } else {
                if (!isset($_SESSION['otp']) || $otp != $_SESSION['otp']) {
                    $error = "Invalid OTP.";
                } else {
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                    $stmt->bind_param("si", $new_hashed_password, $user_id);

                    if ($stmt->execute()) {
                        $success = "Password updated successfully.";
                        unset($_SESSION['otp']); 
                    } else {
                        $error = "Failed to update password. Please try again.";
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/icon/favicon.png" type="image/x-icon">
    

    <title>Change Password - Job Portal</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            padding-top: 60px;
        }
        .container {
            background: linear-gradient(135deg, #ffffff, #f7f7f7);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 500px;
            margin: 120px auto;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
        }
        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        h2 {
            margin-bottom: 20px;
            font-size: 2.2em;
            color: #333;
            font-weight: bold;
        }
        nav {
            background: #333;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .logo a {
            display: flex;
            align-items: center;
            text-decoration: none;
            margin-left: 10px;
            color: white;
            font-size: 1.5em;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 1px;
            transition: color 0.3s ease;
        }
        .logo a img {
            display: block;
            height: 40px;
            margin-left: 10px;
            transition: transform 0.3s ease;
            object-fit: contain;
        }
        .nav_links {
            list-style: none;
            display: flex;
            align-items: center;
            padding: 0;
            margin: 0;
            gap: 20px;
        }
        .nav_links li {
            display: inline;
        }
        .nav_links a {
            text-decoration: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s ease, transform 0.2s ease;
            font-weight: bold;
        }
        .nav_links a:hover {
            background: #555;
        }
        .sign_out_button {
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .profile_dropdown {
            position: relative;
        }
        .profile_dropdown > a {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            color: white;
            cursor: pointer;
        }
        .profile_dropdown .dropdown_menu {
            display: none;
            position: absolute;
            top: 145%;
            left: 0;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            list-style: none;
            padding: 10px 0;
            margin: 0;
            border-radius: 8px;
            min-width: 100%;
            z-index: 1000;
        }
        .profile_dropdown:hover .dropdown_menu,
        .profile_dropdown.active .dropdown_menu {
            display: block;
        }
        .profile_dropdown .dropdown_menu li {
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
        }
        .profile_dropdown .dropdown_menu li a {
            text-decoration: none;
            color: #333;
            font-size: 14px;
            display: block;
            transition: background 0.3s ease, color 0.3s ease;
            text-align: center;
        }
        .profile_dropdown .dropdown_menu li a:hover {
            background-color: #f8f9fa;
            color: #007BFF;
        }
        .change_password_container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            margin: 100px auto;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        h2 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .tab_buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .tab_buttons button {
            flex: 1;
            padding: 12px;
            border: none;
            background: #f8f9fa; 
            color: #333;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease, color 0.3s ease;
            border-radius: 5px;
            margin: 0 5px;
        }
        .tab_buttons button.active {
            background: #333; 
            color: #fff; 
        }
        .password_form {
            display: none;
        }
        .password_form.active {
            display: block;
        }
        .password_form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #495057;
            text-align: left;
        }
        .password_form input,
        .password_form button {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .password_form input {
            background: #f8f9fa; 
        }
        .password_form button {
            background: #333; 
            color: #fff; 
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .password_form button:hover {
            background: #555; 
        }
        .otp_row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .otp_row input {
            flex: 2;
            padding: 12px;
            border: 1px solid #ced4da; 
            border-radius: 5px;
            font-size: 14px;
            background: #f8f9fa; 
        }
        .otp_row button {
            flex: 1;
            padding: 12px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .otp_row button:hover {
            background: #555; 
        }
        /* Password input wrapper and toggle icon styles */
        .input_wrapper {
            position: relative;
            width: 100%;
        }
        .input_wrapper input[type="password"],
        .input_wrapper input[type="text"] {
            width: 100%;
            padding-right: 40px;
        }
        .password_toggle_icon {
            position: absolute;
            right: 10px;
            top: 38%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            z-index: 2;
            width: 24px;
            /* Remove height: 100%; and display: flex; align-items: center; */
        }
        .password_toggle_icon img {
            width: 24px;
            height: 24px;
            pointer-events: none;
            display: block;
        }
        .password-match-indicator {
            min-height: 18px;
            font-size: 13px;
            text-align: left;
            margin-bottom: 10px;
            margin-top: -10px;
            padding-left: 2px;
        }
        .otp-info {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 15px;
        }
        .otp-label {
            color: #555;
            font-weight: 500;
        }
        .otp-email {
            color: #007BFF;
            font-weight: bold;
            word-break: break-all;
        }
        /* Popup Notification Styles */
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
    <nav>
        <p class="logo">
            <a href="../../index.php">
                <img src="../../static/img/icon/logo.png" alt="Hire Path Logo">
            </a>
        </p>
        <ul class="nav_links">
            <li><a href="pages/employer/employee_sign_in.php">Post a Job</a></li>
            <?php if (!empty($username)): ?>
                <li><a href="application.php">Application</a></li>
            <?php endif; ?>
            <?php if (!empty($username)): ?>
                <li class="profile_dropdown">
                    <a><?php echo htmlspecialchars($email); ?> <span style="font-size: 1em;">&#9660;</span></a>
                    <ul class="dropdown_menu">
                        <li><a href="jobseeker_changepass.php" class="sign_out_button">Change Password</a></li>
                        <li><a href="../../logout.php" class="sign_out_button">Sign Out</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="sign_in.php">Sign In</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="change_password_container">
        <h2>Change Password</h2>

        <div class="tab_buttons">
            <button id="tab_current" class="active">With Current Password</button>
            <button id="tab_otp">With OTP</button>
        </div>

        <form id="form_current" class="password_form active" method="POST" action="">
            <label for="currentPassword">Current Password</label>
            <div class="input_wrapper">
                <input type="password" id="currentPassword" name="current_password" placeholder="Enter current password" required>
                <span class="password_toggle_icon" id="toggleCurrentPassword">
                    <img id="currentPasswordToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                </span>
            </div>

            <label for="newPassword1">New Password</label>
            <div class="input_wrapper">
                <input type="password" id="newPassword1" name="new_password" placeholder="Enter new password" required>
                <span class="password_toggle_icon" id="toggleNewPassword1">
                    <img id="newPassword1ToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                </span>
            </div>

            <label for="confirmPassword1">Confirm New Password</label>
            <div class="input_wrapper">
                <input type="password" id="confirmPassword1" name="confirm_password" placeholder="Confirm new password" required>
                <span class="password_toggle_icon" id="toggleConfirmPassword1">
                    <img id="confirmPassword1ToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                </span>
            </div>
            <div id="matchIndicator1" class="password-match-indicator"></div>
            <button type="submit" name="change_with_password" class="update-button">Update Password</button>
        </form>

        <form id="form_otp" class="password_form" method="POST" action="">
            <div class="otp-info">
                <span class="otp-label">OTP will be sent to:</span>
                <span class="otp-email"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="otp_row">
                <input type="text" id="otp" name="otp" placeholder="Enter OTP" required>
                <button type="button" id="sendOtpButton">Send OTP</button>
            </div>

            <label for="newPassword2">New Password</label>
            <div class="input_wrapper">
                <input type="password" id="newPassword2" name="new_password" placeholder="Enter new password" required>
                <span class="password_toggle_icon" id="toggleNewPassword2">
                    <img id="newPassword2ToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                </span>
            </div>

            <label for="confirmPassword2">Confirm New Password</label>
            <div class="input_wrapper">
                <input type="password" id="confirmPassword2" name="confirm_password" placeholder="Confirm new password" required>
                <span class="password_toggle_icon" id="toggleConfirmPassword2">
                    <img id="confirmPassword2ToggleImage" src="../../static/img/icon/hidden.png" alt="Toggle Password" draggable="false">
                </span>
            </div>
            <div id="matchIndicator2" class="password-match-indicator"></div>
            <button type="submit" name="change_with_otp" class="update-button">Update Password</button>
        </form>
    </div>

    <script src="../../static/js/jobseeker_changepass.js"></script>
    <script>
        // Password toggle logic (similar to sign_up.js)
        function setupPasswordToggle(inputId, toggleId, imageId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            const image = document.getElementById(imageId);
            if (input && toggle && image) {
                toggle.addEventListener('click', function() {
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;
                    image.src = type === 'password'
                        ? '../../static/img/icon/hidden.png'
                        : '../../static/img/icon/visible.png';
                });
            }
        }
        setupPasswordToggle('currentPassword', 'toggleCurrentPassword', 'currentPasswordToggleImage');
        setupPasswordToggle('newPassword1', 'toggleNewPassword1', 'newPassword1ToggleImage');
        setupPasswordToggle('confirmPassword1', 'toggleConfirmPassword1', 'confirmPassword1ToggleImage');
        setupPasswordToggle('newPassword2', 'toggleNewPassword2', 'newPassword2ToggleImage');
        setupPasswordToggle('confirmPassword2', 'toggleConfirmPassword2', 'confirmPassword2ToggleImage');

        // Password match indicator logic
        function setupPasswordMatchIndicator(newId, confirmId, indicatorId) {
            const newInput = document.getElementById(newId);
            const confirmInput = document.getElementById(confirmId);
            const indicator = document.getElementById(indicatorId);
            function checkMatch() {
                if (!confirmInput.value) {
                    indicator.textContent = '';
                    return;
                }
                if (newInput.value === confirmInput.value) {
                    indicator.textContent = 'Passwords match';
                    indicator.style.color = 'green';
                } else {
                    indicator.textContent = 'Passwords do not match';
                    indicator.style.color = 'red';
                }
            }
            newInput.addEventListener('input', checkMatch);
            confirmInput.addEventListener('input', checkMatch);
        }
        setupPasswordMatchIndicator('newPassword1', 'confirmPassword1', 'matchIndicator1');
        setupPasswordMatchIndicator('newPassword2', 'confirmPassword2', 'matchIndicator2');

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

        // Show OTP send status
        <?php if (!empty($otp_status)): ?>
            showPopup(<?php echo json_encode($otp_status); ?>, <?php echo strpos($otp_status, 'Failed') === false ? "'success'" : "'error'"; ?>);
        <?php endif; ?>

        // Show password change success/failure
        <?php if (!empty($success)): ?>
            showPopup(<?php echo json_encode($success); ?>, 'success');
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            showPopup(<?php echo json_encode($error); ?>, 'error');
        <?php endif; ?>
    </script>
</body>
</html>
