<?php
require_once 'includes/header.php';
requireLogin();

if (!isSeller() && !isAdmin()) {
    ?>
    <div class="container" style="padding: 60px 0; text-align: center;">
        <i class="fa-solid fa-lock" style="font-size: 60px; color: var(--danger); margin-bottom: 20px;"></i>
        <h2>Access Denied</h2>
        <p style="color: var(--gray); margin-bottom: 25px;">This dashboard is restricted to Sellers only.</p>
        <a href="index.php" class="btn btn-primary">Go Home</a>
    </div>
    <?php
    require_once 'includes/footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = $_SESSION['success_flash'] ?? '';
unset($_SESSION['success_flash']);
$error_msg = '';

// Handle Availability Toggle
if (isset($_GET['toggle_avail_id'])) {
    $item_id = (int)$_GET['toggle_avail_id'];
    
    // Check ownership
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT is_available FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
    } else {
        $stmt = $pdo->prepare("SELECT is_available FROM menu_items WHERE id = ? AND seller_id = ?");
        $stmt->execute([$item_id, $user_id]);
    }
    
    $curr = $stmt->fetch();
    if ($curr) {
        $new_avail = $curr->is_available ? 0 : 1;
        $up = $pdo->prepare("UPDATE menu_items SET is_available = ? WHERE id = ?");
        if ($up->execute([$new_avail, $item_id])) {
            $success_msg = "Availability updated successfully.";
        } else {
            $error_msg = "Failed to update availability.";
        }
    }
}

// Handle Menu Item Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    // Ensure the item belongs to the logged-in user (or user is admin)
    if (isAdmin()) {
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        $execute_params = [$delete_id];
    } else {
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ? AND seller_id = ?");
        $execute_params = [$delete_id, $user_id];
    }
    
    if ($stmt->execute($execute_params)) {
        $success_msg = "Item deleted from menu successfully.";
    } else {
        $error_msg = "Failed to delete item.";
    }
}

// Fetch Seller's Menu Items
if (isAdmin()) {
    $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
}
$menu_items = $stmt->fetchAll();
$total_items = count($menu_items);

// Calculate Stats
// 1. Pending/Preparing/Ready Orders
if (isAdmin()) {
    $active_orders_stmt = $pdo->query("SELECT COUNT(DISTINCT id) FROM orders WHERE status IN ('Pending', 'Preparing', 'Ready')");
} else {
    $active_orders_stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT o.id) 
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        JOIN menu_items mi ON oi.menu_item_id = mi.id 
        WHERE mi.seller_id = ? AND o.status IN ('Pending', 'Preparing', 'Ready')
    ");
    $active_orders_stmt->execute([$user_id]);
}
$active_orders_count = $active_orders_stmt->fetchColumn();

