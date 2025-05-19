CREATE DATABASE IF NOT EXISTS student_attendance_db;
USE student_attendance_db;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_number` VARCHAR(50) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `role` ENUM('student', 'teacher') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_user_id` INT NOT NULL,
  `attendance_date` DATE NOT NULL,
  `status` ENUM('present', 'absent') NOT NULL,
  FOREIGN KEY (`student_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `student_date_unique` (`student_user_id`, `attendance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dummy Teacher
-- Password for T001: teacherpass
INSERT INTO `users` (`id_number`, `password`, `name`, `role`) VALUES
('T001', '$2y$10$fPlhDkYr0LQt0WWZlFoN5O/oEmXvV0xKpR0m1Zl8G1o6z8XJmNJwa', 'Prof. Albus Dumbledore', 'teacher');

-- Dummy Students
-- Password for S001: studentpass1
INSERT INTO `users` (`id_number`, `password`, `name`, `role`) VALUES
('S001', '$2y$10$1qAZ.QL0M81B6.0XJLBM7u83QId.94jN3P5N/xKz9Y7H8d7E.g3zK', 'Harry Potter', 'student');
-- Password for S002: studentpass2
INSERT INTO `users` (`id_number`, `password`, `name`, `role`) VALUES
('S002', '$2y$10$gQ8fKj.7iKLY8hvyy3eCSOQkYkjx8b9nQxK2qS6j.2dZ.y3U5N.7a', 'Hermione Granger', 'student');
-- Password for S003: studentpass3
INSERT INTO `users` (`id_number`, `password`, `name`, `role`) VALUES
('S003', '$2y$10$rX.1jJ3fS.L4x/n5fP6qIOz.QxY.L4w9r.t3sS8hN.r7pA2kI9o0S', 'Ron Weasley', 'student');

-- Dummy Attendance Data (for current month to be visible)
-- Assuming current month is May 2025 for sample data
-- Replace with actual current month data or make script dynamic
-- For Harry Potter (S001, user_id typically 2 if inserted after teacher)
INSERT INTO `attendance` (`student_user_id`, `attendance_date`, `status`) VALUES
(2, CONCAT(YEAR(CURDATE()), '-', LPAD(MONTH(CURDATE()), 2, '0'), '-01'), 'present'),
(2, CONCAT(YEAR(CURDATE()), '-', LPAD(MONTH(CURDATE()), 2, '0'), '-02'), 'absent'),
(2, CONCAT(YEAR(CURDATE()), '-', LPAD(MONTH(CURDATE()), 2, '0'), '-05'), 'present');

-- For Hermione Granger (S002, user_id typically 3)
INSERT INTO `attendance` (`student_user_id`, `attendance_date`, `status`) VALUES
(3, CONCAT(YEAR(CURDATE()), '-', LPAD(MONTH(CURDATE()), 2, '0'), '-01'), 'present'),
(3, CONCAT(YEAR(CURDATE()), '-', LPAD(MONTH(CURDATE()), 2, '0'), '-03'), 'present');