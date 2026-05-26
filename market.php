<?php
require_once 'includes/header.php';

// Handle search and filtering
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$query = "SELECT m.*, u.name as seller_name FROM marketplace_items m JOIN users u ON m.user_id = u.id";
$params = [];

$conditions = [];
if ($search) {
    $conditions[] = "(m.title LIKE ? OR m.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category) {
    $conditions[] = "m.category = ?";
    $params[] = $category;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}
$query .= " ORDER BY m.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <h2><i class="fa-solid fa-store" style="color: var(--primary-color);"></i> Gebeta Market</h2>
        <?php if (canPostMarket()): ?>
            <a href="market_post.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Post Item</a>
        <?php elseif (!isLoggedIn()): ?>
            <a href="login.php" class="btn btn-primary">Login to Post</a>
        <?php else: ?>
            <span style="font-size:0.85rem; color:var(--gray);"><i class="fa-solid fa-lock"></i> Only sellers &amp; admins can post items</span>
        <?php endif; ?>
    </div>

    <!-- Search & Filter -->
    <div class="card" style="margin-bottom: 30px;">
        <form method="GET" action="market.php" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" name="search" class="form-control" placeholder="Search meals, snacks, drinks..." value="<?= h($search) ?>">
            </div>
            <div style="width: 200px;">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <option value="Homemade Meals" <?= $category == 'Homemade Meals' ? 'selected' : '' ?>>Homemade Meals</option>
                    <option value="Packed Snacks" <?= $category == 'Packed Snacks' ? 'selected' : '' ?>>Packed Snacks</option>
                    <option value="Drinks" <?= $category == 'Drinks' ? 'selected' : '' ?>>Drinks</option>
                    <option value="Groceries" <?= $category == 'Groceries' ? 'selected' : '' ?>>Groceries</option>
                    <option value="Other" <?= $category == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Search</button>
            <?php if ($search || $category): ?>
                <a href="market.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 50px 0; color: var(--gray);">
            <i class="fa-solid fa-box-open" style="font-size: 48px; margin-bottom: 20px;"></i>
            <h3>No items found</h3>
            <p>Try adjusting your search or be the first to post an item!</p>
        </div>
    <?php else: ?>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
            <?php foreach ($items as $item): ?>
                <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                    <?php if ($item->image_url): ?>
                        <img src="<?= h($item->image_url) ?>" alt="<?= h($item->title) ?>" style="width: 100%; height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: var(--light-color); display: flex; justify-content: center; align-items: center;">
                            <i class="fa-solid fa-image" style="font-size: 48px; color: var(--gray);"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div style="padding: 20px; flex: 1; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <span class="badge" style="background: var(--dark-color); color: white;"><?= h($item->category) ?></span>
                            <span style="font-weight: 700; color: var(--primary-color); font-size: 1.2rem;"><?= number_format($item->price, 2) ?> ETB</span>
                        </div>
                        <h3 style="margin-bottom: 10px;"><?= h($item->title) ?></h3>
                        <p style="color: var(--gray); font-size: 0.9rem; margin-bottom: 15px; flex: 1;"><?= h(substr($item->description, 0, 100)) ?><?= strlen($item->description) > 100 ? '...' : '' ?></p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 15px;">
                            <span style="font-size: 0.85rem; color: var(--gray);"><i class="fa-solid fa-user"></i> <?= h($item->seller_name) ?></span>
                            <span style="font-size: 0.85rem; color: var(--gray);"><i class="fa-solid fa-clock"></i> <?= date('M d', strtotime($item->created_at)) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
