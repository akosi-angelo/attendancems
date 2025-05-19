<?php
require 'db.php'; // Starts session

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as a teacher.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null; // 'present' or 'absent'

    if (!$student_id || !$date || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing data. Student ID, date, and status are required.']);
        exit();
    }

    if ($status !== 'present' && $status !== 'absent') {
        echo json_encode(['success' => false, 'message' => 'Invalid status.']);
        exit();
    }

    // Validate date format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format.']);
        exit();
    }

    try {
        // Check if student exists
        $stmt_check_student = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'student'");
        $stmt_check_student->bindParam(':id', $student_id, PDO::PARAM_INT);
        $stmt_check_student->execute();
        if (!$stmt_check_student->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Student not found.']);
            exit();
        }

        // Use INSERT ... ON DUPLICATE KEY UPDATE (UPSERT)
        // Requires a UNIQUE key on (student_user_id, attendance_date) in the 'attendance' table.
        $sql = "INSERT INTO attendance (student_user_id, attendance_date, status) 
                VALUES (:student_id, :date, :status)
                ON DUPLICATE KEY UPDATE status = :status_update";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':status_update', $status); // For the UPDATE part

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Attendance updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update attendance.']);
        }

    } catch (PDOException $e) {
        // Log $e->getMessage() for server-side debugging
        error_log("Update attendance error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>