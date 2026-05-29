<?php require_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section reveal active">
    <div class="container hero-container">
        <!-- Left: Content -->
        <div class="hero-content-left">
            <span class="hero-badge">
                <i class="fa-solid fa-utensils"></i> Authentic Ethiopian Campus Dining
            </span>
            <h1 class="hero-title">
                Welcome to <span class="hero-highlight">Campus Gebeta</span>
            </h1>
            <p class="hero-text">
                Skip the queue. Browse authentic local menus, order online, and pick up your hot meals when they're ready. The ultimate food ordering platform for university students.
            </p>
            <div class="hero-buttons">
                <a href="#menu" class="btn btn-primary hero-btn-primary">
                    <i class="fa-solid fa-fire"></i> Order Now
                </a>
                <?php if (!isLoggedIn()): ?>
                    <a href="register.php" class="btn hero-btn-secondary">
                        Get Started <i class="fa-solid fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right: Desktop App Mockup (Creative & Desktop-Descriptive Placement) -->
        <div class="hero-content-right">
            <!-- Background decorative glows -->
            <div class="hero-glow hero-glow-orange"></div>
            <div class="hero-glow hero-glow-green"></div>
            
            <!-- Desktop App Mockup Frame replaced with hero image -->
            <div class="desktop-mockup">
                <img src="assets/images/gebeta_hero.png" alt="Campus Gebeta Hero" class="desktop-hero-img">
            </div>
        </div>
    </div>
</section>

<div class="container" style="margin-top: -40px; position: relative; z-index: 10;">
    <div class="search-container reveal" style="background: var(--card-bg); padding: 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <i class="fa-solid fa-search search-icon" style="top: 50%;"></i>
        <input type="text" id="searchInput" class="search-input" placeholder="Search for burgers, pizza, coffee..." style="border: none; background: var(--bg-color);">
    </div>
</div>

