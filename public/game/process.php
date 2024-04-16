<?php
require_once __DIR__ . "/../../config.php";
require_once __DIR__ . '/../../session_management.php';


if (!isset($_SESSION["user_id"], $_SESSION["level"], $_SESSION["lives"])) {
    // Redirect user if essential session variables are missing
    header("Location: /game/start.php");
    exit();
}

// Function to check if the answer is correct
function isAnswerCorrect($level, $userAnswers) {
    $correctAnswers = $_SESSION['correctAnswers']; // Retrieve the correct answers stored in the session
    error_log("User Answers before processing: " . json_encode($userAnswers));


    // Normalize user answers by trimming whitespace
    $userAnswers = array_map('trim', $userAnswers);

    // Handle alphabetic and numeric levels differently
    if ($level == 1 || $level == 2) {
        // Levels 1 and 2 are alphabetic and may require case-insensitive comparison
        $userAnswers = array_map('strtolower', $userAnswers);
        // Direct comparison for levels that require sorted order replication
        return $userAnswers === $correctAnswers;
    } elseif ($level == 3 || $level == 4) {
        // Levels 3 and 4 are numeric
        $userAnswers = array_map('intval', $userAnswers); // Convert user answers to integers for numeric levels
        return $userAnswers === $correctAnswers;
    } elseif ($level == 5 ) {
        // Levels 5 and 6 involve picking specific items (e.g., smallest and largest)
        return count($userAnswers) === 2 && $userAnswers === $correctAnswers;
    } elseif ($level == 6) {
        $userAnswers = array_map('intval', $userAnswers);
        return count($userAnswers) === 2 &&
                     $userAnswers[0] === $_SESSION['correctAnswers'][0] &&
                     $userAnswers[1] === $_SESSION['correctAnswers'][1];
    }
    return false; // If none of the conditions match, return false
}

function updateGameCompletion($pdo, $userId, $livesUsed) {
    $sql = "INSERT INTO score (scoreTime, result, livesUsed, registrationOrder) VALUES (NOW(), 'win', :livesUsed, :userId)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':livesUsed' => $livesUsed,
            ':userId' => $userId
        ]);
        error_log("Game completion data recorded successfully.");
    } catch (PDOException $e) {
        error_log("Database error on game completion: " . $e->getMessage());
        // Uncomment below to output error directly on the script for debugging (not recommended for production)
         echo "Database error: " . $e->getMessage();
        // exit;
    }
}


// Collecting user answers and checking them
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answers'])) {
    $userAnswers = $_POST['answers'];

    if (isAnswerCorrect($_SESSION["level"], $userAnswers)) {
        if ($_SESSION["level"] == 6) {
            $_SESSION["feedback"] = "Congratulations, you've completed the game!";
            updateGameCompletion($pdo, $_SESSION['user_id'], $_SESSION['lives']);
            $_SESSION["level"] = 1; // Optionally reset or redirect to a completion page
            $_SESSION["lives"] = 5; // Reset lives
        } else {
            $_SESSION["level"]++;
            $_SESSION["feedback"] = "Correct! Moving to the next level.";
        }
    } else {
        $_SESSION["lives"]--;
        if ($_SESSION["lives"] <= 0) {
            $_SESSION["feedback"] = "Game Over. You've run out of lives.";
            $_SESSION["level"] = 1; // Reset level
            $_SESSION["lives"] = 5; // Reset lives
        } else {
            $_SESSION["feedback"] = "Incorrect. Please try again.";
        }
    }

    header("Location: level.php"); // Redirect back to the level page to show feedback
    exit();
} else {
    header("Location: /game/start.php");
    exit();
}
?>