// 2. Total Revenue from seller's items
if (isAdmin()) {
    $revenue_stmt = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status = 'Served'");
} else {
    $revenue_stmt = $pdo->prepare("
        SELECT COALESCE(SUM(oi.price * oi.quantity), 0) 
        FROM order_items oi 
        JOIN menu_items mi ON oi.menu_item_id = mi.id 
        JOIN orders o ON oi.order_id = o.id 
        WHERE mi.seller_id = ? AND o.status = 'Served'
    ");
    $revenue_stmt->execute([$user_id]);
}
$total_revenue = $revenue_stmt->fetchColumn();

// Fetch Incoming Orders containing seller's items
if (isAdmin()) {
    $orders_stmt = $pdo->query("
        SELECT DISTINCT o.*, u.name as customer_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC LIMIT 20
    ");
} else {
    $orders_stmt = $pdo->prepare("
        SELECT DISTINCT o.*, u.name as customer_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE mi.seller_id = ?
        ORDER BY o.created_at DESC LIMIT 20
    ");
    $orders_stmt->execute([$user_id]);
}
$orders = $orders_stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 30px; gap: 15px;">
        <div>
            <h2><i class="fa-solid fa-store" style="color: var(--primary-color);"></i> <?= isAdmin() ? 'Admin Portal Dashboard' : 'My Cafeteria Shop Dashboard' ?></h2>
            <p style="color: var(--gray);"><?= isAdmin() ? 'All campus food sales and listings' : 'Manage your menu items, track orders, and view sales.' ?></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-eye"></i> View Main Site</a>
            <a href="seller_menu_item.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Post Food/Drink</a>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= h($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= h($error_msg) ?></div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="card" style="padding: 25px; text-align: center; border-radius: 12px; border-top: 4px solid var(--primary-color);">
            <i class="fa-solid fa-burger" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h3 style="font-size: 2rem; margin: 0;"><?= $total_items ?></h3>
            <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">My Menu Items</p>
        </div>
        <div class="card" style="padding: 25px; text-align: center; border-radius: 12px; border-top: 4px solid var(--secondary-color);">
            <i class="fa-solid fa-circle-play" style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 15px;"></i>
            <h3 style="font-size: 2rem; margin: 0;"><?= $active_orders_count ?></h3>
            <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Incoming Active Orders</p>
        </div>
        <div class="card" style="padding: 25px; text-align: center; border-radius: 12px; border-top: 4px solid var(--warning);">
            <i class="fa-solid fa-money-bill-wave" style="font-size: 2rem; color: var(--warning); margin-bottom: 15px;"></i>
            <h3 style="font-size: 2rem; margin: 0;"><?= number_format($total_revenue, 2) ?> ETB</h3>
            <p style="color: var(--gray); margin: 0; font-size: 0.9rem;">Total Revenue (Served)</p>
        </div>
    </div>

    <!-- Incoming Orders Card -->
    <div class="card" style="padding: 25px; border-radius: 12px; margin-bottom: 40px;">
        <h3 style="margin-bottom: 20px;"><i class="fa-solid fa-utensils" style="color: var(--primary-color); margin-right: 8px;"></i> Incoming Orders</h3>
        <?php if (!empty($orders)): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items Ordered</th>
                            <th>Order Amount</th>
                            <th>Date &amp; Time</th>
                            <th>Order Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): 
                            // Fetch items in this order belonging to this seller
                            if (isAdmin()) {
                                $item_stmt = $pdo->prepare("
                                    SELECT oi.*, mi.name as item_name 
                                    FROM order_items oi 
                                    JOIN menu_items mi ON oi.menu_item_id = mi.id 
                                    WHERE oi.order_id = ?
                                ");
                                $item_stmt->execute([$order->id]);
                            } else {
                                $item_stmt = $pdo->prepare("
                                    SELECT oi.*, mi.name as item_name 
                                    FROM order_items oi 
                                    JOIN menu_items mi ON oi.menu_item_id = mi.id 
                                    WHERE oi.order_id = ? AND mi.seller_id = ?
                                ");
                                $item_stmt->execute([$order->id, $user_id]);
                            }
                            $items = $item_stmt->fetchAll();
                            
                            $item_details = [];
                            $order_subtotal = 0;
                            foreach ($items as $item) {
                                $item_details[] = $item->quantity . 'x ' . h($item->item_name);
                                $order_subtotal += $item->price * $item->quantity;
                            }
                        ?>
                            <tr>
                                <td><strong>#<?= str_pad($order->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><?= h($order->customer_name) ?></td>
                                <td><?= implode('<br>', $item_details) ?></td>
                                <td style="font-weight: 700; color: var(--primary-color);">
                                    <?= number_format(isAdmin() ? $order->total_amount : $order_subtotal, 2) ?> ETB
                                    <?php if (!isAdmin() && $order_subtotal < $order->total_amount): ?>
                                        <br><span style="font-size:0.75rem; color:var(--gray); font-weight:normal;">(Split Order Total: <?= number_format($order->total_amount, 2) ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, h:i A', strtotime($order->created_at)) ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <select class="form-control status-select" data-id="<?= $order->id ?>" style="width: auto; padding: 5px 8px; font-size: 0.85rem;">
                                            <option value="Pending" <?= $order->status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Preparing" <?= $order->status == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                                            <option value="Ready" <?= $order->status == 'Ready' ? 'selected' : '' ?>>Ready</option>
                                            <option value="Served" <?= $order->status == 'Served' ? 'selected' : '' ?>>Served</option>
                                            <option value="Cancelled" <?= $order->status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <span id="status-indicator-<?= $order->id ?>" style="color: var(--success); display: none;" title="Saved!"><i class="fa-solid fa-check-double"></i></span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: var(--gray); padding: 20px 0;">No active incoming orders found.</p>
        <?php endif; ?>
    </div>

    <!-- Items Listings Card -->
    <div class="card" style="padding: 25px; border-radius: 12px;">
        <h3 style="margin-bottom: 20px;"><i class="fa-solid fa-cubes" style="color: var(--primary-color); margin-right: 8px;"></i> My Menu Items</h3>
        <?php if ($total_items > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Posted On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menu_items as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item->image_url): ?>
                                        <img src="<?= h($item->image_url) ?>" alt="<?= h($item->name) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 10px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600;"><?= h($item->name) ?></td>
                                <td><span class="badge" style="background: rgba(255, 140, 0, 0.1); color: var(--primary-color);"><?= h($item->category) ?></span></td>
                                <td style="color: var(--primary-color); font-weight: 700;"><?= number_format($item->price, 2) ?> ETB</td>
                                <td>
                                    <a href="seller_dashboard.php?toggle_avail_id=<?= $item->id ?>" class="badge <?= $item->is_available ? 'status-active' : 'status-suspended' ?>" style="cursor: pointer; text-decoration: none;" title="Click to toggle availability">
                                        <?= $item->is_available ? 'Available' : 'Unavailable' ?>
                                    </a>
                                </td>
                                <td><?= date('M d, Y', strtotime($item->created_at)) ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="seller_menu_item.php?id=<?= $item->id ?>" class="btn" style="background: rgba(0, 0, 0, 0.05); color: var(--dark-color); padding: 5px 10px; font-size: 0.85rem;">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                        <a href="seller_dashboard.php?delete_id=<?= $item->id ?>" class="btn" style="background: #ffebee; color: var(--danger); padding: 5px 10px; font-size: 0.85rem;" onclick="return confirm('Are you sure you want to delete this menu item? This cannot be undone.');">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 0;">
                <i class="fa-solid fa-burger" style="font-size: 50px; color: var(--border-color); margin-bottom: 15px;"></i>
                <p style="color: var(--gray);">You haven't listed any foods or drinks yet.</p>
                <a href="seller_menu_item.php" class="btn btn-primary" style="margin-top: 15px;">Add Your First Dish</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            let orderId = this.getAttribute('data-id');
            let newStatus = this.value;
            let indicator = document.getElementById('status-indicator-' + orderId);

            let formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('status', newStatus);

            fetch('ajax/update_order_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    indicator.style.display = 'inline-block';
                    setTimeout(function() {
                        indicator.style.display = 'none';
                    }, 2000);
                } else {
                    alert("Failed to update status: " + (data.message || "Unknown error"));
                    location.reload();
                }
            })
            .catch(error => {
                alert("An error occurred while connecting to the server.");
                location.reload();
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
