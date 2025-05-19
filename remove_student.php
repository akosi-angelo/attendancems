<?php
require 'db.php'; // Starts session

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    $_SESSION['login_error'] = "Unauthorized access.";
    header("Location: index.html");
    exit();
}

if (isset($_GET['student_id'])) {
    $student_id_to_remove = (int)$_GET['student_id'];

    if ($student_id_to_remove > 0) {
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Get student name for message (optional)
            $stmt_name = $pdo->prepare("SELECT name FROM users WHERE id = :id AND role = 'student'");
            $stmt_name->bindParam(':id', $student_id_to_remove, PDO::PARAM_INT);
            $stmt_name->execute();
            $student = $stmt_name->fetch();
            $student_name = $student ? $student['name'] : 'Student';


            // Delete from attendance first (due to foreign key)
            $stmt_att = $pdo->prepare("DELETE FROM attendance WHERE student_user_id = :student_id");
            $stmt_att->bindParam(':student_id', $student_id_to_remove, PDO::PARAM_INT);
            $stmt_att->execute();

            // Then delete from users
            $stmt_user = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'student'");
            $stmt_user->bindParam(':id', $student_id_to_remove, PDO::PARAM_INT);
            
            if ($stmt_user->execute() && $stmt_user->rowCount() > 0) {
                $pdo->commit();
                $_SESSION['message'] = htmlspecialchars($student_name) . " and their attendance records removed successfully.";
            } else {
                $pdo->rollBack();
                $_SESSION['error_message'] = "Failed to remove student or student not found.";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Database error during removal: " . $e->getMessage();
             // Log actual error $e->getMessage() for server logs
        }
    } else {
        $_SESSION['error_message'] = "Invalid student ID specified for removal.";
    }
    header("Location: teacher_dashboard.php");
    exit();

} else {
    $_SESSION['error_message'] = "No student ID specified for removal.";
    header("Location: teacher_dashboard.php");
    exit();
}
?>