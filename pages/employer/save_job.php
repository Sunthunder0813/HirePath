<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: employee_sign_in.php");
    exit();
}

// Include database connection
include '../../db_connection/connection.php'; // Replace with your actual database connection file
$conn = OpenConnection();

// Get the logged-in user's username
$username = $_SESSION['username'];

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $job_title = htmlspecialchars($_POST['job_title']);
    $job_description = htmlspecialchars($_POST['job_description']);
    $job_category = htmlspecialchars($_POST['final_category']); // Use final_category instead of job_category
    $job_salary = htmlspecialchars($_POST['job_salary']);
    $job_location = htmlspecialchars($_POST['job_location']);
    $job_skills = htmlspecialchars($_POST['job_skills']);
    $job_education = htmlspecialchars($_POST['job_education']);
    $status = "pending"; // Default status for a new job (goes to admin for review)

    // Fetch the employer ID and company name from the database
    $query = "SELECT user_id, company_name FROM `users` WHERE `username` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $employer = $result->fetch_assoc();
    $employer_id = $employer['user_id'];
    $company_name = $employer['company_name'];

    // Get current date/time in PHP
    $created_at = date('Y-m-d H:i:s');

    // Insert job details into the database
    $sql = "INSERT INTO `jobs` (`employer_id`, `title`, `description`, `category`, `salary`, `location`, `status`, `created_at`, `company_name`, `skills`, `education`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssdssssss", $employer_id, $job_title, $job_description, $job_category, $job_salary, $job_location, $status, $created_at, $company_name, $job_skills, $job_education);

    if ($stmt->execute()) {
        // Redirect to the dashboard with a success message
        header("Location: Employee_dashboard.php?success=1");
        exit();
    } else {
        // Redirect back to the post job page with an error message
        header("Location: post_job.php?error=1");
        exit();
    }
}
?>
