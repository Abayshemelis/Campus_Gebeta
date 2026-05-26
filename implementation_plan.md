# Campus Gebeta (Campus Café Management System)

This document outlines the implementation plan for the Campus Gebeta project, a web-based system designed to streamline how students interact with campus cafés by enabling online ordering and order tracking.

## Goal Description

The objective is to build a full-stack web application using HTML, CSS, JavaScript, PHP, and MySQL. The system will feature:
- Student registration and login
- Real-time menu browsing and search (using AJAX)
- Cart and checkout functionality
- Order tracking for students
- Admin panel for managing menu items (CRUD) and updating order statuses

## User Review Required

> [!IMPORTANT]  
> Please review the proposed database schema and features below. Specifically, confirm if you have a local web server (like XAMPP or WAMP) installed and running with MySQL and Apache, as this is required to run PHP and MySQL locally.

## Open Questions

> [!WARNING]  
> 1. Do you already have a local development environment set up (e.g., XAMPP, WAMP, or similar)?
> 2. What specific details would you like to collect from students during registration? (Currently planned: Name, Email, Password).
> 3. Should there be any payment integration (e.g., Chapa, Telebirr), or is the system simply for placing orders to be paid in person/upon pickup?

## Proposed Architecture & Structure

The project will follow a standard PHP application structure.

### Directory Structure
```text
Campus_Gebeta/
├── index.php                 # Home page / Menu display
├── login.php                 # User login
├── register.php              # User registration
├── logout.php                # Logout script
├── cart.php                  # Cart and checkout page
├── orders.php                # Student order tracking
├── includes/
│   ├── db.php                # Database connection
│   ├── header.php            # Common header
│   ├── footer.php            # Common footer
│   └── functions.php         # Helper functions
├── admin/
│   ├── index.php             # Admin dashboard
│   ├── menu.php              # Manage menu (CRUD)
│   ├── orders.php            # Manage and update orders
│   └── header.php            # Admin header
├── ajax/
│   ├── add_to_cart.php       # AJAX handler for cart additions
│   ├── update_cart.php       # AJAX handler for cart updates
│   └── search_menu.php       # AJAX handler for live search
└── assets/
    ├── css/
    │   └── style.css         # Main stylesheet (custom modern design)
    ├── js/
    │   └── main.js           # AJAX and interactivity logic
    └── images/               # Menu item images
```

### Database Schema

**1. `users` Table**
- `id` (INT, Primary Key, Auto Increment)
- `name` (VARCHAR)
- `email` (VARCHAR, Unique)
- `password` (VARCHAR, Hashed)
- `role` (ENUM: 'student', 'admin' - default 'student')
- `created_at` (TIMESTAMP)

**2. `menu_items` Table**
- `id` (INT, Primary Key, Auto Increment)
- `name` (VARCHAR)
- `description` (TEXT)
- `price` (DECIMAL)
- `image_url` (VARCHAR)
- `category` (VARCHAR)
- `is_available` (BOOLEAN)
- `created_at` (TIMESTAMP)

**3. `orders` Table**
- `id` (INT, Primary Key, Auto Increment)
- `user_id` (INT, Foreign Key referencing `users(id)`)
- `total_amount` (DECIMAL)
- `status` (ENUM: 'Pending', 'Preparing', 'Ready', 'Served', 'Cancelled')
- `created_at` (TIMESTAMP)

**4. `order_items` Table**
- `id` (INT, Primary Key, Auto Increment)
- `order_id` (INT, Foreign Key referencing `orders(id)`)
- `menu_item_id` (INT, Foreign Key referencing `menu_items(id)`)
- `quantity` (INT)
- `price` (DECIMAL)

## Proposed Implementation Phases

1.  **Phase 1: Database Setup and Core Layout**
    -   Create the database and tables using an initial SQL script.
    -   Set up the directory structure.
    -   Create the `includes/db.php` connection file.
    -   Design the global layout (`header.php`, `footer.php`, `style.css`) with a modern, dynamic, and responsive aesthetic.
2.  **Phase 2: User Authentication**
    -   Implement `register.php` with input validation and password hashing.
    -   Implement `login.php` using PHP sessions to handle user state.
    -   Implement `logout.php`.
3.  **Phase 3: Admin & Menu Management (CRUD)**
    -   Build the admin dashboard (`admin/index.php`).
    -   Build the menu management interface (`admin/menu.php`) allowing the admin to Create, Read, Update, and Delete menu items.
4.  **Phase 4: Frontend Menu & AJAX Search**
    -   Populate `index.php` with the live menu items from the database.
    -   Implement the live search functionality via JavaScript (`ajax/search_menu.php`).
5.  **Phase 5: Cart and Checkout System**
    -   Implement "Add to Cart" functionality via AJAX (`ajax/add_to_cart.php`) storing cart data in the PHP session.
    -   Build the `cart.php` page to display cart items, update quantities, and submit the final order.
6.  **Phase 6: Order Tracking & Management**
    -   Save orders to the `orders` and `order_items` tables upon checkout.
    -   Build `orders.php` for students to track their current order status.
    -   Build `admin/orders.php` for the admin to update statuses (e.g., from "Pending" to "Preparing" to "Ready").

## Verification Plan

### Manual Verification
1.  **Environment Check**: Verify that a local server (XAMPP/WAMP) is running.
2.  **Database Creation**: Run the generated SQL script to ensure tables are created successfully.
3.  **User Flow**: Register a new student, log in, browse the menu, add items to the cart, and place an order.
4.  **Admin Flow**: Log in as an admin, add a new menu item, view incoming orders, and change an order's status.
5.  **AJAX Testing**: Verify that searching the menu and adding to the cart updates the UI without page reloads.
