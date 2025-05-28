-- Create database if not exists
CREATE DATABASE IF NOT EXISTS urbanwear_db;
USE urbanwear_db;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2),
    category_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    gallery TEXT,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$WK5DIcIp9o0AU3V6MiGlq.w1WvJXhDKk9hSJBR6Vn6Wh8W0SwWRY.', 'admin@urbanwear.com');

-- Insert sample categories
INSERT INTO categories (name, description, image) VALUES
('T-Shirts', 'Comfortable and stylish t-shirts for everyday wear', 'assets/images/categories/tshirts.jpg'),
('Jeans', 'High-quality denim jeans for all occasions', 'assets/images/categories/jeans.jpg'),
('Jackets', 'Trendy jackets to keep you warm and stylish', 'assets/images/categories/jackets.jpg'),
('Shoes', 'Fashionable footwear for urban lifestyle', 'assets/images/categories/shoes.jpg'),
('Accessories', 'Complete your look with our accessories', 'assets/images/categories/accessories.jpg');

-- Insert sample products
INSERT INTO products (name, sku, description, price, sale_price, category_id, image, stock_quantity) VALUES
('Classic Black Tee', 'TS001', 'A classic black t-shirt made from premium cotton. Perfect for everyday wear.', 24.99, NULL, 1, 'assets/images/products/black-tee.jpg', 50),
('Urban Graphic Tee', 'TS002', 'Express your style with this unique graphic design t-shirt.', 29.99, 19.99, 1, 'assets/images/products/graphic-tee.jpg', 35),
('Slim Fit Jeans', 'JN001', 'Modern slim fit jeans with a comfortable stretch fabric.', 59.99, NULL, 2, 'assets/images/products/slim-jeans.jpg', 20),
('Distressed Denim', 'JN002', 'Trendy distressed jeans with a vintage look.', 64.99, 49.99, 2, 'assets/images/products/distressed-jeans.jpg', 15),
('Leather Jacket', 'JK001', 'Classic leather jacket with a modern fit and high-quality materials.', 149.99, NULL, 3, 'assets/images/products/leather-jacket.jpg', 10),
('Denim Jacket', 'JK002', 'Versatile denim jacket that never goes out of style.', 89.99, 69.99, 3, 'assets/images/products/denim-jacket.jpg', 12),
('Urban Sneakers', 'SH001', 'Comfortable and stylish sneakers for everyday urban adventures.', 79.99, NULL, 4, 'assets/images/products/urban-sneakers.jpg', 25),
('High-Top Boots', 'SH002', 'Premium high-top boots with a rugged look and durable construction.', 129.99, 99.99, 4, 'assets/images/products/high-top-boots.jpg', 8),
('Urban Backpack', 'AC001', 'Functional and trendy backpack with multiple compartments.', 49.99, NULL, 5, 'assets/images/products/urban-backpack.jpg', 18),
('Stainless Steel Watch', 'AC002', 'Elegant stainless steel watch with a minimalist design.', 99.99, 79.99, 5, 'assets/images/products/steel-watch.jpg', 15);