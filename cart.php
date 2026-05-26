<?php
require_once 'includes/header.php';
requireLogin();

$success = '';
$error = '';

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $error = "Your cart is empty.";
    } else {
        $total_amount = getCartTotal();
        $user_id = $_SESSION['user_id'];

        try {
            $pdo->beginTransaction();

            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Pending')");
            $stmt->execute([$user_id, $total_amount]);
            $order_id = $pdo->lastInsertId();

            // Create order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION['cart'] as $item) {
                $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            }

            $pdo->commit();

            // Clear cart
            $_SESSION['cart'] = [];
            $success = "Order placed successfully! You can track it in 'My Orders'.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to place order: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <h2>Your Cart</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= h($success) ?>
            <br><br>
            <a href="orders.php" class="btn btn-primary">View My Orders</a>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart']) && !$success): ?>
        <div style="text-align: center; padding: 50px 0;">
            <i class="fa-solid fa-cart-arrow-down" style="font-size: 50px; color: var(--gray); margin-bottom: 20px;"></i>
            <h3>Your cart is empty</h3>
            <p style="color: var(--gray); margin-bottom: 20px;">Looks like you haven't added anything to your cart yet.</p>
            <a href="index.php" class="btn btn-primary">Browse Menu</a>
        </div>
    <?php elseif (!empty($_SESSION['cart'])): ?>
        <div class="table-responsive" style="margin-bottom: 30px;">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartTableBody">
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <tr id="cart-row-<?= $index ?>">
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <?php if ($item['image_url']): ?>
                                        <img src="<?= h($item['image_url']) ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #eee; border-radius: 5px; margin-right: 15px;"></div>
                                    <?php endif; ?>
                                    <strong><?= h($item['name']) ?></strong>
                                </div>
                            </td>
                            <td><?= number_format($item['price'], 2) ?> ETB</td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <button class="btn btn-secondary cart-update" data-index="<?= $index ?>" data-action="decrease" style="padding: 5px 10px;">-</button>
                                    <span style="margin: 0 15px; font-weight: bold;" id="qty-<?= $index ?>"><?= $item['quantity'] ?></span>
                                    <button class="btn btn-secondary cart-update" data-index="<?= $index ?>" data-action="increase" style="padding: 5px 10px;">+</button>
                                </div>
                            </td>
                            <td id="subtotal-<?= $index ?>" style="font-weight: 500;"><?= number_format($item['price'] * $item['quantity'], 2) ?> ETB</td>
                            <td>
                                <button class="btn btn-danger cart-update" data-index="<?= $index ?>" data-action="remove" style="padding: 5px 10px;"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <div style="background: var(--white); padding: 30px; border-radius: 10px; box-shadow: var(--shadow); width: 100%; max-width: 400px;">
                <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 15px;">Order Summary</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 20px; font-weight: bold;">
                    <span>Total:</span>
                    <span id="cartTotalDisplay" style="color: var(--primary-color);"><?= number_format(getCartTotal(), 2) ?> ETB</span>
                </div>
                <form action="cart.php" method="POST">
                    <button type="submit" name="checkout" class="btn btn-primary" style="width: 100%; font-size: 18px; padding: 15px;">Place Order</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- AJAX handling script specifically for cart page -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateButtons = document.querySelectorAll('.cart-update');
        updateButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const index = this.dataset.index;
                const action = this.dataset.action;

                // In a production app, we would do a fetch/AJAX request here.
                // Since it's complex to re-render the whole table via vanilla JS cleanly without a framework
                // simply reloading the page or using a simpler form submission approach is often safer for beginners.
                // Let's implement a simple fetch request and then reload the page to ensure UI is in sync.

                fetch('ajax/update_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `index=${index}&action=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload(); // Quickest way to ensure accurate DOM sync for basic PHP apps
                        }
                    });
            });
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>