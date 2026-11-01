-- Face Attendance System - Installation Script
-- MySQL / MariaDB (InnoDB, utf8mb4)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Change this to your hosting database name before import
USE `your_cpanel_database_name`;

DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `student_leave_applications`;
DROP TABLE IF EXISTS `rest_attendance`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `face_descriptors`;
DROP TABLE IF EXISTS `face_registrations`;
DROP TABLE IF EXISTS `teacher_assignments`;
DROP TABLE IF EXISTS `class_subjects`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `teachers`;
DROP TABLE IF EXISTS `subjects`;
DROP TABLE IF EXISTS `classes`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `role` ENUM('admin','teacher','student') NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_username` (`username`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `classes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `start_time` TIME NOT NULL DEFAULT '08:00:00',
  `late_grace_minutes` INT UNSIGNED NOT NULL DEFAULT 15,
  `rest_start` TIME NULL DEFAULT '12:00:00',
  `rest_end` TIME NULL DEFAULT '13:00:00',
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_classes_code` (`code`),
  KEY `idx_classes_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `subjects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subjects_code` (`code`),
  KEY `idx_subjects_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `teachers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `teacher_code` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_teachers_user` (`user_id`),
  UNIQUE KEY `uk_teachers_code` (`teacher_code`),
  CONSTRAINT `fk_teachers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `students` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NULL,
  `student_code` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `gender` ENUM('male','female','other') NULL,
  `date_of_birth` DATE NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `face_registered` TINYINT(1) NOT NULL DEFAULT 0,
  `registration_version` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_students_user` (`user_id`),
  UNIQUE KEY `uk_students_code` (`student_code`),
  KEY `idx_students_class` (`class_id`),
  KEY `idx_students_face` (`face_registered`),
  CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `class_subjects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_class_subject` (`class_id`, `subject_id`),
  CONSTRAINT `fk_cs_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cs_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `teacher_assignments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_teacher_assignment` (`teacher_id`, `class_id`, `subject_id`),
  KEY `idx_ta_class` (`class_id`),
  KEY `idx_ta_subject` (`subject_id`),
  CONSTRAINT `fk_ta_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ta_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ta_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `face_registrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL,
  `sample_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `preview_image` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `registered_by` INT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fr_student` (`student_id`),
  KEY `idx_fr_active` (`student_id`, `is_active`),
  CONSTRAINT `fk_fr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fr_user` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `face_descriptors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `registration_id` INT UNSIGNED NOT NULL,
  `descriptor_json` LONGTEXT NOT NULL,
  `sample_index` INT UNSIGNED NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fd_student_active` (`student_id`, `is_active`),
  KEY `idx_fd_registration` (`registration_id`),
  CONSTRAINT `fk_fd_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fd_registration` FOREIGN KEY (`registration_id`) REFERENCES `face_registrations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `attendance` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NULL,
  `attendance_date` DATE NOT NULL,
  `check_in_time` DATETIME NULL,
  `check_out_time` DATETIME NULL,
  `check_in_confidence` DECIMAL(6,4) NULL,
  `check_out_confidence` DECIMAL(6,4) NULL,
  `check_in_photo` VARCHAR(255) NULL,
  `check_out_photo` VARCHAR(255) NULL,
  `status` ENUM('checked_in','checked_out','late','absent','on_leave') NOT NULL DEFAULT 'checked_in',
  `notes` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_attendance_student_date` (`student_id`, `attendance_date`),
  KEY `idx_attendance_date` (`attendance_date`),
  KEY `idx_attendance_class` (`class_id`),
  KEY `idx_attendance_status` (`status`),
  CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rest_attendance` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `class_id` INT UNSIGNED NULL,
  `attendance_date` DATE NOT NULL,
  `check_in_time` DATETIME NULL,
  `check_out_time` DATETIME NULL,
  `check_in_confidence` DECIMAL(6,4) NULL,
  `check_out_confidence` DECIMAL(6,4) NULL,
  `check_in_photo` VARCHAR(255) NULL,
  `check_out_photo` VARCHAR(255) NULL,
  `status` ENUM('checked_in','checked_out') NOT NULL DEFAULT 'checked_in',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rest_student_date` (`student_id`, `attendance_date`),
  KEY `idx_rest_date` (`attendance_date`),
  CONSTRAINT `fk_rest_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rest_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `student_leave_applications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `leave_date` DATE NOT NULL,
  `reason` TEXT NOT NULL,
  `document_path` VARCHAR(255) NULL,
  `document_original_name` VARCHAR(255) NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_note` TEXT NULL,
  `reviewed_by` INT UNSIGNED NULL,
  `reviewed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_leave_student` (`student_id`),
  KEY `idx_leave_date` (`leave_date`),
  KEY `idx_leave_status` (`status`),
  CONSTRAINT `fk_leave_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leave_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL,
  `student_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `device` VARCHAR(50) NULL,
  `browser` VARCHAR(80) NULL,
  `confidence_score` DECIMAL(6,4) NULL,
  `registration_version` INT UNSIGNED NULL,
  `metadata` JSON NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_student` (`student_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_created` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_audit_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT NOT NULL,
  `description` VARCHAR(255) NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================
-- Default Settings
-- =====================
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('min_face_size', '80', 'Minimum face box size in pixels'),
('max_face_size', '420', 'Maximum face box size in pixels'),
('min_brightness', '40', 'Minimum average brightness'),
('max_brightness', '220', 'Maximum average brightness'),
('min_sharpness', '20', 'Minimum sharpness score'),
('detection_confidence', '0.50', 'Minimum face detection score'),
('face_match_threshold', '0.55', 'Maximum Euclidean distance for match'),
('min_attendance_confidence', '0.45', 'Minimum match confidence for attendance'),
('stable_frames', '3', 'Stable consecutive frames required'),
('duplicate_scan_interval', '300', 'Seconds before another scan is allowed'),
('registration_samples', '3', 'Number of face samples for registration');

-- =====================
-- Demo Users
-- Passwords: admin123 / teacher123 / student123
-- =====================
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `status`) VALUES
(1, 'admin', 'admin@school.local', '$2y$10$/NED71TWEsMLzOWVqO8IsOfPbkUcUnKTvraeY66vwVWInqCJsCiu.', 'System Administrator', 'admin', 1),
(2, 'teacher1', 'alice@school.local', '$2y$10$vg3r.Wfp6OnGgnttRwsUSeg4LB23ytRxQbWen7.T5chPo9Gj/o8vq', 'Alice Teacher', 'teacher', 1),
(3, 'teacher2', 'bob@school.local', '$2y$10$vg3r.Wfp6OnGgnttRwsUSeg4LB23ytRxQbWen7.T5chPo9Gj/o8vq', 'Bob Teacher', 'teacher', 1),
(4, 'student1', 'john@school.local', '$2y$10$3LVnntxlFdb1yIM2WVtE9OJsUnygxVXZgHBYoI3d4S6WHINzCXRju', 'John Student', 'student', 1),
(5, 'student2', 'mary@school.local', '$2y$10$3LVnntxlFdb1yIM2WVtE9OJsUnygxVXZgHBYoI3d4S6WHINzCXRju', 'Mary Student', 'student', 1),
(6, 'student3', 'david@school.local', '$2y$10$3LVnntxlFdb1yIM2WVtE9OJsUnygxVXZgHBYoI3d4S6WHINzCXRju', 'David Student', 'student', 1),
(7, 'student4', 'sara@school.local', '$2y$10$3LVnntxlFdb1yIM2WVtE9OJsUnygxVXZgHBYoI3d4S6WHINzCXRju', 'Sara Student', 'student', 1),
(8, 'student5', 'kevin@school.local', '$2y$10$3LVnntxlFdb1yIM2WVtE9OJsUnygxVXZgHBYoI3d4S6WHINzCXRju', 'Kevin Student', 'student', 1);

