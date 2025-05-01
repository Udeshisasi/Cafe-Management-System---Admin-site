<?php
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE orders SET status = '$new_status'";
    
    if ($new_status == 'completed') {
        $sql .= ", completed_at = NOW()";
    } else {
        $sql .= ", completed_at = NULL";
    }
    
    $sql .= " WHERE id = $order_id";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Order status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating order status: " . $conn->error;
    }
    
    header("Location: view_orders.php");
    exit();
}

// Get all orders with their items
$orders_query = "SELECT o.*, 
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count
                FROM orders o
                ORDER BY o.created_at DESC";
$orders = $conn->query($orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - Cafe Management System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Cafe Management System</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="place_order.php">Place Order</a></li>
                    <li><a href="view_orders.php" class="active">View Orders</a></li>
                    <li><a href="../menu/view_menu.php">Menu</a></li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="../staff/view_staff.php">Staff</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h2>Order History</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <div class="order-filters">
                <form method="get" action="">
                    <select name="status" onchange="this.form.submit()">
                        <option value="">All Orders</option>
                        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="preparing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'preparing' ? 'selected' : ''); ?>>Preparing</option>
                        <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </form>
            </div>
            
            <div class="orders-list">
                <?php if ($orders->num_rows > 0): ?>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-meta">
                                    <span class="order-id">Order #<?php echo $order['id']; ?></span>
                                    <span class="order-time"><?php echo date('M j, Y h:i A', strtotime($order['created_at'])); ?></span>
                                    <span class="order-table">Table <?php echo $order['table_number']; ?></span>
                                </div>
                                <div class="order-status">
                                    <span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                                </div>
                            </div>
                            
                            <div class="order-details">
                                <div class="order-items">
                                    <h4>Items (<?php echo $order['item_count']; ?>):</h4>
                                    <ul>
                                        <?php
                                        $items_query = "SELECT mi.name, oi.quantity, oi.special_requests, mi.price
                                                       FROM order_items oi
                                                       JOIN menu_items mi ON oi.menu_item_id = mi.id
                                                       WHERE oi.order_id = {$order['id']}";
                                        $items = $conn->query($items_query);
                                        
                                        while ($item = $items->fetch_assoc()): ?>
                                            <li>
                                                <?php echo $item['quantity'] . 'x ' . $item['name']; ?>
                                                <?php if (!empty($item['special_requests'])): ?>
                                                    <div class="special-request">
                                                        <small>Note: <?php echo $item['special_requests']; ?></small>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                                
                                <div class="order-summary">
                                    <div class="order-total">
                                        <strong>Total: $<?php echo number_format($order['total_amount'], 2); ?></strong>
                                    </div>
                                    
                                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff'): ?>
                                        <form method="post" class="status-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="preparing" <?php echo $order['status'] == 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                                                <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No orders found.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>