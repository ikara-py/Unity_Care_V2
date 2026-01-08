<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

if (!isset($skipAuthCheck)) {
    Auth::requireLogin();
}

$user = Auth::user();
if ($user === null) {
    if (!isset($skipAuthCheck)) {
        header('Location: login.php');
        exit();
    }
    $role = 'guest';
    $userName = 'Guest';
} else {
    $role = $user['role'];
    $userName = $user['first_name'] . ' ' . $user['last_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Hospital Management System</h1>
            <div class="user-info">
                <span>Welcome, <?= htmlspecialchars($userName) ?></span>
                <span class="role-badge"><?= ucfirst($role) ?></span>
                <?php if ($user !== null): ?>
                    <a href="profile.php" class="btn btn-small">Profile</a>
                    <a href="logout.php" class="btn btn-small btn-danger">Logout</a>
                <?php endif; ?>
            </div>
        </header>
        
        <?php if ($user !== null): ?>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                
                <?php if ($role === 'admin'): ?>
                    <li><a href="doctors.php">Doctors</a></li>
                    <li><a href="patients.php">Patients</a></li>
                    <li><a href="departments.php">Departments</a></li>
                    <li><a href="medications.php">Medications</a></li>
                <?php endif; ?>
                
                <?php if ($role === 'admin' || $role === 'doctor'): ?>
                    <li><a href="appointments.php">Appointments</a></li>
                    <li><a href="prescriptions.php">Prescriptions</a></li>
                <?php endif; ?>
                
                <?php if ($role === 'patient'): ?>
                    <li><a href="appointments.php">My Appointments</a></li>
                    <li><a href="prescriptions.php">My Prescriptions</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <main>