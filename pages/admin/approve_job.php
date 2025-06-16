<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin_sign_in.php");
    exit();
}

// Include database connection
include '../../db_connection/connection.php';
$conn = OpenConnection();

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Check if job_id is provided
if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);
    $created_at = date('Y-m-d H:i:s');

    // Update job status to 'approved' and set created_at to NOW()
    $query = "UPDATE `jobs` SET `status` = 'approved', `created_at` = ? WHERE `job_id` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $created_at, $job_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Job approved successfully.";
    } else {
        $_SESSION['error'] = "Failed to approve the job.";
    }

    $stmt->close();
} else {
    $_SESSION['error'] = "Invalid job ID.";
}

$conn->close();
header("Location: admin_dashboard.php?tab=pending");
exit();
