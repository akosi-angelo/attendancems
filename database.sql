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
