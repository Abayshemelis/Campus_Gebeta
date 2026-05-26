<?php
require_once 'includes/header.php';
requireLogin();

// Sellers cannot post notices; only students, lecturers, and admins can
if (isSeller()) {
    header('Location: noticeboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type    = trim($_POST['type'] ?? '');
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Students can only post lost_found
    if (isStudent() && $type !== 'lost_found') {
        $error = "Students can only post Lost &amp; Found notices.";
    } elseif (empty($title) || empty($content) || empty($type)) {
        $error = "All fields are required!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO notices (user_id, type, title, content) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $type, $title, $content])) {
            $success = "Notice posted successfully!";
            $title = $content = $type = '';
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<div class="container" style="padding: 40px 0; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i class="fa-solid fa-plus" style="color: var(--primary-color);"></i> Post a Notice</h2>
        <a href="noticeboard.php" class="btn btn-secondary">Back to Noticeboard</a>
    </div>

    <?php if (isStudent()): ?>
    <div style="margin-bottom: 20px; padding: 12px 16px; background: #e8f4fd; border-left: 4px solid #457b9d; border-radius: 6px; font-size: 0.9rem; color: #1a5276;">
        <i class="fa-solid fa-circle-info"></i> <strong>Students</strong> can only post <strong>Lost &amp; Found</strong> notices. Contact a lecturer or admin to post announcements or events.
    </div>
    <?php endif; ?>

    <div class="card">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="notice_post.php">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="type" style="display: block; margin-bottom: 8px; font-weight: 500;">Notice Type *</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="">Select a type</option>
                    <?php if (canPostAnnouncement()): ?>
                        <option value="announcement" <?= ($type ?? '') == 'announcement' ? 'selected' : '' ?>>📢 Announcement</option>
                        <option value="event"        <?= ($type ?? '') == 'event'        ? 'selected' : '' ?>>📅 Event</option>
                    <?php endif; ?>
                    <option value="lost_found" <?= ($type ?? '') == 'lost_found' ? 'selected' : '' ?>>🔍 Lost &amp; Found</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="title" style="display: block; margin-bottom: 8px; font-weight: 500;">Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= h($title ?? '') ?>" required placeholder="e.g. Lost Blue Notebook, Tech Seminar">
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="content" style="display: block; margin-bottom: 8px; font-weight: 500;">Content *</label>
                <textarea id="content" name="content" class="form-control" rows="6" required placeholder="Provide all necessary details here..."><?= h($content ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-paper-plane"></i> Post Notice</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
