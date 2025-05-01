<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Management - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Cafe Management System</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>

        <?php
        if (isset($_POST['login'])) {
            $username = $conn->real_escape_string($_POST['username']);
            $password = $_POST['password'];
            
            $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<p class='error'>Invalid password</p>";
                }
            } else {
                echo "<p class='error'>User not found</p>";
            }
        }
        ?>
    </div>
</body>
</html>