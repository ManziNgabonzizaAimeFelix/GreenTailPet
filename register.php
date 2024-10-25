<?php
include 'db.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "Registration successful! Please <a class='login-link' href='login.php'>login</a>.";
    } else {
        $errorMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - GreenTail Pets</title>
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
            position: relative; 
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
        input[type="password"],
        select {
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
            margin-bottom: 10px; 
        }

        button:hover {
            background-color: #009e7e; 
        }

        .error, .success {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px; 
            text-align: center;
        }

        .error {
            color: #e74c3c;
            background-color: #ffe6e6;
            border: 1px solid #e74c3c;
        }

        .success {
            color: #2ecc71;
            background-color: #e8f8f5;
            border: 1px solid #2ecc71;
        }

        .back-button {
            background-color: #dfe6e9; 
            color: #2d3436; 
            margin-top: 10px; 
        }

        .back-button:hover {
            background-color: #b2bec3; 
        }

        .login-link {
            color: #00b894; 
            text-decoration: underline;
        }

        .login-link:hover {
            text-decoration: none; 
        }
    </style>
</head>
<body>
    <form method="POST" action="register.php">
        <h2>Register</h2>

        <?php if ($successMessage): ?>
            <div class="success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <label>Username:</label>
        <input type="text" name="username" required>
        
        <label>Password:</label>
        <input type="password" name="password" required>
        
        <label>Role:</label>
        <select name="role">
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
            <option value="manager">Manager</option>
        </select>
        
        <button type="submit">Register</button>
        <button type="button" class="back-button" onclick="window.location.href='index.php'">Back to Home</button>
    </form>
</body>
</html>
