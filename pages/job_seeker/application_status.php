<?php
$status = $_GET['status'] ?? 'failure';

session_start();
include '../../db_connection/connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: sign_in.php'); // Redirect to sign-in if not logged in
    exit();
}

// Open the database connection
$conn = OpenConnection(); // Call the function to open the connection

// Fetch user details (username and email) in a single query
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status</title>
    <style>
        html, body {
    height: 100%; /* Ensure full height */
    overflow: hidden; /* Prevent scrolling */
}

body {
    font-family: 'Poppins', sans-serif; /* Match nav font */
    margin: 0;
    background-color: #f4f4f4;
    padding-top: 60px; /* Account for fixed nav */
    color: #333; /* Consistent text color */
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
    display: flex; /* Use flexbox for alignment */
    align-items: center; /* Vertically center the logo */
    text-decoration: none;
    margin-left: 10px;
    color: white;
    font-size: 1.5em; /* Slightly larger for better emphasis */
    font-weight: 600; /* Medium weight for a modern look */
    font-family: 'Poppins', sans-serif;
    letter-spacing: 1px; /* Subtle spacing for a refined appearance */
    transition: color 0.3s ease;
}

.logo a img {
    display: block;
    height: 40px; /* Fixed height */
    margin-left: 10px; /* Maintain spacing */
    transition: transform 0.3s ease; /* Add hover effect */
    object-fit: contain; /* Ensure the image fits within the height */
}


.nav-links {
list-style: none;
display: flex;
align-items: center;
padding: 0;
margin: 0;
gap: 20px;
}

.nav-links li {
display: inline;
}

.nav-links a {
text-decoration: none;
color: white;
padding: 10px 15px;
border-radius: 5px;
transition: background 0.3s ease, transform 0.2s ease;
font-weight: bold;
}

.nav-links a:hover {
background: #555;
}

.sign-out-button {
padding: 10px 15px;
border-radius: 5px;
text-decoration: none;
font-weight: bold;
transition: background 0.3s ease, transform 0.2s ease;
}
.profile-dropdown {
position: relative;
}

.profile-dropdown > a {
display: inline-block;
padding: 10px 15px;
text-decoration: none;
color: white;
cursor: pointer;
}

.profile-dropdown .dropdown-menu {
display: none;
position: absolute;
top: 145%;
left: 0;
background-color: #ffffff;
box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Enhanced shadow for depth */
list-style: none;
padding: 10px 0; /* Added padding for spacing */
margin: 0;
border-radius: 8px; /* Rounded corners */
min-width: 100%; /* Match the width of the email container */
z-index: 1000;
}

.profile-dropdown:hover .dropdown-menu,
.profile-dropdown.active .dropdown-menu {
display: block;
}
.dropdown-menu.show {
    display: block;
}

.profile-dropdown .dropdown-menu li {
border-bottom: 1px solid #f0f0f0;
text-align: center; /* Center the text */
}

.profile-dropdown .dropdown-menu li a {
text-decoration: none;
color: #333;
font-size: 14px; /* Slightly larger font for readability */
display: block;
transition: background 0.3s ease, color 0.3s ease;
text-align: center; /* Ensure the link text is centered */
}

.profile-dropdown .dropdown-menu li a:hover {
background-color: #f8f9fa;
color: #007BFF;
}

/*====================================================*/

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 80vh; /* Increase height for a larger appearance */
    padding: 40px; /* Increase padding for more spacing */
    background: linear-gradient(135deg, #f4f4f4, #e8e8e8); /* Subtle gradient background */
}

.status-message {
    text-align: center;
    padding: 80px; /* Increase padding for a larger appearance */
    background: #ffffff;
    border-radius: 16px; /* Slightly more rounded corners */
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); /* Enhanced shadow for depth */
    margin: 0 auto; /* Center horizontally */
    max-width: 1000px; /* Increase width for better visibility */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add hover effects */
}

.status-message h2 {
    font-size: 2.5rem; /* Increase font size for better emphasis */
    margin-bottom: 25px;
    font-weight: bold; /* Make the heading more prominent */
}

/* Success status */
.status-message.success h2 {
    color: #28a745; /* Green for success */
}

/* Already applied status */
.status-message.already-applied h2 {
    color: #ffc107; /* Yellow for already applied */
}

/* Failure status */
.status-message.failure h2 {
    color: #dc3545; /* Red for failure */
}

.status-message p {
    font-size: 1.2rem; /* Slightly larger font for readability */
    color: #555;
    margin-bottom: 25px;
    line-height: 1.8; /* Improve readability */
}

.status-message .button {
    display: inline-block;
    padding: 15px 30px; /* Larger button for better visibility */
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 10px; /* Slightly more rounded corners */
    font-size: 1.1rem; /* Increase font size for better visibility */
    font-weight: bold;
    transition: background 0.3s ease, transform 0.2s ease;
}

.status-message .button:hover {
    background: #0056b3;
    transform: scale(1.05); /* Slightly larger zoom effect */
}

.status-message .button:active {
    transform: scale(0.98);
}
    </style>
</head>
<body>
<nav>
<p class="logo">
        <a href="../../index.php">
            <img src="../../static/img/icon/logo.png" alt="Hire Path Logo">
        </a>
    </p>
    <ul class="nav-links">
        <li><a href="employee_sign_in.php">Post a Job</a></li>
        <?php if (!empty($username)): ?>
            <li><a href="application.php">Application</a></li>
        <?php endif; ?>
        <?php if (!empty($username)): ?>
            <li class="profile-dropdown">
                <a><?php echo htmlspecialchars($email); ?> <span style="font-size: 1em;">&#9660;</span></a> 
                <ul class="dropdown-menu">
                    <li><a href="../../logout.php" class="sign-out-button">Sign Out</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li><a href="sign_in.php">Sign In</a></li>
        <?php endif; ?>
    </ul>
</nav>
<div class="container">
    <div class="status-message 
        <?php 
            if ($status === 'success') echo 'success'; 
            elseif (isset($_GET['message']) && $_GET['message'] === 'already_applied') echo 'already-applied'; 
            else echo 'failure'; 
        ?>">
        <?php if ($status === 'success'): ?>
            <h2>Application Submitted Successfully!</h2>
            <p>Your application has been submitted. We will get back to you soon.</p>
            <a href="../../index.php" class="button">Go to Home</a>
        <?php elseif (isset($_GET['message']) && $_GET['message'] === 'already_applied'): ?>
            <h2>Application Already Submitted</h2>
            <p>You have already applied for this job. Please check your application status.</p>
            <a href="application.php" class="button">View Applications</a>
        <?php else: ?>
            <h2>Application Submission Failed</h2>
            <p>There was an issue submitting your application. Please try again.</p>
            <a href="../../index.php" class="button">Go to Home</a>
        <?php endif; ?>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const profileDropdown = document.querySelector('.profile-dropdown');

    profileDropdown.addEventListener('click', (e) => {
        e.preventDefault();
        profileDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('active');
        }
    });

    // Prevent dropdown menu from closing when clicking inside it
    const dropdownMenu = document.querySelector('.profile-dropdown .dropdown-menu');
    dropdownMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});
</script>
</body>
</html>
