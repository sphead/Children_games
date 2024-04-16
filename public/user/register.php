<?php
require_once __DIR__ . '/../../config.php'; 
session_start();

$message = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fName = trim($_POST['fName']);
    $lName = trim($_POST['lName']);
    $userName = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate non-empty fields
    if (empty($fName) || empty($lName) || empty($userName) || empty($password) || empty($confirmPassword)) {
        $errors[] = 'All fields are required.';
    }

    // Validate that first name, last name, and username start with a letter and only contain letters
    if (!preg_match('/^[a-zA-Z]+$/', $fName) || !preg_match('/^[a-zA-Z]+$/', $lName)) {
        $errors[] = 'First and Last names must only contain letters and begin with a letter.';
    }
    
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]{7,}$/', $userName)) {
        $errors[] = 'Username must begin with a letter and be at least 8 characters long.';
    }

    // Validate password length and match
    if (strlen($password) < 8) {
        $errors[] = 'Password must contain at least 8 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    // Check for unique username in the database
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM player WHERE userName = ?");
    $stmt->execute([$userName]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = 'Username is already taken.';
    }

    // Proceed if no errors
    if (empty($errors)) {
        $passCode = password_hash($password, PASSWORD_DEFAULT);
        $registrationTime = date('Y-m-d H:i:s'); // Current time in SQL datetime format

        try {
            $pdo->beginTransaction();

            // Insert into player table
            $sqlPlayer = 'INSERT INTO player (fName, lName, userName, registrationTime) VALUES (?, ?, ?, ?)';
            $stmtPlayer = $pdo->prepare($sqlPlayer);
            $stmtPlayer->execute([$fName, $lName, $userName, $registrationTime]);

            // Get the registrationOrder that was just created
            $registrationOrder = $pdo->lastInsertId();

            // Insert into authenticator table
            $sqlAuth = 'INSERT INTO authenticator (passCode, registrationOrder) VALUES (?, ?)';
            $stmtAuth = $pdo->prepare($sqlAuth);
            $stmtAuth->execute([$passCode, $registrationOrder]);

            $pdo->commit();
            $message = 'User registered successfully!';
            header("Location: login.php"); // Redirect to a welcome page after successful registration
            exit;
        } catch(PDOException $e) {
            $pdo->rollBack();
            $errors[] = 'Error registering user: ' . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Children's Games Website</title>
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
        .form-group label {
            color: #666; /* Darker font color for labels */
        }
        .form-control, .btn-primary {
            border-radius: 20px; /* Rounded borders for input fields and button */
        }
        .btn-primary {
            background-color: #f9a825;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); /* Soft shadow for button */
        }
        .btn-primary:hover {
            background-color: #ffb74d; /* Lighter yellow on hover */
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#username").keyup(function(){
                var username = $(this).val().trim();
                if(username != ''){
                    $.ajax({
                        url: 'check_username.php',
                        type: 'post',
                        data: {username: username},
                        success: function(response){
                            $('#username_status').html(response);
                        }
                    });
                }else{
                    $("#username_status").html("");
                }
            });
        });
    </script>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <?php 
    if (!empty($message)): 
        echo '<div class="alert alert-info">' . $message . '</div>';
    endif;
    if (!empty($errors)): 
        echo '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>';
    endif;
    ?>
    <form action="register.php" method="POST">
        <div id="username_status"></div>
        <div class="form-group">
            <label for="fName">First Name:</label>
            <input type="text" name="fName" id="fName" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="lName">Last Name:</label>
            <input type="text" name="lName" id="lName" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-success">Login</a>
    </form>
</div>
</body>
</html>
