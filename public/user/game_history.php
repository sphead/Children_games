<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../session_management.php';


// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /children_games/public/user/login.php");
    exit;
}

$gameHistory = [];

try {
    // Fetch all game history data from the database
    $sql = "SELECT scoreTime, id, fName, lName, result, livesUsed FROM history";
    $stmt = $pdo->query($sql);  // No need to prepare or execute with parameters
    $gameHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching game history: " . $e->getMessage());
    echo "Error fetching game history: " . $e->getMessage();  // Output error for debugging
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game History</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('/children_games/assets/images/background1.webp') no-repeat center center fixed;
            background-size: cover; /* Cover the entire viewport */
            font-family: 'Fredoka One', cursive; /* Fun and readable font */
        }
        .container {
            background-color: rgba(255, 255, 255, 0.95); /* Slightly transparent white */
            border-radius: 20px; /* Rounded borders for a soft look */
            margin-top: 100px; /* Provide space from the top */
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
        }
        h1, h2 {
            color: #f9a825; /* A cheerful yellow matching the navbar */
            text-shadow: 1px 1px 0 #ffffff; /* Slight white shadow for better readability */
        }
        table {
            background-color: white; /* Ensures readability of the table content */
        }
        th, td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-5">Game History</h1>
        <h2>Game History Overview</h2>
        <?php if ($gameHistory): ?>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Date and Time</th>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Outcome</th>
                        <th>Lives Used</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gameHistory as $game): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($game['scoreTime']); ?></td>
                            <td><?php echo htmlspecialchars($game['id']); ?></td>
                            <td><?php echo htmlspecialchars($game['fName']); ?></td>
                            <td><?php echo htmlspecialchars($game['lName']); ?></td>
                            <td><?php echo htmlspecialchars($game['result']); ?></td>
                            <td><?php echo htmlspecialchars($game['livesUsed']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No game history found or there was an error fetching it.</p>
        <?php endif; ?>
    </div>
</body>
</html>
