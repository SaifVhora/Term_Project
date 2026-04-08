-- ============================================================
-- Online Second-Hand Marketplace Platform
-- COSC-2956 Internet Tools | Term Project
-- ============================================================

CREATE DATABASE IF NOT EXISTS marketplace_db
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;
USE marketplace_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  seller_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image_url VARCHAR(255),
  category ENUM('Electronics','Clothing','Books','Music','Collectibles','Other') NOT NULL,
  `condition` ENUM('New','Like New','Good','Fair','Poor') NOT NULL,
  stock INT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('Pending','Processing','Shipped','Completed','Cancelled') DEFAULT 'Pending',
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT DEFAULT NULL,
  quantity INT NOT NULL,
  price_at_purchase DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, is_admin) VALUES
('Admin', 'admin@marketplace.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Regular user (password: user123)
INSERT INTO users (name, email, password, is_admin) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0);

INSERT INTO products (seller_id, name, description, price, image_url, category, `condition`, stock) VALUES
(2, 'iPhone 12 64GB', 'Excellent condition, no scratches. Comes with original box.', 349.99, 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/IPhone_12_Pro.png/220px-IPhone_12_Pro.png', 'Electronics', 'Like New', 1),
(2, 'Denim Jacket - Large', 'Vintage Levis denim jacket, barely worn.', 45.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Camponotus_flavomarginatus_ant.jpg/320px-Camponotus_flavomarginatus_ant.jpg', 'Clothing', 'Good', 1),
(2, 'Clean Code - Robert Martin', 'Paperback, some highlights in first chapter.', 18.00, 'https://m.media-amazon.com/images/I/41xShlnTZTL.jpg', 'Books', 'Good', 2),
(2, 'Dark Side of the Moon - Vinyl', 'Pink Floyd original press, plays perfectly.', 120.00, 'https://upload.wikimedia.org/wikipedia/en/3/3b/Dark_Side_of_the_Moon.png', 'Music', 'Good', 1),
(1, 'Sony WH-1000XM4 Headphones', 'Noise cancelling, excellent sound. Minor wear on ear cups.', 180.00, 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/High_accuracy_settling_time_measurement_using_sockets.jpg/320px-High_accuracy_settling_time_measurement_using_sockets.jpg', 'Electronics', 'Good', 1);
