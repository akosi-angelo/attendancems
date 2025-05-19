<?php
require 'db.php'; // Ensures session_start() is called

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = $_POST['id_number'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Debugging output - moved inside the POST block
    $stmt = $pdo->prepare("SELECT id, password, name, role FROM users WHERE id_number = :id_number AND role = :role");
    $stmt->bindParam(':id_number', $id_number);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // End of debugging output

    if (empty($id_number) || empty($password) || empty($role)) {
        $_SESSION['login_error'] = "All fields are required.";
        header("Location: index.php");
        exit();
    }

    try {
        // The prepared statement is already done above for debugging
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'student') {
                header("Location: student_dashboard.php");
                exit();
            } elseif ($user['role'] === 'teacher') {
                header("Location: teacher_dashboard.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>