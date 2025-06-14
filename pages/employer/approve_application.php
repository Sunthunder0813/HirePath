<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: employee_sign_in.php");
    exit();
}

// Include database connection
include '../../db_connection/connection.php';
$conn = OpenConnection();

// Get application ID from the query string
if (isset($_GET['application_id'])) {
    $application_id = intval($_GET['application_id']);

    // Update the application status to "accepted"
    $query = "UPDATE `applications` SET `status` = 'accepted' WHERE `application_id` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $application_id);
    $stmt->execute();

    // Fetch applicant's email, username, job title, company_name, and company_cover from users table (employer)
    $stmt = $conn->prepare("SELECT u.email, u.username, j.title, e.company_name, e.company_cover FROM applications a JOIN users u ON a.job_seeker_id = u.user_id JOIN jobs j ON a.job_id = j.job_id JOIN users e ON j.employer_id = e.user_id WHERE a.application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $stmt->bind_result($email, $username, $job_title, $company_name, $company_cover);
    $stmt->fetch();
    $stmt->close();

    // Use company_cover as the image if available, otherwise leave empty
    $company_img_url = '';
    if (!empty($company_cover)) {
        // If company_cover is a relative path, prepend the base URL
        if (strpos($company_cover, 'http') === 0) {
            $company_img_url = $company_cover;
        } else {
            $company_img_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/Hirepath/$company_cover";
        }
    }

    // Send approval email
    require_once 'application_email.php';
    $subject = $company_name;
    $body = [
        'username' => $username,
        'job_title' => $job_title,
        'company_name' => $company_name,
        'company_img_url' => $company_img_url
    ];
    sendEmail($email, $subject, $body, $username);

    // Redirect back to applications page
    header("Location: view_applications.php?message=Application accepted and email sent successfully");
    exit();
} else {
    echo "Invalid request.";
}
?>
