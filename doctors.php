<?php
require_once 'includes/header.php';
require_once 'classes/repositories/DoctorRepository.php';
require_once 'classes/repositories/DepartmentRepository.php';
require_once 'classes/repositories/UserRepository.php';
require_once 'classes/models/Doctor.php';
require_once __DIR__ . '/vendor/autoload.php';

$doctorRepo = new DoctorRepository();
$departmentRepo = new DepartmentRepository();

if ($user['role'] === 'admin') {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        $id = $_GET['id'] ?? null;
        
        if ($action === 'delete' && $id) {
            if ($doctorRepo->delete($id)) {
                flash('success', 'Doctor deleted successfully');
            } else {
                flash('error', 'Failed to delete doctor');
            }
            header('Location: doctors.php');
            exit();
        }
        
        if ($action === 'add' || ($action === 'edit' && $id)) {
            $departments = $departmentRepo->findAll();
            $userRepo = new UserRepository();
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'email' => sanitizeInput($_POST['email']),
                    'first_name' => sanitizeInput($_POST['first_name']),
                    'last_name' => sanitizeInput($_POST['last_name']),
                    'username' => sanitizeInput($_POST['username']),
                    'password' => $_POST['password'],
                    'role' => 'doctor',
                    'department_id' => (int)$_POST['department_id'],
                    'specialty' => sanitizeInput($_POST['specialty'])
                ];
                
                if ($action === 'add') {
                    $userId = $userRepo->add(new Doctor($data));
                    if ($userId) {
                        $data['id'] = $userId;
                        $doctor = new Doctor($data);
                        if ($doctorRepo->add($doctor)) {
                            flash('success', 'Doctor added successfully');
                            header('Location: doctors.php');
                            exit();
                        }
                    }
                    flash('error', 'Failed to add doctor');
                } else {
                    $userUpdateData = [
                        'email' => $data['email'],
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'username' => $data['username'],
                        'password' => '',
                        'role' => 'doctor'
                    ];
                    $doctorForUser = new Doctor($userUpdateData);
                    $userRepo->edit($id, $doctorForUser);
                    
                    if (!empty($data['password'])) {
                        $userRepo->changePassword($id, $data['password']);
                    }
                    $doctor = new Doctor($data);
                    if ($doctorRepo->edit($id, $doctor)) {
                        flash('success', 'Doctor updated successfully');
                        header('Location: doctors.php');
                        exit();
                    }
                    flash('error', 'Failed to update doctor');
                }
            }
            
            $doctor = $action === 'edit' ? $doctorRepo->find($id) : null;
            ?>
            <h2><?= $action === 'add' ? 'Add' : 'Edit' ?> Doctor</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?= $doctor['first_name'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?= $doctor['last_name'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $doctor['email'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="<?= $doctor['username'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password <?= $action === 'edit' ? '(leave blank to keep current)' : '' ?></label>
                        <input type="password" name="password" <?= $action === 'add' ? 'required' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department_id" required>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= $dept['id'] ?>" 
                                    <?= isset($doctor) && $doctor['department_id'] == $dept['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dept['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Specialty</label>
                        <input type="text" name="specialty" value="<?= $doctor['specialty'] ?? '' ?>" required>
                    </div>
                    <div class="form-actions">
                        <a href="doctors.php" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-success">
                            <?= $action === 'add' ? 'Add' : 'Update' ?> Doctor
                        </button>
                    </div>
                </form>
            </div>
            <?php
            require_once 'includes/footer.php';
            exit();
        }
    }
    
    $doctors = $doctorRepo->findAll();
    ?>
    <h2>Manage Doctors</h2>
    <div class="mb-2">
        <a href="doctors.php?action=add" class="btn btn-success">Add New Doctor</a>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Specialty</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></td>
                        <td><?= htmlspecialchars($doctor['email']) ?></td>
                        <td><?= htmlspecialchars($doctor['department_name']) ?></td>
                        <td><?= htmlspecialchars($doctor['specialty']) ?></td>
                        <td>
                            <a href="doctors.php?action=edit&id=<?= $doctor['id'] ?>" class="btn btn-small btn-warning">Edit</a>
                            <a href="doctors.php?action=delete&id=<?= $doctor['id'] ?>" class="btn btn-small btn-danger delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    
} elseif ($user['role'] === 'doctor') {
    $doctor = $doctorRepo->find($user['id']);
    ?>
    <h2>My Profile</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>Name</th>
                <td><?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($doctor['email']) ?></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><?= htmlspecialchars($doctor['department_name']) ?></td>
            </tr>
            <tr>
                <th>Specialty</th>
                <td><?= htmlspecialchars($doctor['specialty']) ?></td>
            </tr>
        </table>
    </div>
    <?php
}

require_once 'includes/footer.php';
?>