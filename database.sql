CREATE DATABASE food_ordering;
USE food_ordering;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (food_id) REFERENCES foods(id)
);

INSERT INTO foods (name, description, price, image, category)
VALUES
('Veg Burger', 'Fresh vegetable burger', 99, 'images/veg burger.jpg', 'Fast Food'),
('Pizza', 'Cheese and vegetable pizza', 199, 'images/pizza.jpg', 'Pizza'),
('Dosa', 'Crispy masala dosa', 80, 'images/dosa.jpg', 'South Indian'),
('North Meal', 'North Meal', 180, 'images/meal.avif', 'Rice'),
('French Fries', 'Crispy potato fries', 79, 'images/fries.jpg', 'Snacks'),
('Sandwich', 'Fresh vegetable sandwich', 89, 'images/sandwich.jpg', 'Snacks'),
('Gobi Manchuri', 'Gobi Manchuri', 110, 'images/gobi.jpg', 'Snacks'),
('Ice Cream', 'Fresh Ice Cream', 129, 'images/icecream.avif', 'desert'),
('Mysore Pak', 'Fresh sweets', 89, 'images/mysorepak.jpg', 'desert'),
('pani puri', 'spicy charts', 89, 'images/panipuri.jpg', 'charts');