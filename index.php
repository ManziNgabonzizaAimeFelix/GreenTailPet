<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GreenTail Pets - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Ensure Font Awesome is included -->
    <style>
        body {
            background-image: url('back.jpeg'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            color: white; 
        }

        nav {
            display: flex;
            align-items: center; 
            justify-content: space-between; 
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7); 
        }

        nav img {
            width: 60px; 
            height: 60px;
            border-radius: 50%; 
            object-fit: cover; 
            margin-right: 20px; 
        }

        .homepage-content {
            background-color: rgba(0, 0, 0, 0.7); 
            padding: 40px; 
            border-radius: 10px; 
            text-align: center; 
            max-width: 800px;
            margin: 20px auto; 
        }

        .homepage-content h1 {
            color: #ffcc00; 
            font-family: 'Arial', sans-serif; 
            font-size: 2.5em; 
        }

        .homepage-content p {
            color: #e0e0e0; 
            font-weight: 500; 
            line-height: 1.6; 
        }

        .join-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00b894; 
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.5s ease, color 0.5s ease; 
            font-size: 1.2em; 
        }
        footer {
            text-align: center;
            padding: 20px; 
            background-color: rgba(0, 0, 0, 0.7); 
            position: relative; 
            bottom: 0; 
            width: 100%;
            color: white;
        }
    </style>
</head>
<body>
    <nav>
        <img src="logo.png" alt="GreenTail Pets Logo"> 
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['logged_in'])) { ?>
                <li><a href="logout.php"><i class="fas fa-door-open"></i> Logout</a></li>
            <?php } else { ?>
                <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
            <?php } ?>
        </ul>
    </nav>

    <div class="homepage-content">
        <h1 id="welcomeTitle">Embrace the Future of Agriculture with GreenTail Pets</h1>
        <p>Join us in revolutionizing the way farmers and sellers connect, ensuring access to top-quality agricultural products.</p>
        <p>Explore our extensive range of resources, including premium fertilizers, animal feed, and veterinary medicines, tailored for optimal farming success.</p>
        <p><a href="register.php" class="join-btn" id="joinUsBtn">Join Us Today!</a></p>
    </div>

    <script>
        const colors = ['#00b894', '#ff6347', '#00bfff', '#ffcc00', '#7d3c98', '#f39c12'];
        let currentColorIndex = 0; 
        function changeColor() {
            const joinButton = document.getElementById('joinUsBtn');
            const welcomeTitle = document.getElementById('welcomeTitle');
            joinButton.style.backgroundColor = colors[currentColorIndex];
            welcomeTitle.style.color = colors[currentColorIndex]; 
            
            currentColorIndex = (currentColorIndex + 1) % colors.length;
        }
        setInterval(changeColor, 500);
    </script>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> GreenTail Pets. All rights reserved.</p>
        <p>Your trusted partner in enhancing agriculture.</p>
    </footer>
</body>
</html>
