<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($name) || empty($email)) {
            $error = "Name and email are required.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $user_id]);
                $_SESSION['user_name'] = $name; // Update session
                $user->name = $name;
                $user->email = $email;
                $success = "Profile updated successfully!";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = "Email is already taken by another account.";
                } else {
                    $error = "An error occurred while updating profile.";
                }
            }
        }
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All password fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $error = "New password must be at least 6 characters long.";
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $hash = $stmt->fetchColumn();
            
            if (password_verify($current_password, $hash)) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$new_hash, $user_id]);
                $success = "Password updated successfully!";
            } else {
                $error = "Incorrect current password.";
            }
        }
    }
}
?>

<div class="container" style="padding: 40px 0; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i class="fa-solid fa-gear" style="color: var(--primary-color);"></i> Account Settings</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <!-- Profile Settings -->
        <div class="card reveal active" style="border-radius: 15px;">
            <h3 style="margin-bottom: 20px;">Profile Information</h3>
            <form method="POST" action="settings.php">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= h($user->name) ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= h($user->email) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
            </form>
        </div>

        <!-- Password Settings -->
        <div class="card reveal active" style="border-radius: 15px;">
            <h3 style="margin-bottom: 20px;">Change Password</h3>
            <form method="POST" action="settings.php">
                <input type="hidden" name="update_password" value="1">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-secondary" style="width: 100%;">Change Password</button>
            </form>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
