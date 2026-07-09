-- =========================================================
-- Verbena & Co. — database schema
-- Import this file first (phpMyAdmin > Import, or via CLI:
--   mysql -u root -p < schema.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS verbena_salon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE verbena_salon;

-- Admin accounts are hardcoded in backend/admin/login.php, no admins table needed.

-- Customer accounts managed by the admin
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  address TEXT NULL,
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services offered by the salon
CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(50) NOT NULL,
  name VARCHAR(120) NOT NULL,
  price DECIMAL(10,2) NOT NULL
);

INSERT INTO services (category, name, price) VALUES
('Hair','Signature cut & finish', 900),
('Hair','Global colour', 2400),
('Hair','Keratin smoothening', 4500),
('Hair','Scalp ritual & oil massage', 1100),
('Skin','Botanical facial', 1600),
('Skin','Brightening peel', 2100),
('Skin','Microdermabrasion', 2800),
('Skin','Threading & brow shape', 250),
('Makeup','Everyday makeup', 1200),
('Makeup','Party & event makeup', 2500),
('Makeup','Bridal trial', 3000),
('Makeup','Full bridal package', 12000),
('Spa','Deep tissue massage, 60 min', 2200),
('Spa','Aromatherapy ritual', 2000),
('Spa','Body polish & wrap', 2600),
('Spa','Foot reflexology, 30 min', 900);

-- Products sold in the shop
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  stock INT DEFAULT 100
);

INSERT INTO products (name, price, image, description) VALUES
('Botanical Shampoo', 650, 'images/products/shampoo.png', 'Sulphate-free daily shampoo'),
('Repair Hair Serum', 890, 'images/products/hair-serum.png', 'Leave-in repair serum'),
('Gentle Face Wash', 450, 'images/products/face-wash.jpg', 'Everyday botanical cleanser'),
('Daily Moisturizer', 720, 'images/products/moisturizer.png', 'Lightweight daily hydration'),
('Signature Lipstick', 550, 'images/products/lipstick.png', 'House-blend matte lipstick'),
('Second-Skin Foundation', 980, 'images/products/foundation.png', 'Buildable everyday foundation');

-- Appointment bookings submitted by users
CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(150),
  service_category VARCHAR(50) NOT NULL,
  appointment_date DATE NOT NULL,
  notes TEXT,
  status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Newsletter subscribers
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) UNIQUE NOT NULL,
  subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product orders placed by users
CREATE TABLE IF NOT EXISTS product_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(120) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT DEFAULT 1,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','completed','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);
