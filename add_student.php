<?php
require 'db.php'; // Starts session

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    $_SESSION['login_error'] = "Unauthorized access.";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name']);
    $student_id_number = trim($_POST['student_id_number']);
    $student_password = $_POST['student_password'];

    if (empty($student_name) || empty($student_id_number) || empty($student_password)) {
        $_SESSION['error_message'] = "All fields are required to add a student.";
        header("Location: teacher_dashboard.php");
        exit();
    }

    // Check if ID number already exists
    try {
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE id_number = :id_number");
        $stmt_check->bindParam(':id_number', $student_id_number);
        $stmt_check->execute();
        if ($stmt_check->fetch()) {
            $_SESSION['error_message'] = "Student ID Number already exists.";
            header("Location: teacher_dashboard.php");
            exit();
        }

        $hashed_password = password_hash($student_password, PASSWORD_DEFAULT);
        $role = 'student';

        $stmt = $pdo->prepare("INSERT INTO users (id_number, password, name, role) VALUES (:id_number, :password, :name, :role)");
        $stmt->bindParam(':id_number', $student_id_number);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':name', $student_name);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Student '" . htmlspecialchars($student_name) . "' added successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to add student.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        // Log actual error $e->getMessage() for server logs
    }
    header("Location: teacher_dashboard.php");
    exit();

} else {
    header("Location: teacher_dashboard.php");
    exit();
}
?>