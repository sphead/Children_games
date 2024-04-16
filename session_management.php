<?php
require_once __DIR__ . '/config.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to update the game status to "incomplete"
function updateGameStatusToIncomplete($registrationOrder) {
    global $pdo;
    try {
        $sql = "INSERT INTO score (scoreTime, result, livesUsed, registrationOrder) VALUES (NOW(), 'incomplete', :livesUsed, :registrationOrder)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['livesUsed' => $_SESSION['livesUsed'], 'registrationOrder' => $registrationOrder]);
    } catch (PDOException $e) {
        error_log("Error updating game status: " . $e->getMessage());
    }
}

// Logout and mark game as incomplete if timed out or user requested logout
if (isset($_GET['logout']) || (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800))) {
    if (!empty($_SESSION['game_in_progress']) && !empty($_SESSION['registrationOrder'])) {
        updateGameStatusToIncomplete($_SESSION['registrationOrder']);
    }
    session_unset();
    session_destroy();
    header("Location: /children_games/public/user/login.php");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time stamp
?>
