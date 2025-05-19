<?php
require 'db.php'; // Starts session
require 'calendar_functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    $_SESSION['login_error'] = "Please log in as a teacher to view this page.";
    header("Location: index.html");
    exit();
}

$teacher_name = $_SESSION['user_name'];

// Fetch list of students
$students = [];
try {
    $stmt = $pdo->query("SELECT id, id_number, name FROM users WHERE role = 'student' ORDER BY name ASC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error fetching students: " . $e->getMessage();
}

// For calendar: current month and year
$month = date('m');
$year = date('Y');

$view_student_id = isset($_GET['view_student_id']) ? (int)$_GET['view_student_id'] : null;
$view_student_name = '';
if ($view_student_id) {
    try {
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = :id AND role = 'student'");
        $stmt->bindParam(':id', $view_student_id, PDO::PARAM_INT);
        $stmt->execute();
        $student_to_view = $stmt->fetch();
        if ($student_to_view) {
            $view_student_name = $student_to_view['name'];
        } else {
            $view_student_id = null; // Student not found
            $_SESSION['error_message'] = "Selected student not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching student details: " . $e->getMessage();
        $view_student_id = null;
    }
}
// Global $pdo for calendar_functions if not passed directly
$pdo_global = $pdo;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="container">
            <h1>Teacher Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($teacher_name); ?>!</span>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p class="success-message">' . htmlspecialchars($_SESSION['message']) . '</p>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div class="teacher-actions">
            <div class="student-management">
                <h2>Manage Students</h2>
                <form action="add_student.php" method="POST" class="add-student-form">
                    <h3>Add New Student</h3>
                    <div class="input-group">
                        <label for="student_name">Name:</label>
                        <input type="text" id="student_name" name="student_name" required>
                    </div>
                    <div class="input-group">
                        <label for="student_id_number">ID Number:</label>
                        <input type="text" id="student_id_number" name="student_id_number" required>
                    </div>
                    <div class="input-group">
                        <label for="student_password">Password:</label>
                        <input type="password" id="student_password" name="student_password" required>
                    </div>
                    <button type="submit" class="btn">Add Student</button>
                </form>

                <h3>Student List</h3>
                <?php if (count($students) > 0): ?>
                <ul class="student-list">
                    <?php foreach ($students as $student): ?>
                    <li>
                        <span><?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['id_number']); ?>)</span>
                        <div class="student-actions">
                            <a href="teacher_dashboard.php?view_student_id=<?php echo $student['id']; ?>" class="btn btn-small">View Attendance</a>
                            <a href="remove_student.php?student_id=<?php echo $student['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to remove this student and all their attendance records?');">Remove</a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p>No students found.</p>
                <?php endif; ?>
            </div>

            <?php if ($view_student_id && $view_student_name): ?>
            <div class="student-calendar-view">
                <h2>Attendance Calendar for <?php echo htmlspecialchars($view_student_name); ?></h2>
                <p>Click on a day to mark attendance.</p>
                <div id="teacher-calendar-container" data-current-student-id="<?php echo $view_student_id; ?>">
                    <?php echo generateCalendar((int)$month, (int)$year, $view_student_id, true, $pdo); ?>
                </div>
            </div>
            <?php elseif (isset($_GET['view_student_id'])): ?>
            <div class="student-calendar-view">
                 <p class="error-message">Could not load calendar for the selected student. They may have been removed or an error occurred.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>