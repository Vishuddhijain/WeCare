SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create the doctors table to store doctor profiles and specialization details
CREATE TABLE `doctors` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `specialization` VARCHAR(100) NOT NULL,
  `experience` VARCHAR(20),
  `image` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the doctor_slots table to manage available slots for each doctor
CREATE TABLE `doctor_slots` (
  `slot_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `doctor_id` INT NOT NULL,
  `slot_time` VARCHAR(20) NOT NULL,
  `is_available` BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create the contact_form table to store patient appointments
CREATE TABLE `contact_form` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `number` VARCHAR(10) NOT NULL,
  `date` DATETIME NOT NULL,
  `disease` VARCHAR(255),
  `doctor_id` INT NOT NULL,
  `slot_id` INT,
  FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`slot_id`) REFERENCES `doctor_slots`(`slot_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE doctors ADD COLUMN password VARCHAR(255) NOT NULL;

-- Insert data into doctors table
INSERT INTO doctors (id, name, specialization, experience, image) VALUES
(1, 'Dr. Ananya Sharma', 'Cardiologist', '10 years', 'images/doc-1.jpg'),
(2, 'Dr. Rajesh Verma', 'Orthopedic Surgeon', '12 years', 'images/doc-2.jpg'),
(3, 'Dr. Leena Kapoor', 'Pediatrician', '8 years', 'images/doc-3.jpg'),
(4, 'Dr. Priya Menon', 'Dermatologist', '6 years', 'images/doc-4.jpg'),
(5, 'Dr. Amit Patel', 'Neurologist', '15 years', 'images/doc-5.jpg'),
(6, 'Dr. Naveen Singh', 'General Surgeon', '10 years', 'images/doc-6.jpg'),
(7, 'Dr. Maya Rao', 'Endocrinologist', '7 years', 'images/doc-7.jpg'),
(8, 'Dr. Arjun Khanna', 'Gastroenterologist', '12 years', 'images/doc-8.jpg'),
(9, 'Dr. Sameer Das', 'Oncologist', '14 years', 'images/doc-9.jpg');


CREATE TABLE chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    sender ENUM('doctor', 'patient') NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data into doctor_slots table
INSERT INTO doctor_slots (doctor_id, slot_time) VALUES
(1, '9:00 AM - 10:00 AM'),
(1, '11:00 AM - 12:00 PM'),
(1, '2:00 PM - 3:00 PM'),
(2, '9:30 AM - 10:30 AM'),
(2, '1:00 PM - 2:00 PM'),
(2, '4:00 PM - 5:00 PM'),
(3, '8:00 AM - 9:00 AM'),
(3, '10:00 AM - 11:00 AM'),
(3, '3:00 PM - 4:00 PM'),
(4, '9:00 AM - 10:00 AM'),
(4, '1:00 PM - 2:00 PM'),
(4, '3:30 PM - 4:30 PM'),
(5, '9:00 AM - 10:00 AM'),
(5, '2:00 PM - 3:00 PM'),
(5, '4:00 PM - 5:00 PM'),
(6, '8:30 AM - 9:30 AM'),
(6, '11:00 AM - 12:00 PM'),
(6, '3:00 PM - 4:00 PM'),
(7, '8:00 AM - 9:00 AM'),
(7, '12:00 PM - 1:00 PM'),
(7, '2:00 PM - 3:00 PM'),
(8, '10:00 AM - 11:00 AM'),
(8, '1:00 PM - 2:00 PM'),
(8, '4:00 PM - 5:00 PM'),
(9, '9:00 AM - 10:00 AM'),
(9, '12:00 PM - 1:00 PM'),
(9, '3:00 PM - 4:00 PM');

COMMIT;
CREATE TABLE doctor_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_name VARCHAR(255) NOT NULL,
    review TEXT NOT NULL,
    rating INT CHECK(rating BETWEEN 1 AND 5),
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);
