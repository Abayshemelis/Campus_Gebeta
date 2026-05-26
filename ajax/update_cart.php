<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $index = $_POST['index'] ?? -1;

    if (isset($_SESSION['cart'][$index])) {
        if ($action == 'increase') {
            $_SESSION['cart'][$index]['quantity']++;
        } elseif ($action == 'decrease') {
            if ($_SESSION['cart'][$index]['quantity'] > 1) {
                $_SESSION['cart'][$index]['quantity']--;
            } else {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // re-index
            }
        } elseif ($action == 'remove') {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }

        echo json_encode([
            'success' => true,
            'cartCount' => getCartItemCount(),
            'cartTotal' => number_format(getCartTotal(), 2)
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
