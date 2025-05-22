<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: employee_sign_in.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);

include '../../db_connection/connection.php';

// $has_company = false;
$conn = OpenConnection();
$stmt = $conn->prepare("SELECT company_name FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($company_name);
// if ($stmt->fetch() && !empty($company_name)) {
//     $has_company = true;
// }
$stmt->close();

// if (!$has_company) {
//     header("Location: company_profile.php?assign_company=1");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/icon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../static/css/employee_dashboard.css">
    <script src="js/get_pending_count.js" defer></script>
    <title>Employer Dashboard</title>
    <style>
        
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="Employee_dashboard.php" class="logo">Employee Portal</a>
        <ul class="nav_links">
            <li><a href="post_job.php">Post Job</a></li>
            <li>
                <div class="applications_container">
                    <a href="view_applications.php">Applications</a>
                    <span id="navbar_badge" class="nav_badge" style="display: none;">0</span>
                </div>
            </li>
            <li><a href="company_profile.php">Company Profile</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="dashboard_links">
            <div class="dashboard_card">
                <h2>Post a Job</h2>
                <p>Create new job postings to attract top talent.</p>
                <a href="post_job.php">
                Post Job
                </a>
            </div>
            <div class="dashboard_card">
                <h2>View Applications</h2>
                <p>Review and manage applications from candidates.</p>
                <a href="view_applications.php">View Applications</a>
            </div>
            <div class="dashboard_card">
                <h2>View Job Listings</h2>
                <p>Manage your job postings by status.</p>
                <a href="view_jobs.php">View Jobs</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> JobPortal. All rights reserved.</p>
    </footer>

</body>
</html>
