<?php
// delete_notice.php — allows admin or the original poster to delete a notice
require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    // Fetch the notice to check ownership
    $stmt = $pdo->prepare("SELECT user_id FROM notices WHERE id = ?");
    $stmt->execute([$id]);
    $notice = $stmt->fetch();

    if ($notice) {
        // Admin can delete any; owners can delete their own
        if (isAdmin() || (int)$notice->user_id === (int)$_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM notices WHERE id = ?")->execute([$id]);
        }
    }
}

header('Location: noticeboard.php');
exit();
