<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Children's Games Website</title>
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
        h1 {
            color: #f9a825;
            text-shadow: 2px 2px 0 #ffffff; /* Slight white shadow for better readability */
        }
        a.btn {
            margin: 5px;
            padding: 10px 20px;
            font-size: 1.2em;
        }
        footer {
            background-color: #f9a825;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .heart {
    color: #FF0000;
}

    </style>
</head>
<body>



<div class="container text-center">
    <h1>Welcome to the Children's Games Website!</h1>
    <a href="public/user/login.php" class="btn btn-primary">Login</a>
    <a href="public/user/register.php" class="btn btn-warning">Register</a>
</div>

<footer>
    <p>© 2024 Made by &hearts; Manik, Seemant, Rohan, & Akram.</p>
</footer>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
