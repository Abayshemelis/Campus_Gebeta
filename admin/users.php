<?php
require_once 'header.php';

$success = '';
$error   = '';

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $uid = (int)($_POST['user_id'] ?? 0);

    if ($_POST['action'] === 'change_role') {
        $new_role = $_POST['role'] ?? '';
        $allowed  = ['student', 'seller', 'admin'];
        if ($uid && in_array($new_role, $allowed)) {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$new_role, $uid]);
            $success = "User role updated successfully.";
        } else {
            $error = "Invalid role.";
        }
    } elseif ($_POST['action'] === 'toggle_status') {
        $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $user = $stmt->fetch();
        if ($user) {
            $new_status = ($user->status === 'active') ? 'suspended' : 'active';
            $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$new_status, $uid]);
            $success = "User status updated to " . ucfirst($new_status) . ".";
        }
    } elseif ($_POST['action'] === 'delete_user') {
        if ($uid && $uid !== (int)$_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
            $success = "User deleted.";
        } else {
            $error = "You cannot delete your own account.";
        }
    }
}

// Filters
$filter_role   = $_GET['role']   ?? '';
$filter_search = trim($_GET['search'] ?? '');

$query  = "SELECT * FROM users WHERE 1=1";
$params = [];
if ($filter_role) {
    $query .= " AND role = ?";
    $params[] = $filter_role;
}
if ($filter_search) {
    $query .= " AND (name LIKE ? OR email LIKE ?)";
    $params[] = "%$filter_search%";
    $params[] = "%$filter_search%";
}
$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Role counts
$role_counts = [];
foreach (['admin', 'seller', 'student'] as $r) {
    $s = $pdo->prepare("SELECT COUNT(*) as c FROM users WHERE role = ?");
    $s->execute([$r]);
    $role_counts[$r] = $s->fetch()->c;
}
?>

<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:15px;">
        <h2><i class="fa-solid fa-users-gear" style="color:var(--primary-color);"></i> Manage Users</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>

    <!-- Role Summary Cards -->
    <div class="admin-stat-grid" style="margin-bottom:30px;">
        <?php
        $card_data = [
            ['role' => 'admin',    'icon' => 'fa-user-shield',  'color' => '#e63946', 'label' => 'Admins'],
            ['role' => 'seller',   'icon' => 'fa-tag',          'color' => '#f4a261', 'label' => 'Sellers'],
            ['role' => 'student',  'icon' => 'fa-graduation-cap', 'color' => '#457b9d', 'label' => 'Students'],
        ];
        foreach ($card_data as $c): ?>
            <div class="admin-stat-card" style="border-top-color:<?= $c['color'] ?>;">
                <div class="stat-icon" style="color:<?= $c['color'] ?>;"><i class="fa-solid <?= $c['icon'] ?>"></i></div>
                <h3><?= $role_counts[$c['role']] ?></h3>
                <p><?= $c['label'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Search & Filter -->
    <div class="card" style="margin-bottom:25px;">
        <form method="GET" action="users.php" style="display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:220px;">
                <label style="font-weight:500; display:block; margin-bottom:6px;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or email..." value="<?= h($filter_search) ?>">
            </div>
            <div style="min-width:160px;">
                <label style="font-weight:500; display:block; margin-bottom:6px;">Filter by Role</label>
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    <option value="admin" <?= $filter_role == 'admin'    ? 'selected' : '' ?>>Admin</option>
                    <option value="seller" <?= $filter_role == 'seller'   ? 'selected' : '' ?>>Seller</option>
                    <option value="student" <?= $filter_role == 'student'  ? 'selected' : '' ?>>Student</option>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Search</button>
                <?php if ($filter_role || $filter_search): ?>
                    <a href="users.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:var(--gray); padding:30px;">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <?php
                        $roleColors = ['admin' => '#e63946', 'seller' => '#f4a261', 'student' => '#457b9d'];
                        $rc = $roleColors[$u->role] ?? '#888';
                        $isCurrentUser = ($u->id == $_SESSION['user_id']);
                        $status = $u->status ?? 'active';
                        ?>
                        <tr>
                            <td><?= $u->id ?></td>
                            <td>
                                <strong><?= h($u->name) ?></strong>
                                <?php if ($isCurrentUser): ?>
                                    <span style="font-size:0.75rem; color:var(--primary-color);"> (You)</span>
                                <?php endif; ?>
                            </td>
                            <td><?= h($u->email) ?></td>
                            <td>
                                <span class="badge" style="background:<?= $rc ?>; color:white; text-transform:uppercase; font-size:0.72rem;">
                                    <?= h($u->role) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $status === 'active' ? 'status-active' : 'status-suspended' ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($u->created_at)) ?></td>
                            <td>
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                    <!-- Change Role -->
                                    <form method="POST" action="users.php" style="display:flex; gap:4px; align-items:center;">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?= $u->id ?>">
                                        <select name="role" class="form-control" style="padding:4px 8px; font-size:0.82rem; width:auto;">
                                            <option value="student" <?= $u->role == 'student'  ? 'selected' : '' ?>>Student</option>
                                            <option value="seller" <?= $u->role == 'seller'   ? 'selected' : '' ?>>Seller</option>
                                            <option value="admin" <?= $u->role == 'admin'    ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary" style="padding:5px 10px; font-size:0.82rem;">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>

                                    <?php if (!$isCurrentUser): ?>
                                        <!-- Toggle Status -->
                                        <form method="POST" action="users.php">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?= $u->id ?>">
                                            <button type="submit" class="btn <?= $status === 'active' ? 'btn-secondary' : 'btn-success' ?>"
                                                style="padding:5px 10px; font-size:0.82rem;"
                                                title="<?= $status === 'active' ? 'Suspend' : 'Activate' ?>">
                                                <i class="fa-solid <?= $status === 'active' ? 'fa-ban' : 'fa-check-circle' ?>"></i>
                                            </button>
                                        </form>
                                        <!-- Delete -->
                                        <form method="POST" action="users.php" onsubmit="return confirm('Delete <?= h($u->name) ?>? This cannot be undone.');">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?= $u->id ?>">
                                            <button type="submit" class="btn btn-danger" style="padding:5px 10px; font-size:0.82rem;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top:12px; color:var(--gray); font-size:0.88rem;">Total: <strong><?= count($users) ?></strong> user(s) found.</p>
</div>

<?php require_once 'footer.php'; ?>