<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$menu_item_id = isset($_GET['menu_item_id']) ? (int)$_GET['menu_item_id'] : 0;

if ($menu_item_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid menu item ID.']);
    exit;
}

// Fetch average rating & reviews count
$stats_stmt = $pdo->prepare("SELECT COUNT(*) as count, AVG(rating) as avg_rating FROM menu_item_ratings WHERE menu_item_id = ?");
$stats_stmt->execute([$menu_item_id]);
$stats = $stats_stmt->fetch();

$avg_rating = $stats->avg_rating ? number_format((float)$stats->avg_rating, 1) : '0.0';
$reviews_count = (int)$stats->count;

// Fetch all reviews for this item
$reviews_stmt = $pdo->prepare("
    SELECT r.*, u.name as user_name 
    FROM menu_item_ratings r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.menu_item_id = ? 
    ORDER BY r.created_at DESC
");
$reviews_stmt->execute([$menu_item_id]);
$reviews_list = $reviews_stmt->fetchAll();

// Check if current user has reviewed this item
$my_review = null;
if (isLoggedIn()) {
    $my_stmt = $pdo->prepare("SELECT rating, comment FROM menu_item_ratings WHERE menu_item_id = ? AND user_id = ?");
    $my_stmt->execute([$menu_item_id, $_SESSION['user_id']]);
    $my_review = $my_stmt->fetch();
}

$reviews = [];
foreach ($reviews_list as $r) {
    $reviews[] = [
        'id' => $r->id,
        'user_name' => $r->user_name,
        'rating' => (int)$r->rating,
        'comment' => h($r->comment),
        'created_at' => date('M d, Y h:i A', strtotime($r->created_at))
    ];
}

echo json_encode([
    'success' => true,
    'avg_rating' => $avg_rating,
    'reviews_count' => $reviews_count,
    'reviews' => $reviews,
    'my_review' => $my_review ? ['rating' => (int)$my_review->rating, 'comment' => h($my_review->comment)] : null
]);
