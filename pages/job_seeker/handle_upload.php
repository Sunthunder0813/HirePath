<?php
session_start();
include '../../db_connection/connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: sign_in.php'); // Redirect to sign-in if not logged in
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
    $job_seeker_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session
    $status = 'Pending'; // Default status
    $applied_at = date('Y-m-d H:i:s'); // Current timestamp

    // Check if a file is uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['resume']['tmp_name'];
        $file_name = basename($_FILES['resume']['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        // Validate file type
        $allowed_extensions = ['pdf', 'docx'];
        if (!in_array(strtolower($file_ext), $allowed_extensions)) {
            die("Invalid file type. Only PDF and DOCX files are allowed.");
        }

        // Generate a unique file name and save the file
        $upload_dir = '../../job_seeker/resumes/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
        }
        $unique_file_name = uniqid('resume_', true) . '.' . $file_ext;
        $file_path = $upload_dir . $unique_file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert application details into the database
            $conn = OpenConnection();
            $stmt = $conn->prepare("INSERT INTO applications (job_id, job_seeker_id, resume_link, status, applied_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $job_id, $job_seeker_id, $file_path, $status, $applied_at);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header("Location: application_status.php?status=success"); // Pass success status
                exit();
            } else {
                $stmt->close();
                $conn->close();
                header("Location: application_status.php?status=failure"); // Pass failure status
                exit();
            }
        } else {
            die("Failed to upload the file.");
        }
    } else {
        die("No file uploaded or an error occurred during the upload.");
    }
} else {
    die("Invalid request method.");
}
?>