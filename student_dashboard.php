<?php
require 'db.php'; // Starts session
require 'calendar_functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    $_SESSION['login_error'] = "Please log in as a student to view this page.";
    header("Location: index.html");
    exit();
}

$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['user_name'];

// For calendar: current month and year
$month = date('m');
$year = date('Y');

// Global $pdo for calendar_functions if not passed directly
$pdo_global = $pdo;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?php echo htmlspecialchars($student_name); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Student Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($student_name); ?>!</span>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-content">
            <h2>Your Monthly Attendance</h2>
            <div id="student-calendar-container">
                <?php echo generateCalendar((int)$month, (int)$year, $student_id, false, $pdo); ?>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>