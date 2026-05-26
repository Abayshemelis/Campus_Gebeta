-- Favorites Table
CREATE TABLE IF NOT EXISTS user_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, menu_item_id)
);

-- Wallet Transactions Table
CREATE TABLE IF NOT EXISTS wallet_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Meal Plans Table
CREATE TABLE IF NOT EXISTS meal_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    meals_per_week INT NOT NULL,
    duration_days INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Meal Plans if not exists
INSERT INTO meal_plans (name, description, price, meals_per_week, duration_days) 
SELECT 'Silver Plan', 'Enjoy 5 lunches per week at any cafeteria.', 500.00, 5, 30
WHERE NOT EXISTS (SELECT id FROM meal_plans WHERE name = 'Silver Plan');

INSERT INTO meal_plans (name, description, price, meals_per_week, duration_days) 
SELECT 'Gold Plan', 'Enjoy 10 meals (lunch or dinner) per week.', 950.00, 10, 30
WHERE NOT EXISTS (SELECT id FROM meal_plans WHERE name = 'Gold Plan');

INSERT INTO meal_plans (name, description, price, meals_per_week, duration_days) 
SELECT 'Platinum Plan', 'Unlimited dining up to 21 meals per week.', 1800.00, 21, 30
WHERE NOT EXISTS (SELECT id FROM meal_plans WHERE name = 'Platinum Plan');

-- User Subscriptions Table
CREATE TABLE IF NOT EXISTS user_meal_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    meals_remaining INT NOT NULL,
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date DATETIME NOT NULL,
    status ENUM('active', 'expired') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES meal_plans(id) ON DELETE CASCADE
);
