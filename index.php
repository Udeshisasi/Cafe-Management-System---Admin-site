<?php 
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Management - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Cafe Management System</h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">Dashboard</a></li>
                    <li><a href="orders/place_order.php">Place Order</a></li>
                    <li><a href="orders/view_orders.php">View Orders</a></li>
                    <li><a href="menu/view_menu.php">Menu</a></li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="staff/view_staff.php">Staff</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Today's Orders</h3>
                    <p>
                        <?php 
                        $today = date('Y-m-d');
                        $result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = '$today'");
                        echo $result->fetch_assoc()['count'];
                        ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>Pending Orders</h3>
                    <p>
                        <?php 
                        $result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
                        echo $result->fetch_assoc()['count'];
                        ?>
                    </p>
                </div>
                <div class="stat-card">
                    <h3>Total Menu Items</h3>
                    <p>
                        <?php 
                        $result = $conn->query("SELECT COUNT(*) as count FROM menu_items");
                        echo $result->fetch_assoc()['count'];
                        ?>
                    </p>
                </div>
            </div>

            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Table No.</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['table_number']}</td>
                                <td>$" . number_format($row['total_amount'], 2) . "</td>
                                <td><span class='status {$row['status']}'>{$row['status']}</span></td>
                                <td>" . date('H:i', strtotime($row['created_at'])) . "</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>