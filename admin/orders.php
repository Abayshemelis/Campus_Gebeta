<?php
require_once 'header.php';

$filter = $_GET['filter'] ?? 'all';

$query = "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ";
if ($filter == 'active') {
    $query .= "WHERE o.status IN ('Pending', 'Preparing', 'Ready') ";
}
$query .= "ORDER BY o.created_at DESC";

$stmt = $pdo->query($query);
$orders = $stmt->fetchAll();
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Orders</h2>
        <div>
            <a href="orders.php?filter=all" class="btn <?= $filter == 'all' ? 'btn-primary' : 'btn-secondary' ?>">All Orders</a>
            <a href="orders.php?filter=active" class="btn <?= $filter == 'active' ? 'btn-primary' : 'btn-secondary' ?>">Active Orders</a>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order):
                        // Fetch items
                        $itemStmt = $pdo->prepare("SELECT oi.quantity, m.name FROM order_items oi JOIN menu_items m ON oi.menu_item_id = m.id WHERE oi.order_id = ?");
                        $itemStmt->execute([$order->id]);
                        $items = $itemStmt->fetchAll();

                        $itemList = [];
                        foreach ($items as $item) {
                            $itemList[] = $item->quantity . 'x ' . h($item->name);
                        }
                    ?>
                        <tr>
                            <td><strong>#<?= str_pad($order->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                            <td><?= h($order->user_name) ?></td>
                            <td><?= implode('<br>', $itemList) ?></td>
                            <td style="font-weight: bold;"><?= number_format($order->total_amount, 2) ?> ETB</td>
                            <td><?= date('h:i A', strtotime($order->created_at)) ?></td>
                            <td>
                                <select class="form-control status-select" data-id="<?= $order->id ?>" style="width: auto; display: inline-block; padding: 5px;">
                                    <option value="Pending" <?= $order->status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Preparing" <?= $order->status == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                                    <option value="Ready" <?= $order->status == 'Ready' ? 'selected' : '' ?>>Ready</option>
                                    <option value="Served" <?= $order->status == 'Served' ? 'selected' : '' ?>>Served</option>
                                    <option value="Cancelled" <?= $order->status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <span id="status-indicator-<?= $order->id ?>" style="margin-left: 10px; color: var(--success); display: none;"><i class="fa-solid fa-check"></i></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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

                fetch('../ajax/update_order_status.php', {
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

<?php require_once 'footer.php'; ?>