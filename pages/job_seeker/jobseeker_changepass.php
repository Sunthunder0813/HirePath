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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_otp'])) {
        $otp = rand(100000, 999999); 
        $_SESSION['otp'] = $otp; 

        $subject = "Your OTP for Password Change";
        $error = null;
        if (sendEmail($email, $subject, $otp, $username, $error)) {
            $success = "OTP sent to your email.";
            echo "<script>alert('OTP has been sent to your email.');</script>";
        } else {
            $error = "Failed to send OTP. Please try again.";
            echo "<script>alert('Failed to send OTP. Please try again.');</script>";
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
    <link rel="stylesheet" href="../../static/css/jobseeker_changepass.css">

    <title>Change Password - Job Portal</title>
</head>
<body>
    <nav>
        <p class="logo">
            <a href="../../index.php">
                <img src="../../static/img/icon/logo.png" alt="Hire Path Logo">
            </a>
        </p>
        <ul class="nav_links">
            <li><a href="employee_sign_in.php">Post a Job</a></li>
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
            <input type="password" id="currentPassword" name="current_password" placeholder="Enter current password" required>

            <label for="newPassword1">New Password</label>
            <input type="password" id="newPassword1" name="new_password" placeholder="Enter new password" required>

            <label for="confirmPassword1">Confirm New Password</label>
            <input type="password" id="confirmPassword1" name="confirm_password" placeholder="Confirm new password" required>

            <button type="submit" name="change_with_password" class="update-button">Update Password</button>
        </form>

        <form id="form_otp" class="password_form" method="POST" action="">
            <div class="otp_row">
                <input type="text" id="otp" name="otp" placeholder="Enter OTP" required>
                <button type="button" id="sendOtpButton">Send OTP</button>
            </div>

            <label for="newPassword2">New Password</label>
            <input type="password" id="newPassword2" name="new_password" placeholder="Enter new password" required>

            <label for="confirmPassword2">Confirm New Password</label>
            <input type="password" id="confirmPassword2" name="confirm_password" placeholder="Confirm new password" required>

            <button type="submit" name="change_with_otp" class="update-button">Update Password</button>
        </form>
    </div>

    <script src="../../static/js/jobseeker_changepass.js"></script>
    <script>
        <?php if (!empty($success)): ?>
            alert("<?php echo ($success); ?>");
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            alert("<?php echo ($error); ?>");
        <?php endif; ?>
    </script>
</body>
</html>
