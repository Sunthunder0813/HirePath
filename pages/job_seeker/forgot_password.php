<?php
session_start();
include '../../db_connection/connection.php';

$forgot_success = '';
$forgot_error = '';
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['otp_email'], $_POST['otp_code'], $_POST['new_password'], $_POST['confirm_new_password'])
) {
    $otp_email = trim($_POST['otp_email']);
    $otp_code = trim($_POST['otp_code']);
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if (
        !isset($_SESSION['otp_email'], $_SESSION['otp_code'], $_SESSION['otp_time']) ||
        $_SESSION['otp_email'] !== $otp_email ||
        $_SESSION['otp_code'] != $otp_code ||
        (time() - $_SESSION['otp_time']) > 600
    ) {
        $forgot_error = "Invalid or expired OTP.";
    } elseif ($new_password !== $confirm_new_password) {
        $forgot_error = "Passwords do not match.";
    } else {
        $valid = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?=.*\d).{8,}$/';
        if (!preg_match($valid, $new_password)) {
            $forgot_error = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, 1 special character, and 1 number.";
        } else {
            $conn = OpenConnection();
            if ($conn) {
                // Check if user exists before updating
                $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
                $userExists = false;
                if ($checkStmt) {
                    $checkStmt->bind_param("s", $otp_email);
                    $checkStmt->execute();
                    $checkStmt->store_result();
                    if ($checkStmt->num_rows > 0) {
                        $userExists = true;
                    }
                    $checkStmt->close();
                }
                if ($userExists) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
                    if ($stmt) {
                        $stmt->bind_param("ss", $hashed_password, $otp_email);
                        $stmt->execute();
                        // Remove strict affected_rows check: allow password update even if same as before
                        if ($stmt->error) {
                            $forgot_error = "Failed to reset password. Please try again.";
                        } else {
                            $forgot_success = "Password has been reset successfully. You can now sign in.";
                            unset($_SESSION['otp_email'], $_SESSION['otp_code'], $_SESSION['otp_time']);
                        }
                        $stmt->close();
                    } else {
                        $forgot_error = "Database error.";
                    }
                } else {
                    $forgot_error = "No user found with that email.";
                }
                CloseConnection($conn);
            } else {
                $forgot_error = "Database connection failed.";
            }
        }
    }
}

header('Content-Type: application/json');
if ($forgot_success) {
    echo json_encode(['success' => true, 'message' => $forgot_success]);
} else {
    echo json_encode(['success' => false, 'error' => $forgot_error]);
}
exit;
?>
