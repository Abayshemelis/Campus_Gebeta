<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'student';

    // Only allow valid roles via registration
    $allowed_roles = ['student', 'seller', 'lecturer'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'student';
    }

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed, $role])) {
                $success = 'Registration successful! You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="form-container" style="margin-top: 40px; margin-bottom: 40px;">
        <h2>Create an Account</h2>
        <p>Join Campus Gebeta — Order meals, sell items, or post notices.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php else: ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= h($_POST['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= h($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="role">I am a...</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="student"  <?= ($_POST['role'] ?? '') == 'student'  ? 'selected' : '' ?>>🎓 Student — Browse menu, order food, view notices</option>
                        <option value="seller"   <?= ($_POST['role'] ?? '') == 'seller'   ? 'selected' : '' ?>>🛍️ Seller — Post items on Gebeta Market</option>
                        <option value="lecturer" <?= ($_POST['role'] ?? '') == 'lecturer' ? 'selected' : '' ?>>📚 Lecturer — Post announcements & events</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <!-- Role info box -->
                <div id="roleInfo" style="margin-bottom: 20px; padding: 12px 15px; border-radius: 8px; font-size: 0.88rem; background: var(--light-color); border-left: 4px solid var(--primary-color);">
                    <strong>Student:</strong> Browse the menu, place orders, save favorites, manage wallet, and post Lost &amp; Found notices.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            </form>
            <p style="margin-top: 20px; text-align: center;">Already have an account? <a href="login.php" style="color: var(--primary-color);">Login here</a></p>
        <?php endif; ?>
    </div>
</div>

<script>
const roleSelect = document.getElementById('role');
const roleInfo   = document.getElementById('roleInfo');
const descriptions = {
    student:  '<strong>Student:</strong> Browse the menu, place orders, save favorites, manage wallet, and post Lost &amp; Found notices.',
    seller:   '<strong>Seller:</strong> List items on Gebeta Market for other students to find and contact you.',
    lecturer: '<strong>Lecturer:</strong> Post official announcements and campus events to the Noticeboard.'
};
const colors = { student: '#457b9d', seller: '#f4a261', lecturer: '#2a9d8f' };
roleSelect.addEventListener('change', function () {
    roleInfo.innerHTML = descriptions[this.value] || '';
    roleInfo.style.borderLeftColor = colors[this.value] || 'var(--primary-color)';
});
</script>

<?php require_once 'includes/footer.php'; ?>
