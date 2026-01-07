<?php
require_once '../config/connection.php';
require_once '../classes/repositories/DoctorRepository.php';
require_once '../classes/repositories/PatientRepository.php';
require_once '../classes/repositories/AppointmentRepository.php';
require_once '../classes/repositories/PrescriptionRepository.php';
require_once '../classes/repositories/MedicationRepository.php';
require_once '../classes/repositories/DepartmentRepository.php';

$doctorRepo      = new DoctorRepository();
$patientRepo     = new PatientRepository();
$appointmentRepo = new AppointmentRepository();
$prescriptionRepo= new PrescriptionRepository();
$medicationRepo  = new MedicationRepository();
$departmentRepo  = new DepartmentRepository();

$doctors      = $doctorRepo->findAll();
$patients     = $patientRepo->findAll();
$appointments = $appointmentRepo->findAll();
$prescriptions= $prescriptionRepo->findAll();
$medications  = $medicationRepo->findAll();
$departments  = $departmentRepo->findAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Clinic Data</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
  <h1>Clinic Overview</h1>
</header>

<main>
<section>
  <h2>Doctors</h2>
  <table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Specialty</th><th>Department</th></tr></thead>
    <tbody>
    <?php foreach ($doctors as $d): ?>
      <tr>
        <td><?= $d['id'] ?></td>
        <td><?= $d['first_name'].' '.$d['last_name'] ?></td>
        <td><?= $d['email'] ?></td>
        <td><?= $d['specialty'] ?></td>
        <td><?= $d['department_name'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <h2>Patients</h2>
  <table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>DOB</th><th>Phone</th></tr></thead>
    <tbody>
    <?php foreach ($patients as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= $p['first_name'].' '.$p['last_name'] ?></td>
        <td><?= $p['email'] ?></td>
        <td><?= $p['date_of_birth'] ?></td>
        <td><?= $p['phone'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <h2>Appointments</h2>
  <table>
    <thead><tr><th>ID</th><th>Date</th><th>Time</th><th>Doctor</th><th>Patient</th><th>Reason</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($appointments as $a): ?>
      <tr>
        <td><?= $a['id'] ?></td>
        <td><?= $a['appointment_date'] ?></td>
        <td><?= $a['appointment_time'] ?></td>
        <td><?= $a['doctor_first_name'].' '.$a['doctor_last_name'] ?></td>
        <td><?= $a['patient_first_name'].' '.$a['patient_last_name'] ?></td>
        <td><?= $a['reason'] ?></td>
        <td><?= $a['status'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <h2>Prescriptions</h2>
  <table>
    <thead><tr><th>ID</th><th>Medication</th><th>Doctor</th><th>Patient</th><th>Instructions</th><th>Created</th></tr></thead>
    <tbody>
    <?php foreach ($prescriptions as $pr): ?>
      <tr>
        <td><?= $pr['id'] ?></td>
        <td><?= $pr['medication_name'] ?></td>
        <td><?= $pr['doctor_first_name'].' '.$pr['doctor_last_name'] ?></td>
        <td><?= $pr['patient_first_name'].' '.$pr['patient_last_name'] ?></td>
        <td><?= $pr['dosage_instructions'] ?></td>
        <td><?= $pr['created_at'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <h2>Medications</h2>
  <table>
    <thead><tr><th>ID</th><th>Name</th><th>Description</th></tr></thead>
    <tbody>
    <?php foreach ($medications as $m): ?>
      <tr>
        <td><?= $m['id'] ?></td>
        <td><?= $m['name'] ?></td>
        <td><?= $m['description'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <h2>Departments</h2>
  <table>
    <thead><tr><th>ID</th><th>Name</th><th>Location</th></tr></thead>
    <tbody>
    <?php foreach ($departments as $dept): ?>
      <tr>
        <td><?= $dept['id'] ?></td>
        <td><?= $dept['name'] ?></td>
        <td><?= $dept['location'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
</main>

<footer>
  <p> &copy; <?= date('Y') ?></p>
</footer>
</body>
</html>