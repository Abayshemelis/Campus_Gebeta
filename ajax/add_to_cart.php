<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to your cart.', 'redirect' => 'login.php']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    if ($item_id > 0) {
        // Fetch item details
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ? AND is_available = 1");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();

        if ($item) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Check if item already in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$cartItem) {
                if ($cartItem['id'] == $item_id) {
                    $cartItem['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'image_url' => $item->image_url,
                    'quantity' => $quantity
                ];
            }

            echo json_encode([
                'success' => true,
                'cartCount' => getCartItemCount()
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not available.']);
            exit;
        }
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
