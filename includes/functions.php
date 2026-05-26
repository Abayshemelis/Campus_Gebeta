<?php
// includes/functions.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect helper
function redirect($url)
{
    header("Location: $url");
    exit();
}

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Check if user is a seller
function isSeller()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'seller';
}

// Check if user is a regular student
function isStudent()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student';
}

// Check if user can post/manage menu items (admin or seller only)
function canPostMenuItem()
{
    return isAdmin() || isSeller();
}

// Require login to access a page
function requireLogin()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

// Require admin to access a page
function requireAdmin()
{
    if (!isAdmin()) {
        redirect('../login.php');
    }
}

// Get the current user role label
function getRoleLabel()
{
    $role = $_SESSION['user_role'] ?? 'guest';
    $labels = [
        'admin'    => 'Admin',
        'seller'   => 'Seller',
        'student'  => 'Student',
    ];
    return $labels[$role] ?? 'Guest';
}

// Get role badge color
function getRoleBadgeColor()
{
    $role = $_SESSION['user_role'] ?? '';
    $colors = [
        'admin'    => '#e63946',
        'seller'   => '#f4a261',
        'student'  => '#457b9d',
    ];
    return $colors[$role] ?? '#888';
}

// Get the total number of items in the cart
function getCartItemCount()
{
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

// Calculate the total price of items in the cart
function getCartTotal()
{
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

// Sanitize output
function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
