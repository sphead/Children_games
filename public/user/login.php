<?php
require_once __DIR__ . '/../../config.php'; 
require_once __DIR__ . '/../../session_management.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Update SQL to join the player and authenticator tables
    $sql = 'SELECT player.userName, authenticator.passCode, player.registrationOrder 
            FROM player 
            JOIN authenticator ON player.registrationOrder = authenticator.registrationOrder 
            WHERE player.userName = ?';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['passCode'])) {
        // Password is correct, so start a new session
        session_regenerate_id();
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['registrationOrder'];
        $_SESSION['username'] = $user['userName']; // Store username
        
        // Redirect user to welcome page
        header("location: ../welcome.php");
        exit;
    } else {
        // Display an error message if password is not valid
        $login_err = "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Children's Games Website</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('/children_games/assets/images/background1.webp') no-repeat center center fixed;
            background-size: cover; /* Cover the entire viewport without repeating */
            font-family: 'Fredoka One', cursive; /* Fun and readable font */
        }
        .navbar {
            background-color: #f9a825; /* A bright and cheerful yellow */
            padding: 10px 0;
        }
        .navbar-brand {
            color: #fff !important;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent white */
            border-radius: 20px; /* Rounded borders for a soft look */
            margin-top: 100px; /* Provide space from the top */
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); /* Soft shadow for depth */
        }
        h2 {
            color: #f9a825;
            text-shadow: 2px 2px 0 #ffffff; /* Slight white shadow for better readability */
        }
        .form-control, .btn {
            border-radius: 20px; /* Rounded borders for input fields and buttons */
        }
        .btn-primary {
            background-color: #f9a825;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); /* Soft shadow for button */
        }
        .btn-primary:hover {
            background-color: #ffb74d; /* Lighter yellow on hover */
        }
        footer {
            background-color: #f9a825;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Login</h2>
    <?php 
    if(!empty($login_err)){
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
    }  
    ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-success">Register</a>
        
    </form>
</div>

<footer>
    <p>© 2024 Children's Games, Inc. All rights reserved.</p>
</footer>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
