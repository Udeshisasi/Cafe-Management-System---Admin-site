<?php
include '../config.php';

// Only admin can edit staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_staff.php");
    exit();
}

$id = intval($_GET['id']);
$staff = $conn->query("SELECT * FROM users WHERE id = $id")->fetch_assoc();

if (!$staff) {
    header("Location: view_staff.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    
    // Check if username is being changed to one that already exists
    if ($username != $staff['username']) {
        $check = $conn->query("SELECT id FROM users WHERE username = '$username' AND id != $id");
        
        if ($check->num_rows > 0) {
            $error = "Username already exists!";
        }
    }
    
    if (!isset($error)) {
        $sql = "UPDATE users SET username = '$username', role = '$role'";
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql .= ", password = '$password'";
        }
        
        $sql .= " WHERE id = $id";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Staff member updated successfully!";
            header("Location: view_staff.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff - Cafe Management</title>
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
                    <li><a href="../menu/view_menu.php">Menu</a></li>
                    <li><a href="view_staff.php" class="active">Staff</a></li>
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h2>Edit Staff Member</h2>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($staff['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="staff" <?php echo $staff['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="admin" <?php echo $staff['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                
                <button type="submit">Update Staff</button>
                <a href="view_staff.php" class="button cancel">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>