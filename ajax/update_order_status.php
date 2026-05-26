<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_role = $_SESSION['user_role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_role !== 'admin' && $user_role !== 'seller') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $status = $_POST['status'] ?? '';

    $allowed_statuses = ['Pending', 'Preparing', 'Ready', 'Served', 'Cancelled'];

    if (in_array($status, $allowed_statuses) && $order_id > 0) {
        // If user is seller, verify they own at least one item in this order
        if ($user_role === 'seller') {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM order_items oi 
                JOIN menu_items mi ON oi.menu_item_id = mi.id 
                WHERE oi.order_id = ? AND mi.seller_id = ?
            ");
            $stmt->execute([$order_id, $user_id]);
            if ($stmt->fetchColumn() == 0) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: This order contains none of your items']);
                exit;
            }
        }

        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $order_id])) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data: status ' . $status . ', order_id ' . $order_id]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
