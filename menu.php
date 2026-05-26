<?php
require_once 'includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$query = "
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
$params = [];

if ($search) {
    $query .= " AND (mi.name LIKE ? OR mi.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category) {
    $query .= " AND mi.category = ?";
    $params[] = $category;
}

$query .= " ORDER BY mi.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

$user_favorites = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT menu_item_id FROM user_favorites WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch distinct categories for the filter
$stmt = $pdo->query("SELECT DISTINCT category FROM menu_items WHERE is_available = 1");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <h2><i class="fa-solid fa-utensils" style="color: var(--primary-color);"></i> Full Menu</h2>
    </div>

    <!-- Search & Filter -->
    <div class="card reveal active" style="margin-bottom: 40px; border-radius: 15px;">
        <form method="GET" action="menu.php" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" name="search" class="form-control" placeholder="Search for burgers, pizza, coffee..." value="<?= h($search) ?>">
            </div>
            <div style="width: 200px;">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php if($cat): ?>
                            <option value="<?= h($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Search</button>
            <?php if ($search || $category): ?>
                <a href="menu.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="menu-grid" id="menuGrid">
        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <div class="menu-card reveal active">
                    <div style="position: relative;">
                        <?php if ($item->image_url): ?>
                            <img src="<?= h($item->image_url) ?>" alt="<?= h($item->name) ?>" class="menu-img">
                        <?php else: ?>
                            <div class="menu-img" style="background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa;">No Image</div>
                        <?php endif; ?>
                        
                        <?php if (isLoggedIn()): ?>
                            <?php $is_fav = in_array($item->id, $user_favorites); ?>
                            <button class="toggle-favorite" data-id="<?= $item->id ?>" style="position: absolute; top: 10px; right: 10px; background: white; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: var(--transition);">
                                <i class="<?= $is_fav ? 'fa-solid' : 'fa-regular' ?> fa-heart" style="color: var(--primary-color); font-size: 18px;"></i>
                            </button>
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
            <?php endforeach; ?>
        <?php else: ?>
            <p style='grid-column: 1 / -1; text-align: center; color: var(--gray); padding: 50px 0;'>No menu items found. Try adjusting your search.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
