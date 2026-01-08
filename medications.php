<?php
require_once 'includes/header.php';
require_once 'classes/repositories/MedicationRepository.php';
require_once 'classes/models/Medication.php';

$medicationRepo = new MedicationRepository();
Auth::requireAdmin();

$medications = $medicationRepo->findAll();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;
    
    if ($action === 'add' || ($action === 'edit' && $id)) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitizeInput($_POST['name']),
                'description' => sanitizeInput($_POST['description'])
            ];
            
            $medication = new Medication($data);
            
            if ($action === 'add') {
                if ($medicationRepo->add($medication)) {
                    flash('success', 'Medication added successfully');
                    header('Location: medications.php');
                    exit();
                }
                flash('error', 'Failed to add medication');
            } else {
                if ($medicationRepo->edit($id, $medication)) {
                    flash('success', 'Medication updated successfully');
                    header('Location: medications.php');
                    exit();
                }
                flash('error', 'Failed to update medication');
            }
        }
        
        $medication = $action === 'edit' ? $medicationRepo->find($id) : null;
        ?>
        <h2><?= $action === 'add' ? 'Add' : 'Edit' ?> Medication</h2>
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= $medication['name'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required><?= $medication['description'] ?? '' ?></textarea>
                </div>
                <div class="form-actions">
                    <a href="medications.php" class="btn btn-danger">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <?= $action === 'add' ? 'Add' : 'Update' ?> Medication
                    </button>
                </div>
            </form>
        </div>
        <?php
        require_once 'includes/footer.php';
        exit();
    }
    
    if ($action === 'delete' && $id) {
        if ($medicationRepo->delete($id)) {
            flash('success', 'Medication deleted successfully');
        } else {
            flash('error', 'Failed to delete medication');
        }
        header('Location: medications.php');
        exit();
    }
}
?>
<h2>Manage Medications</h2>
<div class="mb-2">
    <a href="medications.php?action=add" class="btn btn-success">Add New Medication</a>
</div>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medications as $medication): ?>
                <tr>
                    <td><?= htmlspecialchars($medication['name']) ?></td>
                    <td><?= htmlspecialchars($medication['description']) ?></td>
                    <td>
                        <a href="medications.php?action=edit&id=<?= $medication['id'] ?>" class="btn btn-small btn-warning">Edit</a>
                        <a href="medications.php?action=delete&id=<?= $medication['id'] ?>" class="btn btn-small btn-danger delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
require_once 'includes/footer.php';
?>