<?php 
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js" defer></script>
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Cafe Management System</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="place_order.php" class="active">Place Order</a></li>
                    <li><a href="view_orders.php">View Orders</a></li>
                    <li><a href="../menu/view_menu.php">Menu</a></li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="../staff/view_staff.php">Staff</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h2>Place New Order</h2>
            
            <form id="order-form" action="process_order.php" method="post">
                <div class="form-group">
                    <label for="table_number">Table Number</label>
                    <input type="number" id="table_number" name="table_number" min="1" required>
                </div>
                
                <div class="menu-categories">
                    <?php
                    $categories = $conn->query("SELECT DISTINCT category FROM menu_items WHERE available = TRUE");
                    while ($category = $categories->fetch_assoc()) {
                        echo "<div class='category-section'>
                            <h3>{$category['category']}</h3>
                            <div class='menu-items'>";
                        
                        $items = $conn->query("SELECT * FROM menu_items WHERE category = '{$category['category']}' AND available = TRUE");
                        while ($item = $items->fetch_assoc()) {
                            echo "<div class='menu-item'>
                                <input type='checkbox' id='item_{$item['id']}' name='items[]' value='{$item['id']}'>
                                <label for='item_{$item['id']}'>
                                    <span class='item-name'>{$item['name']}</span>
                                    <span class='item-price'>$" . number_format($item['price'], 2) . "</span>
                                </label>
                                <input type='number' name='quantity[{$item['id']}]' min='1' value='1' class='item-quantity'>
                                <textarea name='special_requests[{$item['id']}]' placeholder='Special requests'></textarea>
                            </div>";
                        }
                        
                        echo "</div></div>";
                    }
                    ?>
                </div>
                
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <div id="summary-items"></div>
                    <div class="total">
                        <strong>Total: $<span id="total-amount">0.00</span></strong>
                    </div>
                </div>
                
                <button type="submit" class="submit-order">Place Order</button>
            </form>
        </main>
    </div>
</body>
</html>