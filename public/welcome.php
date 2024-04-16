<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session_management.php';
// Other page-specific PHP code follows


// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /children_games/public/user/login.php");
    exit;
}



// Fetch additional user information from the database
$userInfo = []; // Initialize as empty array

try {
    // Adjust SQL based on your users table structure
    $sql = "SELECT email, first_name, last_name FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION["user_id"]]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error appropriately
    error_log("Error fetching user information: " . $e->getMessage());
    // Consider showing an error message or handling differently
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Children's Games Website</title>
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
        .action-buttons {
            display: flex;
            justify-content: space-around; /* Distribute space evenly around the buttons */
            margin-top: 20px;
        }
        a.btn {
            padding: 10px 20px; /* Uniform padding */
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
    </style>
    <script>
    document.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;

    function logout() {
        alert("You have been logged out due to inactivity.");
        window.location.href = '/children_games/public/user/logout.php?timeout=true';
    }

    function resetTimer() {
        clearTimeout(time);
        time = setTimeout(logout, 1800000);  // 1800000 milliseconds = 30 minutes
    }
    </script>
</head>
<body>

<div class="container">
    <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"] ?? 'User'); ?></b>. Welcome to our site.</h1>
    
    <!-- Display user information if fetched -->
    <?php if (!empty($userInfo)): ?>
        <p>Email: <?php echo htmlspecialchars($userInfo['email']); ?></p>
        <?php if (!empty($userInfo['first_name']) && !empty($userInfo['last_name'])): ?>
            <p>Name: <?php echo htmlspecialchars($userInfo['first_name'] . ' ' . $userInfo['last_name']); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- User Actions -->
    <div class="action-buttons">
        <a href="/children_games/public/logout.php" class="btn btn-danger">Sign Out</a>
        <a href="/children_games/public/user/game_history.php" class="btn btn-primary">Game History</a>
        <a href="/children_games/public/user/change_password.php" class="btn btn-warning">Change Password</a>
        <a href="../public/game/start.php" class="btn btn-success">Play Game</a>
    </div>

</div>

</body>
</html>
