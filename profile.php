<?php
require_once 'includes/header.php';
require_once 'classes/repositories/UserRepository.php';

$role = $user['role'];
if ($role === 'doctor') {
    require_once 'classes/models/Doctor.php';
} elseif ($role === 'patient') {
    require_once 'classes/models/Patient.php';
} else {
    require_once 'classes/models/Admin.php';
}

$userRepo = new UserRepository();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $data = [
            'email' => sanitizeInput($_POST['email']),
            'first_name' => sanitizeInput($_POST['first_name']),
            'last_name' => sanitizeInput($_POST['last_name']),
            'username' => sanitizeInput($_POST['username']),
            'role' => $user['role']
        ];
        if ($role === 'doctor') {
            $updatedUser = new Doctor($data);
        } elseif ($role === 'patient') {
            $updatedUser = new Patient($data);
        } else {
            $updatedUser = new Admin($data);
        }
        
        if ($userRepo->edit($user['id'], $updatedUser)) {
            $_SESSION['user_first_name'] = $data['first_name'];
            $_SESSION['user_last_name'] = $data['last_name'];
            flash('success', 'Profile updated successfully');
        } else {
            flash('error', 'Failed to update profile');
        }
        header('Location: profile.php');
        exit();
        
    } elseif ($action === 'change_password') {
        $current = $_POST['current_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if ($new !== $confirm) {
            flash('error', 'New passwords do not match');
        } elseif (password_verify($current, $user['password'])) {
            if ($userRepo->changePassword($user['id'], $new)) {
                flash('success', 'Password changed successfully');
            } else {
                flash('error', 'Failed to change password');
            }
        } else {
            flash('error', 'Current password is incorrect');
        }
        header('Location: profile.php');
        exit();
    }
}
?>
<h2>My Profile</h2>
<div class="form-container">
    <form method="POST" action="">
        <input type="hidden" name="action" value="update_profile">
        
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?= $user['first_name'] ?>" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= $user['last_name'] ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= $user['username'] ?>" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <input type="text" value="<?= ucfirst($user['role']) ?>" disabled>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-success">Update Profile</button>
        </div>
    </form>
</div>

<h3>Change Password</h3>
<div class="form-container">
    <form method="POST" action="">
        <input type="hidden" name="action" value="change_password">
        
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-warning">Change Password</button>
        </div>
    </form>
</div>
<?php
require_once 'includes/footer.php';
?>