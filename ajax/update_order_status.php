<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $status = $_POST['status'] ?? '';

    $allowed_statuses = ['Pending', 'Preparing', 'Ready', 'Served', 'Cancelled'];

    if (in_array($status, $allowed_statuses) && $order_id > 0) {
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
