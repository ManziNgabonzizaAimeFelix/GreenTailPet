<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'seller') {
                header('Location: seller.php');
            } elseif ($user['role'] == 'buyer') {
                header('Location: buyer.php');
            } elseif ($user['role'] == 'manager') {
                header('Location: manager.php');
            }
            exit();
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GreenTail Pets</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
        }

        h2 {
            text-align: center;
            color: #2d3436;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #00b894;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #00b894;
        }

        .error {
            color: #e74c3c;
            background-color: #ffe6e6;
            padding: 10px;
            border: 1px solid #e74c3c;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .error a {
            color: #00b894;
            text-decoration: underline;
        }

        .error a:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <form method="POST" action="login.php">
        <h2>Login</h2>
        <label>Username:</label><input type="text" name="username" required>
        <label>Password:</label><input type="password" name="password" required>
        <button type="submit">Login</button>

        <?php if (isset($error_message)): ?>
            <div class="error">
                <?php echo $error_message; ?>
                <div>Go back to <a href="index.php">Home</a>.</div>
            </div>
        <?php endif; ?>
    </form>
</body>
</html>
