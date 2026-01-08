<?php
require_once 'includes/header.php';
require_once 'classes/repositories/PatientRepository.php';
require_once 'classes/repositories/DoctorRepository.php';
require_once 'classes/repositories/UserRepository.php';
require_once 'classes/repositories/AppointmentRepository.php';
require_once 'classes/repositories/PrescriptionRepository.php';
require_once 'classes/models/Patient.php';

$patientRepo = new PatientRepository();
$doctorRepo = new DoctorRepository();

if ($user['role'] === 'admin' || $user['role'] === 'doctor') {
    
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        $id = $_GET['id'] ?? null;
        if ($action === 'delete' && $id && $user['role'] === 'admin') {
            $appointmentRepo = new AppointmentRepository();
            $prescriptionRepo = new PrescriptionRepository();
            
            $hasAppointments = count($appointmentRepo->findByPatient($id)) > 0;
            $hasPrescriptions = count($prescriptionRepo->findByPatient($id)) > 0;
            
            if ($hasAppointments || $hasPrescriptions) {
                flash('error', 'Cannot delete patient with existing appointments or prescriptions. Delete those first.');
            } else {
                if ($patientRepo->delete($id)) {
                    flash('success', 'Patient deleted successfully');
                } else {
                    flash('error', 'Failed to delete patient');
                }
            }
            header('Location: patients.php');
            exit();
        }
        
        if (($action === 'add' && $user['role'] === 'admin') || 
            ($action === 'edit' && $id)) {
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'email' => sanitizeInput($_POST['email']),
                    'first_name' => sanitizeInput($_POST['first_name']),
                    'last_name' => sanitizeInput($_POST['last_name']),
                    'username' => sanitizeInput($_POST['username']),
                    'password' => $_POST['password'],
                    'role' => 'patient',
                    'date_of_birth' => $_POST['date_of_birth'],
                    'phone' => sanitizeInput($_POST['phone'])
                ];
                
                $userRepo = new UserRepository();
                
                if ($action === 'add') {
                    $userId = $userRepo->add(new Patient($data));
                    if ($userId) {
                        $data['id'] = $userId;
                        $patient = new Patient($data);
                        if ($patientRepo->add($patient)) {
                            flash('success', 'Patient added successfully');
                            header('Location: patients.php');
                            exit();
                        }
                    }
                    flash('error', 'Failed to add patient');
                } else {
                    $userUpdateData = [
                        'email' => $data['email'],
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'username' => $data['username'],
                        'password' => '',
                        'role' => 'patient'
                    ];
                    $userRepo->edit($id, new Patient($userUpdateData));
                    if (!empty($data['password'])) {
                        $userRepo->changePassword($id, $data['password']);
                    }
                    $patient = new Patient($data);
                    if ($patientRepo->edit($id, $patient)) {
                        flash('success', 'Patient updated successfully');
                        header('Location: patients.php');
                        exit();
                    }
                    flash('error', 'Failed to update patient');
                }
            }
            
            $patient = $action === 'edit' ? $patientRepo->find($id) : null;
            ?>
            <h2><?= $action === 'add' ? 'Add' : 'Edit' ?> Patient</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?= $patient['first_name'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?= $patient['last_name'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $patient['email'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= $patient['username'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password <?= $action === 'edit' ? '(leave blank to keep current)' : '' ?></label>
                        <input type="password" name="password" <?= $action === 'add' ? 'required' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" value="<?= $patient['date_of_birth'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?= $patient['phone'] ?? '' ?>" required>
                    </div>
                    <div class="form-actions">
                        <a href="patients.php" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-success">
                            <?= $action === 'add' ? 'Add' : 'Update' ?> Patient
                        </button>
                    </div>
                </form>
            </div>
            <?php
            require_once 'includes/footer.php';
            exit();
        }
    }
    
    $patients = $patientRepo->findAll();
    ?>
    <h2>Manage Patients</h2>
    <?php if ($user['role'] === 'admin'): ?>
        <div class="mb-2">
            <a href="patients.php?action=add" class="btn btn-success">Add New Patient</a>
        </div>
    <?php endif; ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date of Birth</th>
                    <?php if ($user['role'] === 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                        <td><?= htmlspecialchars($patient['email']) ?></td>
                        <td><?= htmlspecialchars($patient['phone']) ?></td>
                        <td><?= formatDate($patient['date_of_birth']) ?></td>
                        <?php if ($user['role'] === 'admin'): ?>
                            <td>
                                <a href="patients.php?action=edit&id=<?= $patient['id'] ?>" class="btn btn-small btn-warning">Edit</a>
                                <a href="patients.php?action=delete&id=<?= $patient['id'] ?>" class="btn btn-small btn-danger delete-btn">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    
} elseif ($user['role'] === 'patient') {
    $patient = $patientRepo->find($user['id']);
    $appointments = $patientRepo->findAppointments($user['id']);
    $prescriptions = $patientRepo->findPrescriptions($user['id']);
    ?>
    <h2>My Profile</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>Name</th>
                <td><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($patient['email']) ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?= htmlspecialchars($patient['phone']) ?></td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td><?= formatDate($patient['date_of_birth']) ?></td>
            </tr>
        </table>
    </div>
    
    <h3>My Appointments</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= formatDate($appointment['appointment_date']) ?></td>
                        <td><?= formatTime($appointment['appointment_time']) ?></td>
                        <td><?= htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']) ?></td>
                        <td><?= htmlspecialchars($appointment['reason']) ?></td>
                        <td><?= htmlspecialchars($appointment['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <h3>My Prescriptions</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Instructions</th>
                    <th>Doctor</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td><?= htmlspecialchars($prescription['medication_name']) ?></td>
                        <td><?= htmlspecialchars($prescription['dosage_instructions']) ?></td>
                        <td><?= htmlspecialchars($prescription['doctor_first_name'] . ' ' . $prescription['doctor_last_name']) ?></td>
                        <td><?= formatDate($prescription['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

require_once 'includes/footer.php';
?>