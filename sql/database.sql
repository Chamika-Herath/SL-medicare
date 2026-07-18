-- SQL Schema for Cloud-Based Hospital Management System (HMS)
-- Target: MySQL (compatible with Hostinger MySQL and local Docker containers)

CREATE DATABASE IF NOT EXISTS `hospital_management`;
USE `hospital_management`;

-- 1. Users Table (Enforces RBAC)
-- Passwords are encrypted using Bcrypt (cost=10). Default password for seed data is 'password123'
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('PATIENT', 'DOCTOR', 'ADMIN') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Profiles Table (User demographic details)
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `dob` DATE,
  `gender` ENUM('MALE', 'FEMALE', 'OTHER'),
  `phone` VARCHAR(20),
  `address` TEXT,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Appointments Table (Patient-Doctor schedulers)
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `appointment_date` DATETIME NOT NULL,
  `status` ENUM('PENDING', 'APPROVED', 'CANCELLED') DEFAULT 'PENDING',
  `reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Medical Records Table (Patient diagnostic history and storage links)
CREATE TABLE IF NOT EXISTS `medical_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `diagnosis` VARCHAR(255) NOT NULL,
  `notes` TEXT,
  `image_url` VARCHAR(500) DEFAULT NULL, -- Stores URL pointing to Cloud Object Storage (Supabase/Cloudinary)
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================================
-- SEED DATA (All passwords are 'password123' - Bcrypt hashed)
-- HASH: $2y$10$cOqMizM1K0wI9/b2N6l6Uefj3zE3Nze8iJpI6e.U/p90b411q8P3S
-- =========================================================================

-- Seed Users
INSERT INTO `users` (`id`, `email`, `password`, `role`) VALUES
(1, 'admin@hms.cloud', '$2y$10$cOqMizM1K0wI9/b2N6l6Uefj3zE3Nze8iJpI6e.U/p90b411q8P3S', 'ADMIN'),
(2, 'doctor.smith@hms.cloud', '$2y$10$cOqMizM1K0wI9/b2N6l6Uefj3zE3Nze8iJpI6e.U/p90b411q8P3S', 'DOCTOR'),
(3, 'doctor.jones@hms.cloud', '$2y$10$cOqMizM1K0wI9/b2N6l6Uefj3zE3Nze8iJpI6e.U/p90b411q8P3S', 'DOCTOR'),
(4, 'patient.doe@hms.cloud', '$2y$10$cOqMizM1K0wI9/b2N6l6Uefj3zE3Nze8iJpI6e.U/p90b411q8P3S', 'PATIENT'),
(5, 'patient.alice@hms.cloud', '$2y$10$cOqMizM1K0wI9/b2N6l6Uefj3zE3Nze8iJpI6e.U/p90b411q8P3S', 'PATIENT');

-- Seed Profiles
INSERT INTO `profiles` (`user_id`, `full_name`, `dob`, `gender`, `phone`, `address`) VALUES
(1, 'System Cloud Admin', '1985-06-15', 'MALE', '+1555010001', 'Cloud Infrastructure HQ, Server Room A'),
(2, 'Dr. Sarah Smith (Cardiology)', '1978-04-22', 'FEMALE', '+1555010002', 'Outpatient Building Room 302'),
(3, 'Dr. Robert Jones (Neurology)', '1982-11-09', 'MALE', '+1555010003', 'Outpatient Building Room 104'),
(4, 'John Doe', '1995-02-12', 'MALE', '+1555010004', '123 Pine St, Metro City'),
(5, 'Alice Johnson', '1998-09-30', 'FEMALE', '+1555010005', '456 Oak Ave, West Suburbs');

-- Seed Appointments
INSERT INTO `appointments` (`patient_id`, `doctor_id`, `appointment_date`, `status`, `reason`) VALUES
(4, 2, '2026-07-20 10:00:00', 'APPROVED', 'Routine cardiovascular health checkup.'),
(4, 3, '2026-07-22 14:30:00', 'PENDING', 'Persistent migraines and sleep issues.'),
(5, 2, '2026-07-21 11:15:00', 'APPROVED', 'Follow-up ECG consultation.');

-- Seed Medical Records
INSERT INTO `medical_records` (`patient_id`, `doctor_id`, `diagnosis`, `notes`, `image_url`) VALUES
(4, 2, 'Mild Hypertension', 'Patient exhibits slightly elevated systolic blood pressure. Recommended reduction in sodium intake and daily light exercise. Re-evaluate in 3 months.', 'https://res.cloudinary.com/demo/image/upload/v1312461204/sample.jpg'),
(5, 2, 'Mitral Valve Prolapse Follow-up', 'Echocardiogram completed. Mild regurgitation observed. Patient advised to avoid heavy caffeine intake and return for annual monitoring.', NULL);
