<?php
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table_number = $conn->real_escape_string($_POST['table_number']);
    $items = $_POST['items'];
    $quantities = $_POST['quantity'];
    $special_requests = isset($_POST['special_requests']) ? $_POST['special_requests'] : [];
    
    // Calculate total amount
    $total_amount = 0;
    foreach ($items as $item_id) {
        $item_result = $conn->query("SELECT price FROM menu_items WHERE id = $item_id");
        $item = $item_result->fetch_assoc();
        $total_amount += $item['price'] * $quantities[$item_id];
    }
    
    // Insert order
    $conn->query("INSERT INTO orders (table_number, total_amount) VALUES ($table_number, $total_amount)");
    $order_id = $conn->insert_id;
    
    // Insert order items
    foreach ($items as $item_id) {
        $quantity = $quantities[$item_id];
        $request = isset($special_requests[$item_id]) ? $conn->real_escape_string($special_requests[$item_id]) : '';
        
        $conn->query("INSERT INTO order_items (order_id, menu_item_id, quantity, special_requests) 
                     VALUES ($order_id, $item_id, $quantity, '$request')");
    }
    
    header("Location: view_orders.php?success=Order placed successfully");
    exit();
} else {
    header("Location: place_order.php");
    exit();
}
?>