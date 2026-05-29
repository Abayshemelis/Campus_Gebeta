<?php
require_once 'db.php';
require_once 'functions.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Gebeta | Manage Your Campus Meals</title>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <header class="navbar">
        <div class="container nav-container">
            <!-- Logo on the LEFT -->
            <a href="index.php" class="logo" style="display: flex; align-items: center; gap: 12px;">
                <img src="assets/images/gebeta_logo.png" alt="Campus Gebeta Logo" class="site-logo">
                <span style="color: var(--dark-color);">Campus <span style="color: var(--primary-color);">Gebeta</span></span>
            </a>

            <!-- Right side: theme toggle + hamburger -->
            <div style="display: flex; align-items: center; gap: 12px;">
                <button id="themeToggle" style="background: none; border: none; font-size: 20px; color: var(--dark-color); cursor: pointer; transition: var(--transition);">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <!-- Hamburger button moved to RIGHT -->
                <button class="nav-toggle" id="navToggle" aria-label="Open Menu" style="background: none; border: none; font-size: 22px; color: var(--dark-color); cursor: pointer; padding: 5px;">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Overlay for sidebar -->
    <div class="overlay" id="overlay"></div>

    <!-- Off-Canvas Side Navigation (slides in from RIGHT) -->
    <aside class="side-nav" id="sideNav">
        <button class="close-nav" id="closeNav" aria-label="Close Menu">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <?php if (isLoggedIn()): ?>
            <!-- User Info & Role Badge -->
            <div style="padding: 10px 20px 20px; border-bottom: 1px solid rgba(0,0,0,0.08); margin-bottom: 10px;">
                <p style="margin: 0 0 5px; font-weight: 600; font-size: 1rem;"><?= h($_SESSION['user_name']) ?></p>
                <span style="display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; color: white; background-color: <?= getRoleBadgeColor() ?>;">
                    <?= getRoleLabel() ?>
                </span>
            </div>
        <?php endif; ?>

        <ul class="side-nav-list">
            <li>
                <a href="index.php" class="side-nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house" style="margin-right: 10px;"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="menu.php" class="side-nav-link <?= $current_page == 'menu.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-utensils" style="margin-right: 10px;"></i> Full Menu
                </a>
            </li>

            <?php if (!isAdmin()): ?>
                <li>
                    <a href="cart.php" class="side-nav-link <?= $current_page == 'cart.php' ? 'active' : '' ?>" style="display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="fa-solid fa-cart-shopping" style="margin-right: 10px;"></i> My Cart</span>
                        <span class="cart-badge" id="cartCount" style="background-color: var(--primary-color); color: white; font-size: 12px; width: 22px; height: 22px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-weight: bold;"><?= getCartItemCount() ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <li>
                        <a href="admin/index.php" class="side-nav-link">
                            <i class="fa-solid fa-gauge" style="margin-right: 10px;"></i> Admin Dashboard
                        </a>
                    </li>
                <?php elseif (isSeller()): ?>
                    <li>
                        <a href="seller_dashboard.php" class="side-nav-link <?= $current_page == 'seller_dashboard.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-store" style="margin-right: 10px;"></i> Seller Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="seller_menu_item.php" class="side-nav-link <?= $current_page == 'seller_menu_item.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-plus" style="margin-right: 10px;"></i> Post Food/Drink
                        </a>
                    </li>
                    <li>
                        <a href="orders.php" class="side-nav-link <?= $current_page == 'orders.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-list" style="margin-right: 10px;"></i> My Orders
                        </a>
                    </li>
                    <li>
                        <a href="wallet.php" class="side-nav-link <?= $current_page == 'wallet.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-wallet" style="margin-right: 10px;"></i> Wallet
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="side-nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-gear" style="margin-right: 10px;"></i> Settings
                        </a>
                    </li>
                <?php else: /* student */ ?>
                    <li>
                        <a href="orders.php" class="side-nav-link <?= $current_page == 'orders.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-list" style="margin-right: 10px;"></i> My Orders
                        </a>
                    </li>
                    <li>
                        <a href="favorites.php" class="side-nav-link <?= $current_page == 'favorites.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-heart" style="margin-right: 10px;"></i> Favorites
                        </a>
                    </li>
                    <li>
                        <a href="meal_plans.php" class="side-nav-link <?= $current_page == 'meal_plans.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-calendar-check" style="margin-right: 10px;"></i> Meal Plans
                        </a>
                    </li>
                    <li>
                        <a href="wallet.php" class="side-nav-link <?= $current_page == 'wallet.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-wallet" style="margin-right: 10px;"></i> Wallet
                        </a>
                    </li>
                    <li>
                        <a href="settings.php" class="side-nav-link <?= $current_page == 'settings.php' ? 'active' : '' ?>">
                            <i class="fa-solid fa-gear" style="margin-right: 10px;"></i> Settings
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="logout.php" class="side-nav-link" style="color: var(--danger);">
                        <i class="fa-solid fa-right-from-bracket" style="margin-right: 10px;"></i> Logout
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php" class="side-nav-link <?= $current_page == 'login.php' ? 'active' : '' ?>">
                        <i class="fa-solid fa-right-to-bracket" style="margin-right: 10px;"></i> Login
                    </a>
                </li>
                <li>
                    <a href="register.php" class="side-nav-link btn btn-primary <?= $current_page == 'register.php' ? 'active' : '' ?>" style="text-align: center; color: white;">
                        Register
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="side-contact">
            <h4>Connect with us</h4>
            <div class="contact-links">
                <a href="mailto:support@campusgebeta.com" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
                <a href="https://t.me/campusgebeta" target="_blank" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
                <a href="https://instagram.com/campusgebeta" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </div>
    </aside>
    <main class="main-content">
