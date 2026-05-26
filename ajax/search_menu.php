<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$q = $_GET['q'] ?? '';

if (empty($q)) {
    $stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY id DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE is_available = 1 AND (name LIKE ? OR category LIKE ?) ORDER BY id DESC");
    $searchTerm = "%$q%";
    $stmt->execute([$searchTerm, $searchTerm]);
}

$items = $stmt->fetchAll();

if (count($items) > 0) {
    foreach ($items as $item) {
?>
        <div class="menu-card">
            <?php if ($item->image_url): ?>
                <img src="<?= h($item->image_url) ?>" alt="<?= h($item->name) ?>" class="menu-img">
            <?php else: ?>
                <div class="menu-img" style="background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa;">No Image</div>
            <?php endif; ?>

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
<?php
    }
} else {
    echo "<p style='grid-column: 1 / -1; text-align: center; color: var(--gray);'>No items found matching your search.</p>";
}
?>