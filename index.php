<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Student Attendance System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container login-container">
        <div class="login-box">
            <h1>Student Attendance System</h1>
            <p class="welcome-message">Welcome! Please log in to continue.</p>

            <?php
                session_start(); // Ensure session is started to check for messages
                if (isset($_SESSION['login_error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
                    unset($_SESSION['login_error']); // Clear the message after displaying
                }
                if (isset($_SESSION['message'])) {
                    echo '<p class="success-message">' . htmlspecialchars($_SESSION['message']) . '</p>';
                    unset($_SESSION['message']);
                }
            ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="id_number">ID Number:</label>
                    <input type="text" id="id_number" name="id_number" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="student">student</option>
                        <option value="teacher">teacher</option>
                    </select>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>