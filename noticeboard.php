<?php
require_once 'includes/header.php';

$type = isset($_GET['type']) ? trim($_GET['type']) : '';

$query = "SELECT n.*, u.name as author_name, u.role as author_role FROM notices n JOIN users u ON n.user_id = u.id";
$params = [];
if ($type) {
    $query .= " WHERE n.type = ?";
    $params[] = $type;
}
$query .= " ORDER BY n.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$notices = $stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <h2><i class="fa-solid fa-clipboard-list" style="color: var(--primary-color);"></i> Campus Noticeboard</h2>
        <?php if (isLoggedIn() && canPostNotice()): ?>
            <a href="notice_post.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Post Notice</a>
        <?php elseif (!isLoggedIn()): ?>
            <a href="login.php" class="btn btn-primary">Login to Post</a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div style="margin-bottom: 30px; display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
        <a href="noticeboard.php" class="btn <?= empty($type) ? 'btn-primary' : 'btn-secondary' ?>">All Notices</a>
        <a href="noticeboard.php?type=announcement" class="btn <?= $type == 'announcement' ? 'btn-primary' : 'btn-secondary' ?>">Announcements</a>
        <a href="noticeboard.php?type=event" class="btn <?= $type == 'event' ? 'btn-primary' : 'btn-secondary' ?>">Events</a>
        <a href="noticeboard.php?type=lost_found" class="btn <?= $type == 'lost_found' ? 'btn-primary' : 'btn-secondary' ?>">Lost &amp; Found</a>
    </div>

    <?php if (empty($notices)): ?>
        <div style="text-align: center; padding: 50px 0; color: var(--gray);">
            <i class="fa-regular fa-folder-open" style="font-size: 48px; margin-bottom: 20px;"></i>
            <h3>No notices found</h3>
            <p>Check back later or post a new notice!</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($notices as $notice): ?>
                <?php
                $icon = 'fa-bullhorn';
                $color = 'var(--primary-color)';
                if ($notice->type == 'event') {
                    $icon = 'fa-calendar-day'; $color = '#fca311';
                } elseif ($notice->type == 'lost_found') {
                    $icon = 'fa-magnifying-glass'; $color = '#4ea8de';
                }
                // Role badge for poster
                $roleColors = ['admin'=>'#e63946','lecturer'=>'#2a9d8f','seller'=>'#f4a261','student'=>'#457b9d'];
                $authorColor = $roleColors[$notice->author_role] ?? '#888';
                ?>
                <div class="card" style="display: flex; gap: 20px; align-items: flex-start;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: <?= $color ?>20; color: <?= $color ?>; display: flex; justify-content: center; align-items: center; font-size: 24px; flex-shrink: 0;">
                        <i class="fa-solid <?= $icon ?>"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <h3 style="margin: 0;"><?= h($notice->title) ?></h3>
                            <span style="font-size: 0.85rem; color: var(--gray); white-space: nowrap;"><i class="fa-regular fa-clock"></i> <?= date('M d, g:i A', strtotime($notice->created_at)) ?></span>
                        </div>
                        <span class="badge" style="background: <?= $color ?>; color: white; margin-bottom: 15px; display: inline-block; text-transform: capitalize;">
                            <?= str_replace('_', ' & ', h($notice->type)) ?>
                        </span>
                        <p style="margin-bottom: 15px; line-height: 1.6; white-space: pre-wrap;"><?= h($notice->content) ?></p>
                        <div style="font-size: 0.9rem; color: var(--gray); border-top: 1px solid #eee; padding-top: 15px; display: flex; align-items: center; gap: 8px;">
                            Posted by: <strong><?= h($notice->author_name) ?></strong>
                            <span class="badge" style="background: <?= $authorColor ?>; color: white; font-size: 0.72rem; padding: 2px 8px; text-transform: uppercase;">
                                <?= h(ucfirst($notice->author_role)) ?>
                            </span>
                            <?php if (isAdmin() || (isLoggedIn() && $_SESSION['user_id'] == $notice->user_id)): ?>
                                <a href="delete_notice.php?id=<?= $notice->id ?>"
                                   onclick="return confirm('Delete this notice?')"
                                   style="margin-left: auto; color: var(--danger); font-size: 0.85rem;">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
