<?php
include '../config.php';

// Only admin can view staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle staff deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Prevent deleting own account
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $id");
        $_SESSION['success'] = "Staff member deleted successfully!";
    } else {
        $_SESSION['error'] = "You cannot delete your own account!";
    }
    
    header("Location: view_staff.php");
    exit();
}

$staff = $conn->query("SELECT * FROM users ORDER BY role, username");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Staff - Cafe Management</title>
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
            <div class="header-actions">
                <h2>Staff Members</h2>
                <a href="add_staff.php" class="button">Add New Staff</a>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($member = $staff->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $member['id']; ?></td>
                            <td><?php echo htmlspecialchars($member['username']); ?></td>
                            <td><span class="role <?php echo $member['role']; ?>"><?php echo ucfirst($member['role']); ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($member['created_at'])); ?></td>
                            <td class="actions">
                                <a href="edit_staff.php?id=<?php echo $member['id']; ?>" class="button edit">Edit</a>
                                <?php if ($member['id'] != $_SESSION['user_id']): ?>
                                    <a href="view_staff.php?delete=<?php echo $member['id']; ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>