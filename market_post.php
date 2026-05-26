<?php
require_once 'includes/header.php';
requireLogin();

// Only sellers and admins can post market items
if (!canPostMarket()) {
    ?>
    <div class="container" style="padding: 60px 0; text-align: center;">
        <i class="fa-solid fa-store-slash" style="font-size: 60px; color: var(--gray); margin-bottom: 20px;"></i>
        <h2>Seller Access Only</h2>
        <p style="color: var(--gray); margin-bottom: 25px;">Only registered <strong>Sellers</strong> and <strong>Admins</strong> can post items on Gebeta Market.</p>
        <p style="color: var(--gray); margin-bottom: 25px;">If you are a student entrepreneur, please <a href="login.php" style="color:var(--primary-color);">register as a Seller</a> or contact the admin to upgrade your account.</p>
        <a href="market.php" class="btn btn-primary">Browse Market</a>
    </div>
    <?php
    require_once 'includes/footer.php';
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($title) || empty($price) || empty($category)) {
        $error = "Title, price, and category are required!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO marketplace_items (user_id, title, description, price, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $title, $description, $price, $category, $image_url])) {
            $success = "Item posted successfully!";
            // Clear form
            $title = $description = $price = $category = $image_url = '';
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<div class="container" style="padding: 40px 0; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i class="fa-solid fa-plus" style="color: var(--primary-color);"></i> Post to Gebeta Market</h2>
        <a href="market.php" class="btn btn-secondary">Back to Market</a>
    </div>

    <div class="card">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="market_post.php">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="title" style="display: block; margin-bottom: 8px; font-weight: 500;">Item Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= h($title ?? '') ?>" required placeholder="e.g. Calculus Textbook">
            </div>

            <div class="form-group" style="margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label for="price" style="display: block; margin-bottom: 8px; font-weight: 500;">Price (ETB) *</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?= h($price ?? '') ?>" required placeholder="e.g. 500">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label for="category" style="display: block; margin-bottom: 8px; font-weight: 500;">Category *</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">Select a category</option>
                        <option value="Homemade Meals" <?= ($category ?? '') == 'Homemade Meals' ? 'selected' : '' ?>>Homemade Meals</option>
                        <option value="Packed Snacks" <?= ($category ?? '') == 'Packed Snacks' ? 'selected' : '' ?>>Packed Snacks</option>
                        <option value="Drinks" <?= ($category ?? '') == 'Drinks' ? 'selected' : '' ?>>Drinks</option>
                        <option value="Groceries" <?= ($category ?? '') == 'Groceries' ? 'selected' : '' ?>>Groceries</option>
                        <option value="Other" <?= ($category ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="image_url" style="display: block; margin-bottom: 8px; font-weight: 500;">Image URL</label>
                <input type="url" id="image_url" name="image_url" class="form-control" value="<?= h($image_url ?? '') ?>" placeholder="https://example.com/image.jpg">
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: 500;">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5" placeholder="Describe the item condition, features, etc."><?= h($description ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-paper-plane"></i> Post Item</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
