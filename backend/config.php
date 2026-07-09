<?php
/**
 * Database connection.
 * Update these four values to match your local MySQL setup
 * (XAMPP/WAMP default is usually host=localhost, user=root, password="").
 */
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'verbena_salon';

mysqli_report(MYSQLI_REPORT_OFF);

function createDatabaseAndTables($host, $user, $pass, $dbName) {
    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        throw new RuntimeException('Database connection failed.');
    }

    $conn->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($dbName);
    $conn->set_charset('utf8mb4');

    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(120) NOT NULL, username VARCHAR(50) UNIQUE NOT NULL, email VARCHAR(150) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL, address TEXT NULL, status ENUM('active','inactive') DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
        'services' => "CREATE TABLE IF NOT EXISTS services (id INT AUTO_INCREMENT PRIMARY KEY, category VARCHAR(50) NOT NULL, name VARCHAR(120) NOT NULL, price DECIMAL(10,2) NOT NULL)",
        'products' => "CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, price DECIMAL(10,2) NOT NULL, image VARCHAR(255) NOT NULL, description VARCHAR(255), stock INT DEFAULT 100)",
        'appointments' => "CREATE TABLE IF NOT EXISTS appointments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NULL, name VARCHAR(120) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(150), service_category VARCHAR(50) NOT NULL, appointment_date DATE NOT NULL, notes TEXT, status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL)",
        'newsletter_subscribers' => "CREATE TABLE IF NOT EXISTS newsletter_subscribers (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(150) UNIQUE NOT NULL, subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
        'product_orders' => "CREATE TABLE IF NOT EXISTS product_orders (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, product_id INT NOT NULL, product_name VARCHAR(120) NOT NULL, unit_price DECIMAL(10,2) NOT NULL, quantity INT DEFAULT 1, total_amount DECIMAL(10,2) NOT NULL, status ENUM('pending','completed','cancelled') DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (product_id) REFERENCES products(id))"
    ];

    foreach ($tables as $sql) {
        $conn->query($sql);
    }

    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active','inactive') DEFAULT 'active'");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS address TEXT NULL");
    $conn->query("ALTER TABLE product_orders ADD COLUMN IF NOT EXISTS status ENUM('pending','completed','cancelled') DEFAULT 'pending'");

    $productCheck = $conn->query('SELECT id FROM products LIMIT 1');
    if ($productCheck && $productCheck->num_rows === 0) {
        $conn->query("INSERT INTO products (name, price, image, description) VALUES
        ('Botanical Shampoo', 650, 'images/products/shampoo.png', 'Sulphate-free daily shampoo'),
        ('Repair Hair Serum', 890, 'images/products/hair-serum.png', 'Leave-in repair serum'),
        ('Gentle Face Wash', 450, 'images/products/face-wash.jpg', 'Everyday botanical cleanser'),
        ('Daily Moisturizer', 720, 'images/products/moisturizer.png', 'Lightweight daily hydration'),
        ('Signature Lipstick', 550, 'images/products/lipstick.png', 'House-blend matte lipstick'),
        ('Second-Skin Foundation', 980, 'images/products/foundation.png', 'Buildable everyday foundation')");
    }

    return $conn;
}

try {
    $conn = createDatabaseAndTables($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
} catch (RuntimeException $e) {
    $isApi = (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/') || 
              str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));
    
    if ($isApi) {
        http_response_code(500);
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]));
    } else {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Database Error — Verbena & Co.</title>
            <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
            <style>
                :root { --ink: #22301F; --cream: #F6F0E4; --gold: #AD8347; }
                body {
                    margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
                    background: var(--ink); font-family: 'Manrope', sans-serif; color: var(--ink); padding: 20px;
                }
                .card {
                    background: var(--cream); border-radius: 16px; padding: 40px; width: 100%; max-width: 500px;
                    box-shadow: 0 30px 60px rgba(0,0,0,.35); text-align: center;
                }
                h1 { font-family: 'Fraunces', serif; font-size: 1.8rem; margin: 0 0 16px; color: #8a2f2f; }
                p { margin: 0 0 20px; line-height: 1.6; color: rgba(34,48,31,.8); }
                .code-block {
                    background: rgba(34,48,31,.05); border-left: 4px solid var(--gold);
                    padding: 12px; font-family: monospace; font-size: 0.9rem; text-align: left;
                    margin-bottom: 24px; word-break: break-all; overflow-x: auto;
                }
                .btn {
                    display: inline-block; padding: 12px 24px; background: var(--gold);
                    color: var(--cream); font-weight: 700; text-decoration: none; border-radius: 999px;
                }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Database Connection Failed</h1>
                <p>We couldn't connect to your MySQL database. Please make sure MySQL is running in your control panel (e.g., XAMPP) and your database settings in <code>backend/config.php</code> are correct.</p>
                <div class="code-block">
                    <strong>Error:</strong> <?= htmlspecialchars($e->getMessage()) ?><br>
                    <strong>Host:</strong> <?= htmlspecialchars($DB_HOST) ?><br>
                    <strong>User:</strong> <?= htmlspecialchars($DB_USER) ?><br>
                    <strong>Database Name:</strong> <?= htmlspecialchars($DB_NAME) ?>
                </div>
                <a href="" class="btn" onclick="window.location.reload(); return false;">Retry Connection</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

$conn->set_charset('utf8mb4');
