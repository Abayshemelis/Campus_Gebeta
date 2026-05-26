<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$current_page = basename($_SERVER['PHP_SELF']);
$admin_nav_items = [
    ['file' => 'index.php', 'icon' => 'fa-gauge', 'label' => 'Dashboard'],
    ['file' => 'menu.php', 'icon' => 'fa-burger', 'label' => 'Menu'],
    ['file' => 'orders.php', 'icon' => 'fa-list-check', 'label' => 'Orders'],
    ['file' => 'users.php', 'icon' => 'fa-users-gear', 'label' => 'Users'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Campus Gebeta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-topbar {
            background: var(--dark-color);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .admin-topbar .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }

        .admin-topbar .logo {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-topbar .logo span {
            color: var(--primary-color);
        }

        .admin-topbar .user-info {
            color: #ccc;
            font-size: 0.9rem;
        }

        .admin-topbar .user-info a {
            color: var(--primary-color);
        }

        .admin-subnav {
            background: #1a1b28;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .admin-subnav .container {
            display: flex;
            gap: 0;
            overflow-x: auto;
        }

        .admin-nav-link {
            color: #aaa;
            padding: 14px 18px;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 7px;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .admin-nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .admin-nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
    </style>
</head>

<body>

    <!-- Top Bar -->
    <header class="admin-topbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-gauge"></i> Admin <span>Panel</span>
            </a>
            <div class="user-info">
                <i class="fa-solid fa-circle-user"></i>
                <?= h($_SESSION['user_name']) ?>
                <span style="margin: 0 8px; color:#555;">|</span>
                <a href="../index.php"><i class="fa-solid fa-globe"></i> View Site</a>
                <span style="margin: 0 8px; color:#555;">|</span>
                <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
    </header>

    <!-- Sub Navigation -->
    <nav class="admin-subnav">
        <div class="container">
            <?php foreach ($admin_nav_items as $item): ?>
                <?php if (!is_file(__DIR__ . '/' . $item['file'])) continue; ?>
                <a href="<?= h($item['file']) ?>" class="admin-nav-link <?= $current_page == $item['file'] ? 'active' : '' ?>">
                    <i class="fa-solid <?= h($item['icon']) ?>"></i> <?= h($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <main class="main-content">
