<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if they don't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS menu_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL,
    status ENUM('pending', 'preparing', 'completed', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL
)";

$conn->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED NOT NULL,
    menu_item_id INT(6) UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    special_requests TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
)";

$conn->query($sql);

// Add default admin if not exists
$result = $conn->query("SELECT id FROM users WHERE username = 'admin'");
if ($result->num_rows == 0) {
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password, role) VALUES ('admin', '$password', 'admin')");
}

session_start();
?>