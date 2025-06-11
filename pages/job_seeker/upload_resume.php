<?php
session_start();
include '../../db_connection/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: sign_in.php');
    exit();
}

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

if ($job_id <= 0) {
    die("Invalid or missing job ID.");
}

$conn = OpenConnection(); 

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
}

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM applications WHERE job_id = ? AND job_seeker_id = ?");
$stmt->bind_param("ii", $job_id, $user_id);
$stmt->execute();
$stmt->bind_result($application_count);
$stmt->fetch();
$stmt->close();

if ($application_count > 0) {
    header("Location: application_status.php?status=failure&message=already_applied");
    exit();
}

$job_details = null;
if ($job_id > 0) {
    $stmt = $conn->prepare("SELECT title, description, salary, location, company_name, skills, education FROM jobs WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $stmt->bind_result($title, $description, $salary, $location, $company_name, $skills, $education);
    if ($stmt->fetch()) {
        $job_details = [
            'title' => $title,
            'description' => $description,
            'salary' => $salary,
            'location' => $location,
            'company_name' => $company_name,
            'skills' => $skills,
            'education' => $education
        ];
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Resume</title>
    <link rel="stylesheet" href="css/j_upload.css">
</head>
<body>
<nav>
    <p class="logo">
        <a href="index.php">
            <img src="img/icon/logo.png" alt="Hire Path Logo">
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
        <div class="left-section">
            <h2>Upload Your Resume</h2>
            <form id="upload-form" action="handle_upload.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                <label for="resume">Select your resume (PDF, DOCX):</label>
                <input type="file" name="resume" id="resume" accept=".pdf,.docx" required>
                <button type="submit" aria-label="Submit your resume">Submit</button>
            </form>
            <div id="preview" style="display: none;">
                <h3>File Preview:</h3>
                <iframe id="file-preview"></iframe>
                <p id="file-message" class="centered-message"></p>
            </div>
        </div>
        <div class="right-section">
            <h3>Job Details</h3>
            <?php if ($job_details): ?>
                <div>
                    <h4><?php echo htmlspecialchars($job_details['title']); ?></h4>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($job_details['company_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job_details['location']); ?></p>
                    <p><strong>Salary:</strong> ₱<?php echo htmlspecialchars(number_format($job_details['salary'], 2)); ?></p>
                    <p><strong>Skills Required:</strong> <?php echo htmlspecialchars($job_details['skills']); ?></p>
                    <p><strong>Education Required:</strong> <?php echo htmlspecialchars($job_details['education']); ?></p>
                    <button id="toggle-details" aria-label="Toggle full job details">Show Full Details</button>
                    <div id="full-details" style="display: none;">
                        <p><strong>Description:</strong></p>
                        <div class="description-box">
                            <?php echo nl2br(htmlspecialchars(html_entity_decode($job_details['description']))); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p>No job details found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="../../static/js/upload.js"></script>
    <script>
        document.getElementById('toggle-details').addEventListener('click', function () {
            const fullDetails = document.getElementById('full-details');
            if (fullDetails.style.display === 'none') {
                fullDetails.style.display = 'block';
                this.textContent = 'Hide Full Details';
            } else {
                fullDetails.style.display = 'none';
                this.textContent = 'Show Full Details';
            }
        });
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

    const dropdownMenu = document.querySelector('.profile-dropdown .dropdown-menu');
    dropdownMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});
    </script>
</body>
</html>