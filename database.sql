-- Kissan Agro Foods Database

-- Drop existing database if it exists
DROP DATABASE IF EXISTS kissan_agro_foods;

-- Create database
CREATE DATABASE kissan_agro_foods;

-- Use the database
USE kissan_agro_foods;

-- Create users table for admin access
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'manager', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email, full_name, role)
VALUES ('admin', '$2y$10$CInV9Ad.MEh4FrTnWxPJUOFzXrEcB7kPQHvhBLYGpCGWYP78GVJky', 'admin@kissanagrofoods.com', 'Administrator', 'admin');

-- Create categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, description)
VALUES
('Wheat Flour Products', 'Products from our wheat flour mill'),
('Puffed Rice Products', 'Products from our puffed rice mill');

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Insert sample products
INSERT INTO products (category_id, name, description, price, stock, is_featured)
VALUES
(1, 'Premium Wheat Flour', 'High-quality wheat flour for all your baking needs', 45.00, 100, 1),
(1, 'Whole Wheat Atta', 'Traditional whole wheat flour for chapatis and rotis', 40.00, 150, 1),
(1, 'Semolina (Suji)', 'Fine semolina for desserts and snacks', 35.00, 80, 0),
(2, 'Plain Puffed Rice', 'Light and crispy puffed rice', 30.00, 200, 1),
(2, 'Masala Puffed Rice', 'Spicy flavored puffed rice', 40.00, 120, 1);

-- Create inquiries table for contact form submissions
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create settings table for website configuration
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value)
VALUES
('site_title', 'Kissan Agro Foods'),
('site_description', 'Quality wheat flour and puffed rice products'),
('contact_email', 'info@kissanagrofoods.com'),
('contact_phone', '+977 9800000000'),
('address', 'MV37+9JJ, Pipra 45700, Khairba, Mahottari, Nepal'),
('facebook_url', 'https://facebook.com/kissanagrofoods'),
('instagram_url', 'https://instagram.com/kissanagrofoods'),
('twitter_url', 'https://twitter.com/kissanagrofoods'),
('delivery_areas', 'Mahottari, Dhanusha');

-- Create orders table (for future e-commerce functionality)
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
