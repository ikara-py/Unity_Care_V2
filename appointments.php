<?php
require_once 'includes/header.php';
require_once 'classes/repositories/AppointmentRepository.php';
require_once 'classes/repositories/DoctorRepository.php';
require_once 'classes/repositories/PatientRepository.php';
require_once 'classes/models/Appointment.php';

$appointmentRepo = new AppointmentRepository();
$doctorRepo = new DoctorRepository();
$patientRepo = new PatientRepository();

if ($user['role'] === 'admin') {
    $appointments = $appointmentRepo->findAll();
    $doctors = $doctorRepo->findAll();
    $patients = $patientRepo->findAll();
} elseif ($user['role'] === 'doctor') {
    $appointments = $appointmentRepo->findByDoctor($user['id']);
    $doctors = [$doctorRepo->find($user['id'])];
    $patients = $patientRepo->findAll();
} elseif ($user['role'] === 'patient') {
    $appointments = $appointmentRepo->findByPatient($user['id']);
    $doctors = $doctorRepo->findAll();
    $patients = [$patientRepo->find($user['id'])];
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;

    if ($action === 'update_status' && $id && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = sanitizeInput($_POST['status']);
        if ($appointmentRepo->updateStatus($id, $status)) {
            flash('success', 'Status updated successfully');
        } else {
            flash('error', 'Failed to update status');
        }
        header('Location: appointments.php');
        exit();
    }
    
    if ($action === 'add' || ($action === 'edit' && $id)) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'appointment_date' => $_POST['appointment_date'],
                'appointment_time' => $_POST['appointment_time'],
                'doctor_id' => (int)$_POST['doctor_id'],
                'patient_id' => (int)$_POST['patient_id'],
                'reason' => sanitizeInput($_POST['reason']),
                'status' => $_POST['status'] ?? 'scheduled'
            ];
            
            $appointment = new Appointment($data);
            
            if ($action === 'add') {
                if ($appointmentRepo->add($appointment)) {
                    flash('success', 'Appointment created successfully');
                    header('Location: appointments.php');
                    exit();
                }
                flash('error', 'Failed to create appointment');
            } else {
                if ($appointmentRepo->edit($id, $appointment)) {
                    flash('success', 'Appointment updated successfully');
                    header('Location: appointments.php');
                    exit();
                }
                flash('error', 'Failed to update appointment');
            }
        }
        
        $appointment = $action === 'edit' ? $appointmentRepo->find($id) : null;
        ?>
        <h2><?= $action === 'add' ? 'Add' : 'Edit' ?> Appointment</h2>
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="appointment_date" value="<?= $appointment['appointment_date'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="appointment_time" value="<?= $appointment['appointment_time'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Doctor</label>
                    <select name="doctor_id" required>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['id'] ?>" 
                                <?= isset($appointment) && $appointment['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Patient</label>
                    <select name="patient_id" required>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= $patient['id'] ?>" 
                                <?= isset($appointment) && $appointment['patient_id'] == $patient['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" required><?= $appointment['reason'] ?? '' ?></textarea>
                </div>
                <?php if ($user['role'] !== 'patient'): ?>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="scheduled" <?= isset($appointment) && $appointment['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                            <option value="done" <?= isset($appointment) && $appointment['status'] === 'done' ? 'selected' : '' ?>>Done</option>
                            <option value="cancelled" <?= isset($appointment) && $appointment['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="form-actions">
                    <a href="appointments.php" class="btn btn-danger">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <?= $action === 'add' ? 'Add' : 'Update' ?> Appointment
                    </button>
                </div>
            </form>
        </div>
        <?php
        require_once 'includes/footer.php';
        exit();
    }
    
    if ($action === 'delete' && $id && $user['role'] === 'admin') {
        if ($appointmentRepo->delete($id)) {
            flash('success', 'Appointment deleted successfully');
        } else {
            flash('error', 'Failed to delete appointment');
        }
        header('Location: appointments.php');
        exit();
    }
}
?>
<h2>Appointments</h2>
<?php if ($user['role'] !== 'patient'): ?>
    <div class="mb-2">
        <a href="appointments.php?action=add" class="btn btn-success">Add New Appointment</a>
    </div>
<?php endif; ?>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <?php if ($user['role'] !== 'patient'): ?>
                    <th>Patient</th>
                <?php endif; ?>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= formatDate($appointment['appointment_date']) ?></td>
                    <td><?= formatTime($appointment['appointment_time']) ?></td>
                    <td><?= htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']) ?></td>
                    <?php if ($user['role'] !== 'patient'): ?>
                        <td><?= htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($appointment['reason']) ?></td>
                    <td><?= htmlspecialchars($appointment['status']) ?></td>
                    <td>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="appointments.php?action=edit&id=<?= $appointment['id'] ?>" class="btn btn-small btn-warning">Edit</a>
                            <a href="appointments.php?action=delete&id=<?= $appointment['id'] ?>" class="btn btn-small btn-danger delete-btn">Delete</a>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'doctor' && $appointment['doctor_id'] == $user['id']): ?>
                            <form method="POST" action="appointments.php?action=update_status&id=<?= $appointment['id'] ?>" style="display: inline;">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="scheduled" <?= $appointment['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                    <option value="done" <?= $appointment['status'] === 'done' ? 'selected' : '' ?>>Done</option>
                                    <option value="cancelled" <?= $appointment['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
require_once 'includes/footer.php';
?>