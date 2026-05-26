<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
?>

<div class="container">
    <h2>My Orders</h2>
    <p>Track the status of your current and past orders.</p>

    <div class="table-responsive" style="margin-top: 30px;">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$user_id]);
                $orders = $stmt->fetchAll();

                if (count($orders) > 0) {
                    foreach ($orders as $order) {
                        // Fetch order items
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
                            <td><?= date('M d, Y h:i A', strtotime($order->created_at)) ?></td>
                            <td><?= implode('<br>', $itemList) ?></td>
                            <td style="font-weight: bold;"><?= number_format($order->total_amount, 2) ?> ETB</td>
                            <td>
                                <span class="badge badge-<?= h($order->status) ?>"><?= h($order->status) ?></span>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align: center;'>You have no orders yet. <a href='index.php'>Order now!</a></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Auto refresh every 30 seconds to check status updates -->
<script>
    setTimeout(function(){
        window.location.reload(1);
    }, 30000);
</script>

<?php require_once 'includes/footer.php'; ?>
