<?php 
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $available = isset($_POST['available']) ? 1 : 0;
    
    $sql = "INSERT INTO menu_items (name, description, price, category, available) 
            VALUES ('$name', '$description', $price, '$category', $available)";
    
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Menu item added successfully!";
        header("Location: view_menu.php");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
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
            <h2>Add New Menu Item</h2>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="name">Item Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" min="0" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required>
                    <small>E.g., Beverages, Food, Desserts</small>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="available" name="available" checked>
                    <label for="available">Available</label>
                </div>
                
                <button type="submit">Add Item</button>
                <a href="view_menu.php" class="button cancel">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>