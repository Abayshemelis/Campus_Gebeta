<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle Top-Up
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['topup_amount'])) {
    $amount = (float)$_POST['topup_amount'];
    if ($amount > 0 && $amount <= 10000) {
        try {
            $pdo->beginTransaction();
            
            // Add to balance
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt->execute([$amount, $user_id]);
            
            // Log transaction
            $stmt = $pdo->prepare("INSERT INTO wallet_transactions (user_id, amount, type, description) VALUES (?, ?, 'credit', ?)");
            $stmt->execute([$user_id, $amount, 'Added via Telebirr/CBE Birr']);
            
            $pdo->commit();
            $success = "Successfully added " . number_format($amount, 2) . " ETB to your wallet!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to process transaction. Please try again.";
        }
    } else {
        $error = "Invalid amount. You can top up between 1 and 10,000 ETB.";
    }
}

// Fetch current balance
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetchColumn() ?: 0.00;

// Fetch transactions
$stmt = $pdo->prepare("SELECT * FROM wallet_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i class="fa-solid fa-wallet" style="color: var(--primary-color);"></i> My Wallet</h2>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 40px;">
        <!-- Balance Card -->
        <div class="card reveal active" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border-radius: 15px; padding: 40px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
            <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 10px;">Available Balance</p>
            <h1 style="font-size: 3.5rem; margin: 0;"><?= number_format($balance, 2) ?> <span style="font-size: 1.5rem;">ETB</span></h1>
        </div>

        <!-- Top Up Form -->
        <div class="card reveal active" style="border-radius: 15px; padding: 30px;">
            <h3 style="margin-bottom: 20px;">Top Up Wallet</h3>
            <form method="POST" action="wallet.php">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Amount (ETB)</label>
                    <input type="number" name="topup_amount" class="form-control" step="0.01" min="1" max="10000" required placeholder="e.g. 500">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px;">Payment Method</label>
                    <select class="form-control" required>
                        <option value="telebirr">Telebirr</option>
                        <option value="cbe">CBE Birr</option>
                        <option value="awash">Awash Birr</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-plus-circle"></i> Add Funds</button>
            </form>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card reveal active" style="border-radius: 15px;">
        <h3 style="margin-bottom: 20px;">Transaction History</h3>
        <?php if (empty($transactions)): ?>
            <p style="color: var(--gray); text-align: center; padding: 20px 0;">No transactions yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?= date('M d, Y h:i A', strtotime($txn->created_at)) ?></td>
                                <td><?= h($txn->description) ?></td>
                                <td style="font-weight: bold; color: <?= $txn->type == 'credit' ? 'var(--success)' : 'var(--danger)' ?>;">
                                    <?= $txn->type == 'credit' ? '+' : '-' ?><?= number_format($txn->amount, 2) ?> ETB
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
