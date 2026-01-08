<?php
require_once 'includes/header.php';
require_once 'classes/repositories/DepartmentRepository.php';
require_once 'classes/models/Department.php';
$departmentRepo = new DepartmentRepository();
Auth::requireAdmin();

$departments = $departmentRepo->findAllWithStats();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = $_GET['id'] ?? null;
    
    if ($action === 'add' || ($action === 'edit' && $id)) {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitizeInput($_POST['name']),
                'location' => sanitizeInput($_POST['location'])
            ];
            
            $department = new Department($data);
            
            if ($action === 'add') {
                if ($departmentRepo->add($department)) {
                    flash('success', 'Department added successfully');
                    header('Location: departments.php');
                    exit();
                }
                flash('error', 'Failed to add department');
            } else {
                if ($departmentRepo->edit($id, $department)) {
                    flash('success', 'Department updated successfully');
                    header('Location: departments.php');
                    exit();
                }
                flash('error', 'Failed to update department');
            }
        }
        
        $department = $action === 'edit' ? $departmentRepo->find($id) : null;
        ?>
        <h2><?= $action === 'add' ? 'Add' : 'Edit' ?> Department</h2>
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= $department['name'] ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?= $department['location'] ?? '' ?>" required>
                </div>
                <div class="form-actions">
                    <a href="departments.php" class="btn btn-danger">Cancel</a>
                    <button type="submit" class="btn btn-success">
                        <?= $action === 'add' ? 'Add' : 'Update' ?> Department
                    </button>
                </div>
            </form>
        </div>
        <?php
        require_once 'includes/footer.php';
        exit();
    }
    
    if ($action === 'delete' && $id) {
        if ($departmentRepo->delete($id)) {
            flash('success', 'Department deleted successfully');
        } else {
            flash('error', 'Failed to delete department');
        }
        header('Location: departments.php');
        exit();
    }
}
?>
<h2>Manage Departments</h2>
<div class="mb-2">
    <a href="departments.php?action=add" class="btn btn-success">Add New Department</a>
</div>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Doctor Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departments as $department): ?>
                <tr>
                    <td><?= htmlspecialchars($department['name']) ?></td>
                    <td><?= htmlspecialchars($department['location']) ?></td>
                    <td><?= htmlspecialchars($department['doctor_count']) ?></td>
                    <td>
                        <a href="departments.php?action=edit&id=<?= $department['id'] ?>" class="btn btn-small btn-warning">Edit</a>
                        <a href="departments.php?action=delete&id=<?= $department['id'] ?>" class="btn btn-small btn-danger delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
require_once 'includes/footer.php';
?>