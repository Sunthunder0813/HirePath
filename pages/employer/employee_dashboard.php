<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: employee_sign_in.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);

include '../../db_connection/connection.php';

$has_company = false;
$conn = OpenConnection(); 
$stmt = $conn->prepare("SELECT company_name FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($company_name);
if ($stmt->fetch() && !empty($company_name)) {
    $has_company = true;
}
$stmt->close();

if (!$has_company) {
    header("Location: company_profile.php?assign_company=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../static/img/icon/favicon.png" type="image/x-icon">
    <title>Employer Dashboard</title>

    <script src="../../static/js/get_pending_count.js" defer></script>
    <style>
        /* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: auto;
    flex: 1;
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.application-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 15px;
    padding: 0;
    list-style: none;
}

.application-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 10px;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.application-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.application-card.selected {
    border: 2px solid #007bff;
}

.application-card strong {
    font-size: 16px;
    color: #333;
}

.application-card p {
    margin: 0;
    font-size: 14px;
    color: #666;
}

.application-card .date {
    font-size: 12px;
    color: #999;
}

.btn {
    text-decoration: none;
    padding: 8px 12px;
    background: #144272;
    color: white;
    border-radius: 5px;
    text-align: center;
    font-size: 14px;
    display: inline-block;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #0056b3;
}

.no-applications {
    text-align: center;
    color: #888;
    font-style: italic;
}

.tabs {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.tab {
    position: relative;
    padding: 10px 20px;
    cursor: pointer;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 5px 5px 0 0;
    margin-right: 5px;
    transition: background 0.3s ease;
}

.tab.active {
    background: #007bff;
    color: white;
    border-bottom: none;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Navigation Bar */
.navbar {
    background: #0A2647;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.logo:hover {
    color: #00c6ff;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 15px;
    align-items: center;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background 0.3s ease;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    position: relative;
}

/* Navigation Bar */
.navbar {
    background: #0A2647;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.logo:hover {
    color: #00c6ff;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 15px;
    align-items: center;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background 0.3s ease;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    position: relative;
}

        .applications-container {
            position: relative; /* Ensure the badge is positioned relative to the parent container */
        }

        .nav-badge {
            position: absolute;
            top: -5px; /* Moves it slightly above the tab */
            right: -5px; /* Moves it slightly outside */
            background: #dc3545;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            display: inline-block;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: #333;
            color: white;
            margin-top: auto;
        }

        footer p {
            margin: 0;
        }

        .dashboard-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
        }

        .dashboard-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card h2 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .dashboard-card p {
            font-size: 1em;
            color: #555;
            margin-bottom: 20px;
        }

        .dashboard-card a {
            text-decoration: none;
            color: white;
            background: #144272;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .dashboard-card a:hover {
            background: #0056b3;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 900px) {
            .dashboard-links {
                flex-direction: column;
                align-items: center;
                gap: 24px;
            }
            .dashboard-card {
                width: 90%;
                max-width: 400px;
            }
        }
        @media (max-width: 600px) {
            .dashboard-links {
                gap: 16px;
                margin: 18px 0;
            }
            .dashboard-card {
                width: 98%;
                max-width: 98vw;
                padding: 14px 6px;
            }
            .dashboard-card h2 {
                font-size: 1.1em;
            }
            .dashboard-card p {
                font-size: 0.95em;
            }
        }

        .disabled-link {
            pointer-events: none;
            background: #ccc !important;
            color: #888 !important;
            cursor: not-allowed;
        }

        .warning-message {
            color: #dc3545;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }   
    </style>
    
</head>
<body>

    <nav class="navbar">
        <a href="Employee_dashboard.php" class="logo">Employee Portal</a>
        <ul class="nav-links">
            <li><a href="post_job.php">Post Job</a></li>
            <li>
                <div class="applications-container">
                    <a href="view_applications.php">Applications</a>
                    <span id="navbar-badge" class="nav-badge" style="display: inline-block;">0</span>
                </div>
            </li>
            <li><a href="company_profile.php">Company Profile</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <?php if (!$has_company): ?>
            <div class="warning-message">
                You must assign your company profile before posting a job.
            </div>
        <?php endif; ?>
        <div class="dashboard-links">
            <div class="dashboard-card">
                <h2>Post a Job</h2>
                <p>Create new job postings to attract top talent.</p>
                <a href="post_job.php"
                   <?php if (!$has_company) echo 'class="disabled-link" tabindex="-1" aria-disabled="true"'; ?>>
                   Post Job
                </a>
            </div>
            <div class="dashboard-card">
                <h2>View Applications</h2>
                <p>Review and manage applications from candidates.</p>
                <a href="view_applications.php">View Applications</a>
            </div>
            <div class="dashboard-card">
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
