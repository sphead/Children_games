<?php
require_once __DIR__ . '/../../config.php'; 
require_once __DIR__ . '/../../session_management.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['level'])) {
    // If the user is not logged in or the level is not set, redirect to the start or login page.
    header('Location: /game/start.php');
    exit;
}

$level = $_SESSION['level'];
$lives = $_SESSION['lives'];
$feedback = $_SESSION['feedback'] ?? ''; // Feedback from the previous attempt, if any.

// Function to generate game content and instructions based on the level.
function generateGameContent($level, &$instructions) {
    $content = [];
    $sortedContent = [];
    switch ($level) {
        case 1:
            $instructions = "Order the following letters in ascending order:";
            $letters = range('a', 'z');
            shuffle($letters);
            $content = array_slice($letters, 0, 6);
            $sortedContent = $content;
            sort($sortedContent);
            break;
        case 2:
            $instructions = "Order the following letters in descending order:";
            $letters = range('a', 'z');
            shuffle($letters);
            $content = array_slice($letters, 0, 6);
            $sortedContent = $content;
            rsort($sortedContent);
            break;
        case 3:
            $instructions = "Order the following numbers in ascending order:";
            $numbers = range(1, 100);
            shuffle($numbers);
            $content = array_slice($numbers, 0, 6);
            $sortedContent = $content;
            sort($sortedContent);
            break;
        case 4:
            $instructions = "Order the following numbers in descending order:";
            $numbers = range(1, 100);
            shuffle($numbers);
            $content = array_slice($numbers, 0, 6);
            $sortedContent = $content;
            rsort($sortedContent);
            break;
        case 5:
            $instructions = "Identify the first (smallest) and last (largest) letter in the set:";
            $letters = range('a', 'z');
            shuffle($letters);
            $content = array_slice($letters, 0, 6);
            $sortedContent = [min($content), max($content)];
            break;
        case 6:
            $instructions = "Identify the smallest and the largest number in the set:";
            $numbers = range(1, 100);
            shuffle($numbers);
            $content = array_slice($numbers, 0, 6);
            sort($content); // Ensure the content is sorted before picking smallest and largest
            // $_SESSION['correctAnswers'] = [intval($content[0]), intval($content[5])]; // Store first and last after sorting
            $sortedContent = [min($content), max($content)];
            break;

    }
    $_SESSION['correctAnswers'] = $sortedContent; // Store sorted correct answers for validation
    return $content; // Return unsorted content for display
}



$instructions = ""; // Variable to hold instructions
$content = generateGameContent($level, $instructions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level <?= htmlspecialchars($level) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('/children_games/assets/images/background1.webp') no-repeat center center fixed;
            background-size: cover; /* Cover the entire viewport */
            font-family: 'Fredoka One', cursive; /* Child-friendly font */
        }
        .card-header {
            background-color: #f9a825; /* Changed from primary blue to cheerful yellow */
            color: #ffffff; /* White text */
        }
        .btn-primary {
            background-color: #f9a825; /* Bright yellow */
            border: none; /* No border */
        }
        .btn-primary:hover {
            background-color: #ffb74d; /* Lighter yellow on hover */
        }
        footer {
            background-color: #f9a825; /* Footer with consistent color */
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .container {
            padding-top: 60px; /* Space for fixed top items */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Level <?= htmlspecialchars($level) ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">Lives: <?= htmlspecialchars($lives) ?></p>
                        <?php if (!empty($feedback)) : ?>
                            <div class="alert alert-warning" role="alert">
                                <?= htmlspecialchars($feedback) ?>
                            </div>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($instructions) ?></p>
                        <p><strong>Items:</strong> <?= implode(", ", $content); ?></p>
                        <?php if ($level == 5 || $level == 6): ?>
                            <!-- Form for Levels 5 and 6 -->
                            <form action="process.php" method="post">
                                <div class="form-group">
                                    <label for="smallest">Smallest Item:</label>
                                    <input type="text" name="answers[]" id="smallest" required placeholder="Enter smallest item">
                                </div>
                                <div class="form-group">
                                    <label for="largest">Largest Item:</label>
                                    <input type="text" name="answers[]" id="largest" required placeholder="Enter largest item">
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Answer</button>
                            </form>
                        <?php else: ?>
                            <!-- Generic form for other levels -->
                            <form action="process.php" method="post">
                                <?php foreach ($content as $item) : ?>
                                    <div class="form-group">
                                        <input type="text" name="answers[]" value="" placeholder="<?= htmlspecialchars($item) ?>" required />
                                    </div>
                                <?php endforeach; ?>
                                <button type="submit" class="btn btn-primary">Submit Answer</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div style="position: absolute; top: 20px; right: 20px;">
            <a href="/children_games/public/welcome.php" class="btn btn-warning">Home</a> <!-- Link to home page -->
            <a href="/children_games/public/logout.php?logout=true" class="btn btn-danger">Log Out</a> <!-- Logout link -->
        </div>
    </div>
</body>
</html>
