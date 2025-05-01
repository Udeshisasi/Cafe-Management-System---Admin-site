<?php 
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Handle item deletion
if (isset($_GET['delete']) ){
    if ($_SESSION['role'] == 'admin') {
        $id = intval($_GET['delete']);
        $conn->query("DELETE FROM menu_items WHERE id = $id");
        $_SESSION['success'] = "Menu item deleted successfully!";
        header("Location: view_menu.php");
        exit();
    }
}

$menu_items = $conn->query("SELECT * FROM menu_items ORDER BY category, name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Menu</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>Cafe Management System</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Dashboard</a></li>
                    <li><a href="../orders/place_order.php">Place Order</a></li>
                    <li><a href="../orders/view_orders.php">View Orders</a></li>
                    <li><a href="view_menu.php" class="active">Menu</a></li>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="../staff/view_staff.php">Staff</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="header-actions">
                <h2>Menu Items</h2>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="add_item.php" class="button">Add New Item</a>
                <?php endif; ?>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <div class="menu-categories">
                <?php
                $current_category = '';
                while ($item = $menu_items->fetch_assoc()):
                    if ($item['category'] != $current_category) {
                        $current_category = $item['category'];
                        echo "<h3>$current_category</h3>";
                    }
                ?>
                    <div class="menu-item">
                        <div class="item-details">
                            <h4><?php echo $item['name']; ?></h4>
                            <p><?php echo $item['description']; ?></p>
                            <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                            <span class="availability <?php echo $item['available'] ? 'available' : 'unavailable'; ?>">
                                <?php echo $item['available'] ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </div>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <div class="item-actions">
                                <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="button edit">Edit</a>
                                <a href="view_menu.php?delete=<?php echo $item['id']; ?>" class="button delete" 
                                   onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
                
                <?php if ($menu_items->num_rows == 0): ?>
                    <p>No menu items found. <?php if ($_SESSION['role'] == 'admin'): ?><a href="add_item.php">Add your first item</a><?php endif; ?></p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>