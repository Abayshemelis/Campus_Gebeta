<?php
require_once 'header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = "Item deleted successfully.";
    }
    $action = 'list';
}

// Handle Add / Edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_url = $_POST['image_url'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, image_url, category, is_available) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $description, $price, $image_url, $category, $is_available])) {
            $success = "Menu item added successfully.";
            $action = 'list';
        }
    } elseif ($action == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("UPDATE menu_items SET name=?, description=?, price=?, image_url=?, category=?, is_available=? WHERE id=?");
        if ($stmt->execute([$name, $description, $price, $image_url, $category, $is_available, $id])) {
            $success = "Menu item updated successfully.";
            $action = 'list';
        }
    }
}
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Manage Menu</h2>
        <?php if ($action == 'list'): ?>
            <a href="menu.php?action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Item</a>
        <?php else: ?>
            <a href="menu.php" class="btn btn-secondary">Back to List</a>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>

    <?php if ($action == 'list'): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY created_at DESC");
                    while ($row = $stmt->fetch()):
                    ?>
                        <tr>
                            <td>
                                <?php if ($row->image_url): ?>
                                    <img src="<?= h($row->image_url) ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: #eee; border-radius: 5px;"></div>
                                <?php endif; ?>
                            </td>
                            <td><?= h($row->name) ?></td>
                            <td><?= h($row->category) ?></td>
                            <td><?= number_format($row->price, 2) ?> ETB</td>
                            <td>
                                <span class="badge" style="background: <?= $row->is_available ? 'var(--success)' : 'var(--danger)' ?>; color: white;">
                                    <?= $row->is_available ? 'Available' : 'Out of Stock' ?>
                                </span>
                            </td>
                            <td>
                                <a href="menu.php?action=edit&id=<?= $row->id ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;"><i class="fa-solid fa-edit"></i> Edit</a>
                                <a href="menu.php?action=delete&id=<?= $row->id ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($action == 'add' || $action == 'edit'): ?>
        <?php
        $item = null;
        if ($action == 'edit' && isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $item = $stmt->fetch();
        }
        ?>
        <div class="form-container" style="max-width: 800px; margin: 0;">
            <form action="menu.php?action=<?= $action ?><?= isset($_GET['id']) ? '&id=' . $_GET['id'] : '' ?>" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $item ? h($item->name) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Price (ETB)</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?= $item ? h($item->price) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" class="form-control" value="<?= $item ? h($item->category) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="image_url" class="form-control" value="<?= $item ? h($item->image_url) : '' ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= $item ? h($item->description) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_available" value="1" <?= (!$item || $item->is_available) ? 'checked' : '' ?>>
                        Available in Stock
                    </label>
                </div>
                <button type="submit" class="btn btn-primary"><?= $action == 'add' ? 'Add Item' : 'Update Item' ?></button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>