INSERT INTO `classes` (`id`, `name`, `code`, `start_time`, `late_grace_minutes`, `rest_start`, `rest_end`, `status`) VALUES
(1, 'Computer Science Year 1 A', 'CS1A', '08:00:00', 15, '12:00:00', '13:00:00', 1),
(2, 'Computer Science Year 1 B', 'CS1B', '08:30:00', 15, '12:00:00', '13:00:00', 1),
(3, 'Information Technology Year 2', 'IT2', '09:00:00', 10, '12:30:00', '13:30:00', 1);

INSERT INTO `subjects` (`id`, `code`, `name`, `description`, `status`) VALUES
(1, 'PROG101', 'Introduction to Programming', 'Basics of programming using Python', 1),
(2, 'DB101', 'Database Systems', 'Relational databases and SQL', 1),
(3, 'WEB101', 'Web Development', 'HTML, CSS, JavaScript and PHP', 1),
(4, 'NET201', 'Computer Networks', 'Network fundamentals', 1);

INSERT INTO `teachers` (`id`, `user_id`, `teacher_code`, `phone`, `status`) VALUES
(1, 2, 'TCH001', '012-3456789', 1),
(2, 3, 'TCH002', '012-9876543', 1);

INSERT INTO `students` (`id`, `user_id`, `class_id`, `student_code`, `phone`, `gender`, `date_of_birth`, `status`, `face_registered`, `registration_version`) VALUES
(1, 4, 1, 'STU001', '011-1111111', 'male', '2005-01-15', 1, 0, 0),
(2, 5, 1, 'STU002', '011-2222222', 'female', '2005-03-22', 1, 0, 0),
(3, 6, 1, 'STU003', '011-3333333', 'male', '2004-11-08', 1, 0, 0),
(4, 7, 2, 'STU004', '011-4444444', 'female', '2005-07-19', 1, 0, 0),
(5, 8, 3, 'STU005', '011-5555555', 'male', '2004-05-30', 1, 0, 0);

INSERT INTO `class_subjects` (`class_id`, `subject_id`) VALUES
(1, 1), (1, 2), (1, 3),
(2, 1), (2, 3),
(3, 2), (3, 4);

INSERT INTO `teacher_assignments` (`teacher_id`, `class_id`, `subject_id`) VALUES
(1, 1, 1),
(1, 1, 2),
(1, 2, 1),
(2, 2, 3),
(2, 3, 2),
(2, 3, 4);
