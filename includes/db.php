<?php
// includes/db.php

function loadEnvFile($path)
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, 'export ') === 0) {
            $line = trim(substr($line, 7));
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = array_map('trim', explode('=', $line, 2));

        if ($name === '' || preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) !== 1) {
            continue;
        }

        $first = substr($value, 0, 1);
        $last = substr($value, -1);
        if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
            $value = substr($value, 1, -1);
        } else {
            $value = preg_replace('/\s+#.*$/', '', $value);
        }

        if (getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

function envValue($key, $default = null)
{
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

function tableExists(PDO $pdo, $table)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
    ");
    $stmt->execute([$table]);

    return (int)$stmt->fetchColumn() > 0;
}

function columnExists(PDO $pdo, $table, $column)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);

    return (int)$stmt->fetchColumn() > 0;
}

function foreignKeyExists(PDO $pdo, $constraintName)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
          AND CONSTRAINT_NAME = ?
    ");
    $stmt->execute([$constraintName]);

    return (int)$stmt->fetchColumn() > 0;
}

function ensureRequiredSchema(PDO $pdo)
{
    if (!tableExists($pdo, 'users') || !tableExists($pdo, 'menu_items')) {
        return;
    }

    if (!columnExists($pdo, 'menu_items', 'seller_id')) {
        $pdo->exec("ALTER TABLE menu_items ADD COLUMN seller_id INT NULL AFTER id");
    }

    if (!foreignKeyExists($pdo, 'fk_menu_items_seller')) {
        try {
            $pdo->exec("
                ALTER TABLE menu_items
                ADD CONSTRAINT fk_menu_items_seller
                FOREIGN KEY (seller_id) REFERENCES users(id)
                ON DELETE SET NULL
            ");
        } catch (PDOException $e) {
            // The app can run without the FK as long as the seller_id column exists.
        }
    }

    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS menu_item_ratings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                menu_item_id INT NOT NULL,
                user_id INT NOT NULL,
                rating TINYINT UNSIGNED NOT NULL,
                comment TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_menu_item_user_rating (menu_item_id, user_id),
                FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (PDOException $e) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS menu_item_ratings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                menu_item_id INT NOT NULL,
                user_id INT NOT NULL,
                rating TINYINT UNSIGNED NOT NULL,
                comment TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_menu_item_user_rating (menu_item_id, user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

loadEnvFile(dirname(__DIR__) . '/.env');

$host = envValue('DB_HOST');
$dbname = envValue('DB_NAME');
$username = envValue('DB_USER');
$password = envValue('DB_PASS');

$missing = [];
foreach (['DB_HOST' => $host, 'DB_NAME' => $dbname, 'DB_USER' => $username] as $key => $value) {
    if ($value === null || $value === false || $value === '') {
        $missing[] = $key;
    }
}
if ($password === null || $password === false) {
    $missing[] = 'DB_PASS';
}

if ($missing) {
    die('Database configuration missing: ' . implode(', ', $missing));
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    ensureRequiredSchema($pdo);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
