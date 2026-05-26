<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first', 'redirect' => 'login.php']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];
    $user_id = $_SESSION['user_id'];

    // Check if item exists
    $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE id = ?");
    $stmt->execute([$item_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit;
    }

    // Check if already favorited
    $stmt = $pdo->prepare("SELECT id FROM user_favorites WHERE user_id = ? AND menu_item_id = ?");
    $stmt->execute([$user_id, $item_id]);

    if ($stmt->fetch()) {
        // Remove favorite
        $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = ? AND menu_item_id = ?");
        $stmt->execute([$user_id, $item_id]);
        echo json_encode(['success' => true, 'action' => 'removed']);
    } else {
        // Add favorite
        $stmt = $pdo->prepare("INSERT INTO user_favorites (user_id, menu_item_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $item_id]);
        echo json_encode(['success' => true, 'action' => 'added']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