<div class="container" style="padding-bottom: 60px;">
    <!-- Featured Meals -->
    <section id="menu" style="margin-top: 60px;">
        <div class="reveal" style="text-align: center; margin-bottom: 40px;">
            <span style="color: var(--primary-color); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Fresh & Hot</span>
            <h2 style="font-size: 2.5rem;">Featured Meals</h2>
            <div style="width: 60px; height: 4px; background: var(--secondary-color); margin: 15px auto 0; border-radius: 2px;"></div>
        </div>
        
        <div class="menu-grid" id="menuGrid">
            <?php
            // Fetch top 6 items with reviews and seller info
            $stmt = $pdo->query("
                SELECT mi.*, 
                       COALESCE(r.avg_rating, 0.0) as avg_rating, 
                       COALESCE(r.reviews_count, 0) as reviews_count,
                       u.name as seller_name
                FROM menu_items mi
                LEFT JOIN (
                    SELECT menu_item_id, AVG(rating) as avg_rating, COUNT(*) as reviews_count 
                    FROM menu_item_ratings 
                    GROUP BY menu_item_id
                ) r ON mi.id = r.menu_item_id
                LEFT JOIN users u ON mi.seller_id = u.id
                WHERE mi.is_available = 1
                ORDER BY RAND() 
                LIMIT 6
            ");
            $items = $stmt->fetchAll();
            
            $user_favorites = [];
            if (isLoggedIn()) {
                $stmt = $pdo->prepare("SELECT menu_item_id FROM user_favorites WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user_favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            
            if (count($items) > 0) {
                foreach ($items as $item) {
                    ?>
                    <div class="menu-card reveal">
                        <div class="menu-img-container">
                            <?php if ($item->image_url): ?>
                                <img src="<?= h($item->image_url) ?>" alt="<?= h($item->name) ?>" class="menu-img">
                            <?php else: ?>
                                <div class="menu-img" style="background: #eee; display: flex; align-items: center; justify-content: center; color: #aaa;">No Image</div>
                            <?php endif; ?>
                            
                            <?php if (isLoggedIn()): ?>
                                <?php $is_fav = in_array($item->id, $user_favorites); ?>
                                <button class="toggle-favorite" data-id="<?= $item->id ?>" style="position: absolute; top: 10px; right: 10px; background: white; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: var(--transition);">
                                    <i class="<?= $is_fav ? 'fa-solid' : 'fa-regular' ?> fa-heart" style="color: var(--primary-color); font-size: 18px;"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="menu-content">
                            <div class="menu-title">
                                <?= h($item->name) ?>
                                <span class="menu-price"><?= number_format($item->price, 2) ?> ETB</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 0.82rem;">
                                <!-- Star rating badge -->
                                <div class="card-rating-badge" data-id="<?= $item->id ?>" data-name="<?= h($item->name) ?>">
                                    <i class="fa-solid fa-star"></i> 
                                    <span class="rating-val-<?= $item->id ?>"><?= number_format($item->avg_rating, 1) ?></span> 
                                    <span class="rating-count-<?= $item->id ?>" style="color: var(--gray); font-weight: normal;">(<?= $item->reviews_count ?>)</span>
                                </div>
                                <!-- Seller badge -->
                                <?php if ($item->seller_name): ?>
                                    <span style="color: var(--gray);"><i class="fa-solid fa-shop" style="color: var(--primary-color);"></i> <?= h($item->seller_name) ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="menu-desc"><?= h($item->description) ?></p>
                            <div class="menu-footer">
                                <span class="badge" style="background: var(--secondary-color); color: white;"><?= h($item->category) ?></span>
                                <?php if (!isAdmin()): ?>
                                    <button class="btn btn-primary add-to-cart" data-id="<?= $item->id ?>"><i class="fa-solid fa-cart-plus"></i> Add</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1 / -1; text-align: center; color: var(--gray);'>No items available right now.</p>";
            }
            ?>
        </div>
        <div class="reveal" style="text-align: center; margin-top: 40px;">
            <a href="menu.php" class="btn btn-secondary" style="border-radius: 30px; padding: 10px 30px;">View Full Menu</a>
        </div>
    </section>

    <!-- Student Discounts -->
    <section class="reveal" style="margin-top: 80px; background: linear-gradient(135deg, var(--primary-color), #ff5a5f); border-radius: 20px; padding: 60px 40px; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 30px; box-shadow: 0 15px 30px rgba(255, 140, 0, 0.2);">
        <div style="flex: 1; min-width: 300px;">
            <span style="background: white; color: var(--primary-color); padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; margin-bottom: 15px; display: inline-block;">Limited Time</span>
            <h2 style="color: white; font-size: 2.5rem; margin-bottom: 15px;">50% Off Your First Order!</h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 20px;">Use your university ID to sign up and get half price on your first meal at any campus cafeteria.</p>
            <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="btn" style="background: var(--dark-color); color: white; border-radius: 30px; padding: 12px 30px;">Claim Discount</a>
            <?php endif; ?>
        </div>
        <div style="flex: 1; text-align: right; min-width: 300px; display: flex; justify-content: flex-end;">
            <i class="fa-solid fa-tags" style="font-size: 150px; opacity: 0.2;"></i>
        </div>
    </section>

    <!-- Popular Cafeterias -->
    <section style="margin-top: 80px;">
        <div class="reveal" style="text-align: center; margin-bottom: 40px;">
            <span style="color: var(--secondary-color); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Top Rated</span>
            <h2 style="font-size: 2.5rem;">Popular Cafeterias</h2>
            <div style="width: 60px; height: 4px; background: var(--primary-color); margin: 15px auto 0; border-radius: 2px;"></div>
        </div>
        
        <div class="grid">
            <article class="cafeteria-card reveal">
                <div class="cafeteria-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=2047" alt="Main Cafe">
                    <span class="cafeteria-status-badge open">Open Now</span>
                    <span class="cafeteria-rating-badge"><i class="fa-solid fa-star"></i> 4.8</span>
                </div>
                <div class="cafeteria-details">
                    <h3>Main Student Lounge</h3>
                    <p><i class="fa-solid fa-location-dot" style="color: var(--primary-color);"></i> Block 4, Ground Floor</p>
                    <a href="menu.php" class="cafeteria-btn">View Menu <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </article>

            <article class="cafeteria-card reveal" style="transition-delay: 0.1s;">
                <div class="cafeteria-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1497935586351-b67a49e012bf?q=80&w=2069" alt="Coffee Spot">
                    <span class="cafeteria-status-badge open">Open Now</span>
                    <span class="cafeteria-rating-badge"><i class="fa-solid fa-star"></i> 4.9</span>
                </div>
                <div class="cafeteria-details">
                    <h3>The Coffee Spot</h3>
                    <p><i class="fa-solid fa-location-dot" style="color: var(--primary-color);"></i> Library Wing</p>
                    <a href="menu.php" class="cafeteria-btn">View Menu <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </article>

            <article class="cafeteria-card reveal" style="transition-delay: 0.2s;">
                <div class="cafeteria-img-wrapper">
                    <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=1974" alt="Dorm Cafe">
                    <span class="cafeteria-status-badge closed">Closed</span>
                    <span class="cafeteria-rating-badge"><i class="fa-solid fa-star"></i> 4.5</span>
                </div>
                <div class="cafeteria-details">
                    <h3>Dormitory Bites</h3>
                    <p><i class="fa-solid fa-location-dot" style="color: var(--primary-color);"></i> Near Block 12</p>
                    <a href="menu.php" class="cafeteria-btn">View Menu <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </article>
        </div>
    </section>

    <!-- Rating Features Showcase -->
    <section class="reveal" style="margin-top: 80px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <span style="color: var(--primary-color); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Transparency & Quality</span>
            <h2 style="font-size: 2.5rem;">Rate Your Dining Experience</h2>
            <div style="width: 60px; height: 4px; background: var(--secondary-color); margin: 15px auto 0; border-radius: 2px;"></div>
        </div>
        
        <div style="display: flex; gap: 30px; align-items: center; justify-content: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px; text-align: center; padding: 20px;">
                <i class="fa-solid fa-star-half-stroke" style="font-size: 4rem; color: #f1c40f; margin-bottom: 15px;"></i>
                <h4>Star Ratings</h4>
                <p style="color: var(--gray); font-size: 0.95rem;">Rate menu items directly out of 5 stars so others know what is delicious.</p>
            </div>
            <div style="flex: 1; min-width: 300px; text-align: center; padding: 20px;">
                <i class="fa-solid fa-comments" style="font-size: 4rem; color: var(--secondary-color); margin-bottom: 15px;"></i>
                <h4>Student Reviews</h4>
                <p style="color: var(--gray); font-size: 0.95rem;">Read comments and feedback from fellow students before placing your order.</p>
            </div>
            <div style="flex: 1; min-width: 300px; text-align: center; padding: 20px;">
                <i class="fa-solid fa-circle-check" style="font-size: 4rem; color: #2ecc71; margin-bottom: 15px;"></i>
                <h4>Verified Sellers</h4>
                <p style="color: var(--gray); font-size: 0.95rem;">Every meal card is linked to a verified campus cafeteria provider.</p>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
