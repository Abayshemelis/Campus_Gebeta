<?php
require_once 'header.php';

// ── Stats ──────────────────────────────────────────────
$menu_count  = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
$revenue     = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status = 'Served'")->fetchColumn();

// Per-role user counts
$role_counts = [];
foreach (['student', 'seller', 'admin'] as $r) {
    $role_counts[$r] = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role=?");
    $role_counts[$r]->execute([$r]);
    $role_counts[$r] = (int)$role_counts[$r]->fetchColumn();
}
$total_users = array_sum($role_counts);

// Recent orders
$recent_orders = $pdo->query(
    "SELECT o.*, u.name as user_name FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC LIMIT 5"
)->fetchAll();
?>

<div class="container" style="padding-bottom: 50px;">

    <div style="margin-bottom: 25px;">
        <h2 style="margin-bottom: 5px;"><i class="fa-solid fa-gauge" style="color:var(--primary-color);"></i> Admin Dashboard</h2>
        <p style="color:var(--gray);">Welcome back, <strong><?= h($_SESSION['user_name']) ?></strong>. Here's your Campus Gebeta overview.</p>
    </div>

    <!-- ── Top Stats ── -->
    <div class="admin-stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        <div class="admin-stat-card">
            <div class="stat-icon" style="color:var(--primary-color);"><i class="fa-solid fa-burger"></i></div>
            <h3><?= $menu_count ?></h3>
            <p>Menu Items</p>
        </div>
        <div class="admin-stat-card" style="border-top-color:#2ecc71;">
            <div class="stat-icon" style="color:#2ecc71;"><i class="fa-solid fa-list-check"></i></div>
            <h3><?= $order_count ?></h3>
            <p>Active Orders</p>
        </div>
        <div class="admin-stat-card" style="border-top-color:#f4a261;">
            <div class="stat-icon" style="color:#f4a261;"><i class="fa-solid fa-money-bill-wave"></i></div>
            <h3><?= number_format($revenue, 0) ?></h3>
            <p>Revenue (ETB)</p>
        </div>
        <div class="admin-stat-card" style="border-top-color:#457b9d;">
            <div class="stat-icon" style="color:#457b9d;"><i class="fa-solid fa-users"></i></div>
            <h3><?= $total_users ?></h3>
            <p>Total Users</p>
        </div>
    </div>

    <!-- ── User Breakdown ── -->
    <div class="card" style="margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px;"><i class="fa-solid fa-users-gear" style="color:var(--primary-color);"></i> User Breakdown</h3>
        <div style="display:flex; gap:15px; flex-wrap:wrap;">
            <?php
            $role_info = [
                'student'  => ['color' => '#457b9d', 'icon' => 'fa-graduation-cap', 'label' => 'Students'],
                'seller'   => ['color' => '#f4a261', 'icon' => 'fa-tag',            'label' => 'Sellers'],
                'admin'    => ['color' => '#e63946', 'icon' => 'fa-user-shield',     'label' => 'Admins'],
            ];
            foreach ($role_info as $role => $info): ?>
                <div style="flex:1; min-width:130px; background:var(--light-color); border-radius:10px; padding:18px; text-align:center; border-left:4px solid <?= $info['color'] ?>;">
                    <i class="fa-solid <?= $info['icon'] ?>" style="font-size:1.5rem; color:<?= $info['color'] ?>; margin-bottom:8px;"></i>
                    <div style="font-size:1.6rem; font-weight:700; color:<?= $info['color'] ?>;"><?= $role_counts[$role] ?></div>
                    <div style="font-size:0.85rem; color:var(--gray);"><?= $info['label'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:15px; text-align:right;">
            <a href="users.php" class="btn btn-primary" style="padding:8px 18px; font-size:0.9rem;">
                <i class="fa-solid fa-arrow-right"></i> Manage All Users
            </a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr; gap:25px;">

        <!-- ── Recent Orders ── -->
        <div class="card">
            <h3 style="margin-bottom:20px;"><i class="fa-solid fa-list" style="color:var(--primary-color);"></i> Recent Orders</h3>
            <?php if (empty($recent_orders)): ?>
                <p style="color:var(--gray);">No orders yet.</p>
            <?php else: ?>
                <table style="font-size:0.88rem;">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $o): ?>
                            <tr>
                                <td><?= h($o->user_name) ?></td>
                                <td><?= number_format($o->total_amount, 2) ?> ETB</td>
                                <td><span class="badge badge-<?= $o->status ?>"><?= $o->status ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top:15px; text-align:right;">
                    <a href="orders.php" class="btn btn-secondary" style="padding:7px 14px; font-size:0.85rem;">View All Orders</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once 'footer.php'; ?>