<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$q = $_GET['q'] ?? '';

$baseQuery = "
    SELECT mi.*, 
           COALESCE(r.avg_rating, 0.0) as avg_rating, 
           COALESCE(r.reviews_count, 0) as reviews_count,
           u.name as seller_name
    FROM menu_items mi
    LEFT JOIN (
        SELECT menu_item_id, AVG(rating) as avg_rating, COUNT(*) as reviews_count 
        FROM menu_item_ratings 
        GROUP BY menu_item_id
    ) r ON mi.id = r.menu_item_id
    LEFT JOIN users u ON mi.seller_id = u.id
    WHERE mi.is_available = 1
";

if (empty($q)) {
    $stmt = $pdo->query($baseQuery . " ORDER BY mi.id DESC");
} else {
    $stmt = $pdo->prepare($baseQuery . " AND (mi.name LIKE ? OR mi.category LIKE ?) ORDER BY mi.id DESC");
    $searchTerm = "%$q%";
    $stmt->execute([$searchTerm, $searchTerm]);
}

$items = $stmt->fetchAll();

if (count($items) > 0) {
    foreach ($items as $item) {
?>
        <div class="menu-card">
            <div style="position: relative;">
                <?php if ($item->image_url): ?>
                    <img src="<?= h($item->image_url) ?>" alt="<?= h($item->name) ?>" class="menu-img">
                <?php else: ?>
                    <div class="menu-img" style="background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa;">No Image</div>
                <?php endif; ?>
            </div>

            <div class="menu-content">
                <div class="menu-title">
                    <?= h($item->name) ?>
                    <span class="menu-price"><?= number_format($item->price, 2) ?> ETB</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 0.82rem;">
                    <!-- Star rating badge -->
                    <div class="card-rating-badge" data-id="<?= $item->id ?>" data-name="<?= h($item->name) ?>">
                        <i class="fa-solid fa-star"></i> 
                        <span class="rating-val-<?= $item->id ?>"><?= number_format($item->avg_rating, 1) ?></span> 
                        <span class="rating-count-<?= $item->id ?>" style="color: var(--gray); font-weight: normal;">(<?= $item->reviews_count ?>)</span>
                    </div>
                    <!-- Seller badge -->
                    <?php if ($item->seller_name): ?>
                        <span style="color: var(--gray);"><i class="fa-solid fa-shop" style="color: var(--primary-color);"></i> <?= h($item->seller_name) ?></span>
                    <?php endif; ?>
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