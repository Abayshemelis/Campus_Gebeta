<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to submit a rating.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_item_id = (int)($_POST['menu_item_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($menu_item_id <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid rating or menu item.']);
        exit;
    }

    // Verify item exists
    $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE id = ?");
    $stmt->execute([$menu_item_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Menu item not found.']);
        exit;
    }

    try {
        // Use UPSERT (INSERT ... ON DUPLICATE KEY UPDATE)
        $stmt = $pdo->prepare("
            INSERT INTO menu_item_ratings (menu_item_id, user_id, rating, comment) 
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), created_at = CURRENT_TIMESTAMP
        ");
        if ($stmt->execute([$menu_item_id, $user_id, $rating, $comment])) {
            // Get new average rating and count
            $avg_stmt = $pdo->prepare("SELECT COUNT(*) as count, AVG(rating) as avg_rating FROM menu_item_ratings WHERE menu_item_id = ?");
            $avg_stmt->execute([$menu_item_id]);
            $stats = $avg_stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'message' => 'Thank you for your rating!',
                'avg_rating' => number_format((float)$stats->avg_rating, 1),
                'reviews_count' => (int)$stats->count
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save rating.']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
