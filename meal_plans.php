<?php
require_once 'includes/header.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle Subscription
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscribe_plan_id'])) {
    $plan_id = (int)$_POST['subscribe_plan_id'];
    
    // Fetch plan details
    $stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE id = ?");
    $stmt->execute([$plan_id]);
    $plan = $stmt->fetch();
    
    if ($plan) {
        // Fetch user wallet balance
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $balance = $stmt->fetchColumn();
        
        if ($balance >= $plan->price) {
            try {
                $pdo->beginTransaction();
                
                // Deduct from wallet
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
                $stmt->execute([$plan->price, $user_id]);
                
                // Log transaction
                $stmt = $pdo->prepare("INSERT INTO wallet_transactions (user_id, amount, type, description) VALUES (?, ?, 'debit', ?)");
                $stmt->execute([$user_id, $plan->price, "Subscribed to " . $plan->name]);
                
                // Add subscription
                $end_date = date('Y-m-d H:i:s', strtotime("+{$plan->duration_days} days"));
                $stmt = $pdo->prepare("INSERT INTO user_meal_plans (user_id, plan_id, meals_remaining, end_date) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $plan->id, $plan->meals_per_week * ($plan->duration_days / 7), $end_date]);
                
                $pdo->commit();
                $success = "Successfully subscribed to {$plan->name}!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Failed to process subscription. Please try again.";
            }
        } else {
            $error = "Insufficient wallet balance. Please top up your wallet first.";
        }
    } else {
        $error = "Invalid plan selected.";
    }
}

// Fetch all available plans
$stmt = $pdo->query("SELECT * FROM meal_plans ORDER BY price ASC");
$plans = $stmt->fetchAll();

// Fetch user's active plans
$stmt = $pdo->prepare("
    SELECT ump.*, mp.name 
    FROM user_meal_plans ump 
    JOIN meal_plans mp ON ump.plan_id = mp.id 
    WHERE ump.user_id = ? AND ump.status = 'active' AND ump.end_date > NOW()
");
$stmt->execute([$user_id]);
$active_plans = $stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i class="fa-solid fa-calendar-check" style="color: var(--primary-color);"></i> Meal Plans</h2>
        <a href="wallet.php" class="btn btn-secondary">My Wallet</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>

    <!-- Active Plans -->
    <?php if (!empty($active_plans)): ?>
        <h3 style="margin-bottom: 20px;">Your Active Subscriptions</h3>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-bottom: 50px;">
            <?php foreach ($active_plans as $ap): ?>
                <div class="card" style="border-left: 5px solid var(--success); padding: 20px; border-radius: 10px;">
                    <h4 style="margin-bottom: 10px; color: var(--success);"><?= h($ap->name) ?></h4>
                    <p style="margin-bottom: 5px;"><strong>Meals Remaining:</strong> <?= $ap->meals_remaining ?></p>
                    <p style="color: var(--gray); font-size: 0.9rem;"><strong>Expires:</strong> <?= date('M d, Y', strtotime($ap->end_date)) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Available Plans -->
    <h3 style="margin-bottom: 20px;">Available Plans</h3>
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        <?php foreach ($plans as $plan): ?>
            <div class="card reveal active" style="border-radius: 15px; display: flex; flex-direction: column; text-align: center; padding: 40px 20px;">
                <h3 style="margin-bottom: 15px; color: var(--dark-color);"><?= h($plan->name) ?></h3>
                <h2 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 20px;"><?= number_format($plan->price, 2) ?> <span style="font-size: 1rem; color: var(--gray);">ETB / <?= $plan->duration_days ?> days</span></h2>
                <p style="color: var(--text-color); margin-bottom: 20px; flex-grow: 1;"><?= h($plan->description) ?></p>
                <ul style="list-style: none; padding: 0; margin-bottom: 30px; color: var(--gray); text-align: left;">
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--success); margin-right: 10px;"></i> <?= $plan->meals_per_week ?> meals per week</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--success); margin-right: 10px;"></i> valid for <?= $plan->duration_days ?> days</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--success); margin-right: 10px;"></i> skip the line</li>
                </ul>
                <form method="POST" action="meal_plans.php" onsubmit="return confirm('Are you sure you want to subscribe to <?= h($plan->name) ?> for <?= number_format($plan->price, 2) ?> ETB?');">
                    <input type="hidden" name="subscribe_plan_id" value="<?= $plan->id ?>">
                    <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 30px;">Subscribe Now</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
