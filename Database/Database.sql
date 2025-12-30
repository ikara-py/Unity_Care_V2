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





INSERT INTO Person (id, email, first_name, last_name, username, password, role) VALUES
(1, 'admin@hassan2.ma', 'Youssef', 'Alaoui', 'admin_youssef', 'hashed_pass_1', 'admin'),
(2, 'meriem.bennani@hassan2.ma', 'Meriem', 'Bennani', 'dr_meriem', 'hashed_pass_2', 'doctor'),
(3, 'amine.idrissi@hassan2.ma', 'Amine', 'Idrissi', 'dr_amine', 'hashed_pass_3', 'doctor'),
(4, 'karima.tazi@email.com', 'Karima', 'Tazi', 'ktazi88', 'hashed_pass_4', 'patient'),
(5, 'omar.haddad@email.com', 'Omar', 'Haddad', 'ohaddad92', 'hashed_pass_5', 'patient');

INSERT INTO departments (name, location) VALUES
('Cardiologie', 'Aile A, Casablanca'),
('Pédiatrie', 'Aile C, Rabat'),
('Neurologie', 'Bâtiment B, Marrakech');

INSERT INTO doctors (id, department_id, specialty) VALUES
(2, 1, 'Cardiologue Interventionnel'),
(3, 3, 'Neurologue');

INSERT INTO patients (id, date_of_birth, phone) VALUES
(4, '1988-05-14', '0661234567'),
(5, '1992-11-20', '0670987654');


INSERT INTO medications (name, description) VALUES
('Doliprane 1000mg', 'Paracétamol pour douleurs et fièvre'),
('Amoxicilline', 'Antibiotique à large spectre'),
('Spasfon', 'Traitement des douleurs spasmodiques');

INSERT INTO appointments (appointment_date, appointment_time, doctor_id, patient_id, reason, status) VALUES
('2024-01-15', '09:30:00', 2, 4, 'Consultation de routine pour hypertension', 'done'),
('2024-02-10', '14:00:00', 3, 5, 'Migraines chroniques persistantes', 'scheduled');

INSERT INTO prescriptions (doctor_id, patient_id, medication_id, dosage_instructions) VALUES
(2, 4, 1, '1 comprimé 3 fois par jour après les repas pendant 5 jours'),
(3, 5, 3, '2 comprimés en cas de crise, maximum 6 par jour');




