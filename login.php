<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user->password)) {
            // Login success
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_role'] = $user->role;
            
            if ($user->role === 'admin') {
                redirect('admin/index.php');
            } else {
                // If they have items in cart, maybe redirect to cart, else index
                redirect('index.php');
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="form-container" style="margin-top: 40px; margin-bottom: 40px;">
        <h2>Login to your account</h2>
        <p>Welcome back to Campus Gebeta.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= h($_POST['email'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        <p style="margin-top: 20px; text-align: center;">Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register here</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
