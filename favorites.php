<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch user's favorite items
$stmt = $pdo->prepare("
    SELECT m.* 
    FROM menu_items m
    JOIN user_favorites f ON m.id = f.menu_item_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i class="fa-solid fa-heart" style="color: var(--primary-color);"></i> My Favorites</h2>
    </div>

    <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 50px 0; color: var(--gray);">
            <i class="fa-regular fa-heart" style="font-size: 48px; margin-bottom: 20px;"></i>
            <h3>No favorites yet</h3>
            <p>Go to the menu and click the heart icon on your favorite meals to save them here!</p>
            <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Browse Menu</a>
        </div>
    <?php else: ?>
        <div class="menu-grid" id="menuGrid">
            <?php foreach ($items as $item): ?>
                <div class="menu-card reveal active">
                    <div style="position: relative;">
                        <?php if ($item->image_url): ?>
                            <img src="<?= h($item->image_url) ?>" alt="<?= h($item->name) ?>" class="menu-img">
                        <?php else: ?>
                            <div class="menu-img" style="background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa;">No Image</div>
                        <?php endif; ?>
                        <button class="toggle-favorite" data-id="<?= $item->id ?>" style="position: absolute; top: 10px; right: 10px; background: white; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: var(--transition);">
                            <i class="fa-solid fa-heart" style="color: var(--primary-color); font-size: 18px;"></i>
                        </button>
                    </div>
                    
                    <div class="menu-content">
                        <div class="menu-title">
                            <?= h($item->name) ?>
                            <span class="menu-price"><?= number_format($item->price, 2) ?> ETB</span>
                        </div>
                        <p class="menu-desc"><?= h($item->description) ?></p>
                        <div class="menu-footer">
                            <span class="badge" style="background: var(--secondary-color); color: white;"><?= h($item->category) ?></span>
                            <?php if (!isAdmin()): ?>
                                <button class="btn btn-primary add-to-cart" data-id="<?= $item->id ?>"><i class="fa-solid fa-cart-plus"></i> Add</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
