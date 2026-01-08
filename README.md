# Unity Care Clinic – Backoffice V2

## Project Overview

Following the success of previous versions of **Unity Care Clinic**, this V2 aims to extend the backoffice to cover the complete patient journey: **appointments, consultations, and medical prescriptions**.
This version introduces a **role-based authentication system** allowing three types of users (**Admin, Doctor, Patient**) to access features tailored to their role.  
The application must ensure **secure access**, **protection against common web attacks**, and **traceability of user actions**, while consolidating the existing **OOP architecture**.

---

## Main Objectives

- Implement a web authentication system using `$_SESSION`
- Implement Role-Based Access Control (RBAC)
- Add medical appointment management
- Add prescription and medication management
- Secure the application against XSS and CSRF attacks
- Enrich dashboard statistics
- Improve and consolidate the existing OOP architecture

---

## Project Structure


├── classes/
│   ├── models/
│   │   ├── User.php
│   │   ├── Admin.php
│   │   ├── Doctor.php
│   │   ├── Patient.php
│   │   ├── Appointment.php
│   │   ├── Prescription.php
│   │   ├── Medication.php
│   │   └── Department.php
│   └── repositories/
│       ├── BaseRepository.php
│       ├── UserRepository.php
│       ├── PatientRepository.php
│       ├── DoctorRepository.php
│       ├── AppointmentRepository.php
│       ├── PrescriptionRepository.php
│       ├── MedicationRepository.php
│       └── DepartmentRepository.php
├── config/
│   └── connection.php
├── includes/
│   ├── auth.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── index.php
├── login.php
├── logout.php
├── doctors.php
├── patients.php
├── appointments.php
├── prescriptions.php
├── medications.php
├── departments.php
└── profile.php

---

## Required Features (TODO)

### 1. Authentication System

- Create an abstract `User` class with the following properties:
  - `email`
  - `username`
  - `password` (hashed)
- Create subclasses:
  - `Admin`
  - `Doctor`
  - `Patient`
- Implement login using **email + password**
- Passwords must be hashed using `password_hash()`
- Authentication verification must use `password_verify()`


---

### 2. PHP Session Management

- Use `$_SESSION` to maintain the authentication state
- Store user identity and role in session
- Protect all secured pages by checking:
  - User authentication
  - User role

---

### 3. Role-Based Access Control (RBAC)

Each role has specific permissions:

| Feature | Admin | Doctor | Patient |
|-------|-------|--------|---------|
| Manage departments | ✓ | ✗ | ✗ |
| Manage doctors | ✓ | ✗ | ✗ |
| Manage patients | ✓ | Read-only | ✗ |
| Manage medications | ✓ | ✗ | ✗ |
| View all appointments | ✓ | ✗ | ✗ |
| View own appointments | ✓ | ✓ | ✓ |
| Create appointment | ✓ | ✓ | ✓ |
| Cancel appointment | ✓ | Own | Own |
| Create prescription | ✓ | ✓ | ✗ |
| View prescriptions | ✓ | Created | Received |
| View statistics | ✓ | Limited | ✗ |

- Each protected page **must check the user role before displaying content**
- Unauthorized access must be blocked

---

### 4. Appointment Management

- Create an `Appointment` class with full CRUD operations
- Appointment properties:
  - `date`
  - `time`
  - `doctor`
  - `patient`
  - `reason`
  - `status` (`scheduled`, `done`, `cancelled`)
- Implement role-based access rules for appointment actions

---

### 5. Prescription Management

- Create the following classes:
  - `Medication`
  - `Prescription`
- A prescription must link:
  - One doctor
  - One patient
  - One medication
  - Dosage instructions
- Doctors can create prescriptions
- Patients can only view prescriptions they received

---

### 6. Web Security

- **XSS Protection**
  - Escape all dynamic outputs (`htmlspecialchars`)
- **SQL Injection Protection**
  - Use prepared statements with bound parameters (PDO)
- **CSRF Protection**
  - Generate and validate CSRF tokens on all forms
- **Password Security**
  - Use `password_hash()` for storage
  - Use `password_verify()` for authentication

---

### 7. Enriched Statistics

- Add appointment statistics:
  - By status
  - By doctor
  - Monthly evolution
- Add prescription statistics:
  - Most prescribed medications
- Statistics visibility depends on user role

---

## Technical Constraints

- PHP (OOP)
- PDO for database access
- Secure session handling
- Clean and maintainable architecture
- Strict role and permission checks

---

## Notes

- Bonus features are intentionally **excluded**
- Focus on correctness, security, and clean architecture
- All features must respect RBAC rules

---

## Author
** Ali Kara **