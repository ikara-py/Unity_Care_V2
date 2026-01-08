<?php
$skipAuthCheck = true;
require_once 'includes/header.php';
require_once 'classes/repositories/UserRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    $userRepo = new UserRepository();
    $user = $userRepo->authenticate($email, $password);
    
    if ($user) {
        Auth::login($user['id']);
        $redirect = $_GET['redirect'] ?? 'index.php';
        header('Location: ' . $redirect);
        exit();
    } else {
        flash('error', 'Invalid email or password');
    }
}
?>
        <div class="login-container">
            <h2>Login</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>
            </form>
        </div>
<?php
require_once 'includes/footer.php';
?>