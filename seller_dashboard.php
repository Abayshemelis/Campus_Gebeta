<?php
require_once 'includes/header.php';
requireLogin();

// Only sellers and admins can access the seller dashboard
if (!canPostMarket()) {
    ?>
    <div class="container" style="padding: 60px 0; text-align: center;">
        <i class="fa-solid fa-lock" style="font-size: 60px; color: var(--danger); margin-bottom: 20px;"></i>
        <h2>Access Denied</h2>
        <p style="color: var(--gray); margin-bottom: 25px;">This dashboard is restricted to Sellers only.</p>
        <a href="index.php" class="btn btn-primary">Go Home</a>
    </div>
    <?php
    require_once 'includes/footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle Item Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    // Ensure the item belongs to the logged-in user (or user is admin)
    if (isAdmin()) {
        $stmt = $pdo->prepare("DELETE FROM marketplace_items WHERE id = ?");
        $execute_params = [$delete_id];
    } else {
        $stmt = $pdo->prepare("DELETE FROM marketplace_items WHERE id = ? AND user_id = ?");
        $execute_params = [$delete_id, $user_id];
    }
    
    if ($stmt->execute($execute_params)) {
        $success_msg = "Item deleted successfully.";
    } else {
        $error_msg = "Failed to delete item.";
    }
}

// Fetch Seller's Items
$stmt = $pdo->prepare("SELECT * FROM marketplace_items WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

// Get Total Items count
$total_items = count($items);
?>

<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 30px; gap: 15px;">
        <div>
            <h2><i class="fa-solid fa-store" style="color: var(--primary-color);"></i> My Shop Dashboard</h2>
            <p style="color: var(--gray);">Manage your Gebeta Market inventory.</p>
        </div>
        <a href="market_post.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Post New Item</a>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= h($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?= h($error_msg) ?></div>
    <?php endif; ?>

    <!-- Stats row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="card" style="padding: 25px; text-align: center; border-radius: 12px; border-top: 4px solid var(--primary-color);">
            <i class="fa-solid fa-boxes-stacked" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h3 style="font-size: 2rem; margin: 0;"><?= $total_items ?></h3>
            <p style="color: var(--gray); margin: 0;">Active Listings</p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card" style="padding: 20px; border-radius: 12px; overflow: hidden;">
        <h3 style="margin-bottom: 20px;">Your Items</h3>
        <?php if ($total_items > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Date Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item->image_url): ?>
                                        <img src="<?= h($item->image_url) ?>" alt="<?= h($item->title) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 10px;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 500;"><?= h($item->title) ?></td>
                                <td><span class="badge" style="background: var(--light-color); color: var(--dark-color);"><?= h($item->category) ?></span></td>
                                <td style="color: var(--primary-color); font-weight: 700;"><?= number_format($item->price, 2) ?> ETB</td>
                                <td><?= date('M d, Y', strtotime($item->created_at)) ?></td>
                                <td>
                                    <a href="seller_dashboard.php?delete_id=<?= $item->id ?>" class="btn" style="background: #ffebee; color: var(--danger); padding: 5px 10px; font-size: 0.9rem;" onclick="return confirm('Are you sure you want to delete this item?');">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 0;">
                <i class="fa-solid fa-box-open" style="font-size: 50px; color: var(--border-color); margin-bottom: 15px;"></i>
                <p style="color: var(--gray);">You haven't posted any items yet.</p>
                <a href="market_post.php" class="btn btn-primary" style="margin-top: 15px;">Start Selling</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
