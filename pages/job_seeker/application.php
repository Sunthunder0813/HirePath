<?php
session_start();
include '../../db_connection/connection.php';

$conn = OpenConnection();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
}

$statusFilter = isset($_GET['status']) ? $_GET['status'] : null;
$query = "
    SELECT applications.application_id, applications.job_id, jobs.title AS job_title, 
    applications.resume_link, applications.status, applications.applied_at
    FROM applications
    LEFT JOIN jobs ON applications.job_id = jobs.job_id
    WHERE applications.job_seeker_id = ?";
if ($statusFilter) {
    $query .= " AND applications.status = ?";
}
$query .= " ORDER BY applications.applied_at DESC";
$stmt = $conn->prepare($query);
if ($statusFilter) {
    $stmt->bind_param("is", $user_id, $statusFilter);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();

function getStatusClass($status) {
    $statusClass = 'unknown'; 
    if (strtolower($status) === 'accepted') {
        $statusClass = 'accepted';
    } elseif (strtolower($status) === 'pending') {
        $statusClass = 'pending';
    } elseif (strtolower($status) === 'rejected') {
        $statusClass = 'rejected';
    } elseif (strtolower($status) === 'reviewed') {
        $statusClass = 'reviewed';
    }
    return $statusClass;
}

function getStatusIcon($status, $directory = '../../static/img/icon') {
    $iconPath = $directory . '/unknown.png'; 
    if (strtolower($status) === 'accepted') {
        $iconPath = $directory . '/accepted.png';
    } elseif (strtolower($status) === 'pending') {
        $iconPath = $directory . '/pending.png';
    } elseif (strtolower($status) === 'rejected') {
        $iconPath = $directory . '/rejected.png';
    } elseif (strtolower($status) === 'reviewed') {
        $iconPath = $directory . '/reviewed.png';
    }
    return $iconPath;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/icon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../../static/css/application.css">
    <title>Job Application</title>
    
</head>
<body>
<nav>
    <p class="logo">
        <a href="../../index.php">
            <img src="../../static/img/icon/logo.png" alt="Hire Path Logo">
        </a>
    </p>
    <ul class="nav_links">
        <li><a href="../employer/employee_sign_in.php">Post a Job</a></li>
        <?php if (!empty($username)): ?>
            <li><a href="application.php">Application</a></li>
        <?php endif; ?>
        <?php if (!empty($username)): ?>
            <li class="profile_dropdown">
            <a><?php echo htmlspecialchars($email); ?> <span style="font-size: 1em;">&#9660;</span></a>
                <ul class="dropdown_menu">
                    <li><a href="jobseeker_changepass.php" class="sign_out_button">Change Password</a></li>
                    <li><a href="../../logout.php" class="sign_out_button">Sign Out</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li><a href="sign_in.php">Sign In</a></li>
        <?php endif; ?>
    </ul>
</nav>
    <div class="container">
        <h1>Your Job Applications</h1>
        <div class="filter_container">
            <form method="GET" action="application.php" onsubmit="return false;">
                <label for="status_filter">Filter by Status:</label>
                <select id="status_filter" name="status">
                    <option value="">All</option>
                    <option value="accepted" <?php echo $statusFilter === 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                    <option value="reviewed" <?php echo $statusFilter === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                    <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </form>
        </div>
        <div class="card_grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="application_card">
                        <img src="<?php echo getStatusIcon($row['status'], 'img/icon/status'); ?>" 
                             alt="<?php echo htmlspecialchars($row['status']); ?>" 
                             class="status_icon" 
                             title="Status: <?php echo ucfirst(htmlspecialchars($row['status'])); ?>">
                        <strong>
                            <span class="job_title">
                                <?php echo htmlspecialchars($row['job_title']); ?>
                            </span>
                        </strong>
                        <p>Status: 
                            <span class="<?php echo getStatusClass($row['status']); ?>">     
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </p>
                        <span class="date" data-applied-date="<?php echo htmlspecialchars(date('c', strtotime($row['applied_at']))); ?>">
                            Applied on: <?php echo htmlspecialchars(date('F j, Y', strtotime($row['applied_at']))); ?>
                        </span>
                        <?php if (!empty($row['resume_link'])): ?>
                            <a href="<?php echo htmlspecialchars($row['resume_link']); ?>" class="btn" target="_blank">View Resume</a>
                        <?php else: ?>
                            <p style="color: #888; font-size: 12px;">No resume uploaded.</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no_applications">
                    No applications found. 
                    <a href="../../index.php">Browse Jobs</a>   
                </p>
            <?php endif; ?>
        </div>
    </div>
    <script src="../../static/js/application.js"></script>
</body>
</html>