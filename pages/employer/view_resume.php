<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: employee_sign_in.php");
    exit();
}

if (!isset($_GET['application_id'])) {
    echo "Invalid request.";
    exit();
}

$application_id = intval($_GET['application_id']);

include '../../db_connection/connection.php';
$conn = OpenConnection();

// Fetch resume link and applicant info, including current status
$stmt = $conn->prepare("SELECT a.resume_link, u.email, u.username, j.title, e.company_name, a.status
    FROM applications a
    JOIN users u ON a.job_seeker_id = u.user_id
    JOIN jobs j ON a.job_id = j.job_id
    JOIN users e ON j.employer_id = e.user_id
    WHERE a.application_id = ?");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$stmt->bind_result($resume_link, $email, $username, $job_title, $company_name, $status);
if (!$stmt->fetch()) {
    echo "Resume not found.";
    exit();
}
$stmt->close();

// If status is 'pending', set to 'reviewed' and send reviewed email
if ($status === 'pending') {
    $stmt = $conn->prepare("UPDATE applications SET status = 'reviewed' WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();

    require_once 'application_email.php';
    $subject = $company_name;
    $body = [
        'username' => $username,
        'job_title' => $job_title,
        'company_name' => $company_name,
        'reviewed' => true
    ];
    sendEmail($email, $subject, $body, $username);
}

// Display the resume (PDF, DOC, etc.)
if (!empty($resume_link)) {
    // Adjust path to point to the correct resumes directory
    $resume_path = "../../job_seeker/resumes/" . basename($resume_link);
    if (file_exists($resume_path)) {
        header('Content-Type: application/pdf');
        readfile($resume_path);
        exit();
    } else {
        echo "Resume file not found.";
        exit();
    }
} else {
    echo "No resume uploaded.";
    exit();
}
