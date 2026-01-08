<?php
require_once 'includes/header.php';
require_once 'classes/repositories/PrescriptionRepository.php';
require_once 'classes/repositories/DoctorRepository.php';
require_once 'classes/repositories/PatientRepository.php';
require_once 'classes/repositories/MedicationRepository.php';
require_once 'classes/models/Prescription.php';

$prescriptionRepo = new PrescriptionRepository();
$doctorRepo = new DoctorRepository();
$patientRepo = new PatientRepository();
$medicationRepo = new MedicationRepository();

if ($user['role'] === 'admin') {
    $prescriptions = $prescriptionRepo->findAll();
    $doctors = $doctorRepo->findAll();
    $patients = $patientRepo->findAll();
} elseif ($user['role'] === 'doctor') {
    $prescriptions = $prescriptionRepo->findByDoctor($user['id']);
    $doctors = [$doctorRepo->find($user['id'])];
    $patients = $patientRepo->findAll();
} elseif ($user['role'] === 'patient') {
    $prescriptions = $prescriptionRepo->findByPatient($user['id']);
    $doctors = $doctorRepo->findAll();
    $patients = [$patientRepo->find($user['id'])];
}

$medications = $medicationRepo->findAll();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;
    
    if ($action === 'add' || ($action === 'edit' && $id)) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'doctor_id' => (int)$_POST['doctor_id'],
                'patient_id' => (int)$_POST['patient_id'],
                'medication_id' => (int)$_POST['medication_id'],
                'dosage_instructions' => sanitizeInput($_POST['dosage_instructions'])
            ];
            
            $prescription = new Prescription($data);
            
            if ($action === 'add') {
                if ($prescriptionRepo->add($prescription)) {
                    flash('success', 'Prescription created successfully');
                    header('Location: prescriptions.php');
                    exit();
                }
                flash('error', 'Failed to create prescription');
            } else {
                if ($prescriptionRepo->edit($id, $prescription)) {
                    flash('success', 'Prescription updated successfully');
                    header('Location: prescriptions.php');
                    exit();
                }
                flash('error', 'Failed to update prescription');
            }
        }
        
        $prescription = $action === 'edit' ? $prescriptionRepo->find($id) : null;
        ?>
        <h2><?= $action === 'add' ? 'Add' : 'Edit' ?> Prescription</h2>
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Doctor</label>
                    <select name="doctor_id" required>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['id'] ?>" <?= isset($prescription) && $prescription['doctor_id'] == $doctor['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Patient</label>
                    <select name="patient_id" required>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= $patient['id'] ?>" <?= isset($prescription) && $prescription['patient_id'] == $patient['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Medication</label>
                    <select name="medication_id" required>
                        <?php foreach ($medications as $medication): ?>
                            <option value="<?= $medication['id'] ?>" <?= isset($prescription) && $prescription['medication_id'] == $medication['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($medication['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Dosage Instructions</label>
                    <textarea name="dosage_instructions" required><?= $prescription['dosage_instructions'] ?? '' ?></textarea>
                </div>
                <div class="form-actions">
                    <a href="prescriptions.php" class="btn btn-danger">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <?= $action === 'add' ? 'Add' : 'Update' ?> Prescription
                    </button>
                </div>
            </form>
        </div>
        <?php
        require_once 'includes/footer.php';
        exit();
    }
    
    if ($action === 'delete' && $id && $user['role'] === 'admin') {
        if ($prescriptionRepo->delete($id)) {
            flash('success', 'Prescription deleted successfully');
        } else {
            flash('error', 'Failed to delete prescription');
        }
        header('Location: prescriptions.php');
        exit();
    }
}
?>
<h2>Prescriptions</h2>
<?php if ($user['role'] !== 'patient'): ?>
    <div class="mb-2">
        <a href="prescriptions.php?action=add" class="btn btn-success">Add New Prescription</a>
    </div>
<?php endif; ?>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Doctor</th>
                <?php if ($user['role'] !== 'patient'): ?>
                    <th>Patient</th>
                <?php endif; ?>
                <th>Medication</th>
                <th>Instructions</th>
                <th>Date</th>
                <?php if ($user['role'] === 'admin'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prescriptions as $prescription): ?>
                <tr>
                    <td><?= htmlspecialchars($prescription['doctor_first_name'] . ' ' . $prescription['doctor_last_name']) ?></td>
                    <?php if ($user['role'] !== 'patient'): ?>
                        <td><?= htmlspecialchars($prescription['patient_first_name'] . ' ' . $prescription['patient_last_name']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($prescription['medication_name']) ?></td>
                    <td><?= htmlspecialchars($prescription['dosage_instructions']) ?></td>
                    <td><?= formatDate($prescription['created_at']) ?></td>
                    <?php if ($user['role'] === 'admin'): ?>
                        <td>
                            <a href="prescriptions.php?action=edit&id=<?= $prescription['id'] ?>" class="btn btn-small btn-warning">Edit</a>
                            <a href="prescriptions.php?action=delete&id=<?= $prescription['id'] ?>" class="btn btn-small btn-danger delete-btn">Delete</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
require_once 'includes/footer.php';
?>