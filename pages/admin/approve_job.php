<?php
session_start();


if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../admin_sign_in.php");
    exit();
}


include '../../db_connection/connection.php';
$conn = OpenConnection();


date_default_timezone_set('Asia/Manila');


if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);
    $created_at = date('Y-m-d H:i:s');

    
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
