CREATE DATABASE uccv2;
USE uccv2;

-- Person
CREATE TABLE Person (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'patient') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    location VARCHAR(255)
);

-- Doctors
CREATE TABLE doctors (
    id INT PRIMARY KEY,
    department_id INT NOT NULL,
    specialty VARCHAR(100),
    FOREIGN KEY (id) REFERENCES Person(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Patients
CREATE TABLE patients (
    id INT PRIMARY KEY,
    date_of_birth DATE,
    phone VARCHAR(20),
    FOREIGN KEY (id) REFERENCES Person(id) ON DELETE CASCADE
);

-- Medications
CREATE TABLE medications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Appointments
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    reason TEXT,
    status ENUM('scheduled', 'done', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id)
);

-- Prescriptions
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    medication_id INT NOT NULL,
    dosage_instructions TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (medication_id) REFERENCES medications(id)
);

