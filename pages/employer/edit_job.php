<?php
session_start();
if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    header("Location: employee_sign_in.php");
    exit();
}
include '../../db_connection/connection.php';
$conn = OpenConnection();

$user_id = $_SESSION['user_id'];
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $job_id) {
    // Collect all fields from POST
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    // Use 'other_category' if provided, else fallback to 'category'
    $category = isset($_POST['other_category']) && $_POST['other_category'] !== '' ? trim($_POST['other_category']) : trim($_POST['category']);
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $company_name = trim($_POST['company_name']);
    $skills = trim($_POST['skills']);
    $education = trim($_POST['education']);

    $stmt = $conn->prepare("UPDATE jobs SET title=?, description=?, category=?, salary=?, location=?, company_name=?, skills=?, education=? WHERE job_id=? AND employer_id=?");
    $stmt->bind_param(
        "ssssssssii",
        $title,
        $description,
        $category,
        $salary,
        $location,
        $company_name,
        $skills,
        $education,
        $job_id,
        $user_id
    );
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: view_jobs.php?updated=1");
    exit();
}
// No output or action if not POST
?>
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: view_jobs.php?updated=1");
    exit();
}

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id=? AND employer_id=?");
$stmt->bind_param("ii", $job_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

if (!$job) {
    $conn->close();
    header("Location: view_jobs.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job</title>
    <link rel="stylesheet" href="css/view_applications.css">
    <style>
        .edit-job-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.08);
            padding: 30px 30px 20px 30px;
        }
        .edit-job-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #144272;
        }
        .edit-job-container label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .edit-job-container input,
        .edit-job-container select {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: inherit;
        }
        .edit-job-container .btn {
            width: 100%;
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 0;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        .edit-job-container .btn:hover {
            background: #0056b3;
        }
        .edit-job-container .cancel-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            color: #888;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="edit-job-container">
        <h2>Edit Job</h2>
        <form method="post">
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['job_id']); ?>">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>

            <label for="description">Description</label>
            <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($job['description']); ?>">

            <label for="category">Category</label>
            <input type="text" name="category" id="category" value="<?php echo htmlspecialchars($job['category']); ?>">

            <label for="salary">Salary</label>
            <input type="number" name="salary" id="salary" value="<?php echo htmlspecialchars($job['salary']); ?>" min="0" max="10000000" required>

            <label for="location">Location</label>
            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($job['location']); ?>">

            <label for="company_name">Company Name</label>
            <input type="text" name="company_name" id="company_name" value="<?php echo htmlspecialchars($job['company_name']); ?>">

            <label for="skills">Skills</label>
            <input type="text" name="skills" id="skills" value="<?php echo htmlspecialchars($job['skills']); ?>">

            <label for="education">Education</label>
            <input type="text" name="education" id="education" value="<?php echo htmlspecialchars($job['education']); ?>">

            <button type="submit" class="btn">Update Job</button>
        </form>
        <a href="view_jobs.php" class="cancel-link">Cancel</a>
    </div>
</body>
</html>
