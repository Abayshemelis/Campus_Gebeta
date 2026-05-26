<?php
require_once 'includes/header.php';
requireLogin();

if (!isSeller() && !isAdmin()) {
    redirect('index.php');
}

$user_id = $_SESSION['user_id'];
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$item = null;

$success = '';
$error = '';

// If editing, fetch existing item details
if ($item_id > 0) {
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
        $stmt->execute([$item_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ? AND seller_id = ?");
        $stmt->execute([$item_id, $user_id]);
    }
    $item = $stmt->fetch();
    if (!$item) {
        redirect('seller_dashboard.php');
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if (empty($name) || $price <= 0 || empty($category)) {
        $error = "Please fill in all required fields (Name, Price, Category).";
    } else {
        if ($item_id > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, description = ?, category = ?, image_url = ?, is_available = ? WHERE id = ?");
            if ($stmt->execute([$name, $price, $description, $category, $image_url, $is_available, $item_id])) {
                $success = "Menu item updated successfully.";
                // Refresh item data
                if (isAdmin()) {
                    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
                    $stmt->execute([$item_id]);
                } else {
                    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ? AND seller_id = ?");
                    $stmt->execute([$item_id, $user_id]);
                }
                $item = $stmt->fetch();
            } else {
                $error = "Database update failed.";
            }
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO menu_items (name, price, description, category, image_url, is_available, seller_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $price, $description, $category, $image_url, $is_available, $user_id])) {
                $success = "New menu item posted successfully!";
                // Redirect to dashboard
                $_SESSION['success_flash'] = $success;
                redirect('seller_dashboard.php');
            } else {
                $error = "Failed to save menu item.";
            }
        }
    }
}
?>

<div class="container" style="padding: 40px 0;">
    <div style="max-width: 600px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2><?= $item ? '<i class="fa-solid fa-pen-to-square" style="color:var(--primary-color);"></i> Edit Menu Item' : '<i class="fa-solid fa-plus" style="color:var(--primary-color);"></i> Post Food/Drink' ?></h2>
            <a href="seller_dashboard.php" class="btn btn-secondary" style="font-size: 0.9rem;"><i class="fa-solid fa-arrow-left"></i> Dashboard</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
        <?php endif; ?>

        <div class="card" style="padding: 30px; border-radius: 12px;">
            <form method="POST" action="seller_menu_item.php<?= $item ? '?id=' . $item->id : '' ?>">
                <div class="form-group">
                    <label for="name">Item Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g., Special Firfir, Mango Juice" value="<?= h($_POST['name'] ?? ($item ? $item->name : '')) ?>" required>
                </div>

                <div class="form-group">
                    <label for="price">Price (ETB) <span style="color: var(--danger);">*</span></label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" placeholder="0.00" value="<?= h($_POST['price'] ?? ($item ? $item->price : '')) ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category <span style="color: var(--danger);">*</span></label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <option value="Breakfast" <?= ($_POST['category'] ?? ($item ? $item->category : '')) == 'Breakfast' ? 'selected' : '' ?>>Breakfast</option>
                        <option value="Lunch" <?= ($_POST['category'] ?? ($item ? $item->category : '')) == 'Lunch' ? 'selected' : '' ?>>Lunch/Dinner</option>
                        <option value="Drinks" <?= ($_POST['category'] ?? ($item ? $item->category : '')) == 'Drinks' ? 'selected' : '' ?>>Drinks</option>
                        <option value="Snacks" <?= ($_POST['category'] ?? ($item ? $item->category : '')) == 'Snacks' ? 'selected' : '' ?>>Snacks</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://example.com/food.jpg" value="<?= h($_POST['image_url'] ?? ($item ? $item->image_url : '')) ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Briefly describe the ingredients, size, preparation time, etc."><?= h($_POST['description'] ?? ($item ? $item->description : '')) ?></textarea>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 25px; margin-bottom: 25px;">
                    <input type="checkbox" name="is_available" id="is_available" style="width: 18px; height: 18px; cursor: pointer;" <?= (isset($_POST['name']) ? isset($_POST['is_available']) : ($item ? $item->is_available : true)) ? 'checked' : '' ?>>
                    <label for="is_available" style="margin: 0; cursor: pointer; font-weight: 500;">Available for order</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1.05rem;">
                    <i class="fa-solid fa-circle-check"></i> <?= $item ? 'Save Changes' : 'Post Item to Menu' ?>
                </button>
            </form>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
