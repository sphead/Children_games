<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /children_games/public/user/login.php");
    exit;
}

require_once __DIR__ . '/../../config.php'; 

// Define variables and initialize with empty values
$username = $firstName = $lastName = $newPassword = $confirmPassword = '';
$username_err = $firstName_err = $lastName_err = $newPassword_err = $confirmPassword_err = '';

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate first name
    if (empty(trim($_POST["firstName"]))) {
        $firstName_err = "Please enter first name.";
    } else {
        $firstName = trim($_POST["firstName"]);
    }

    // Validate last name
    if (empty(trim($_POST["lastName"]))) {
        $lastName_err = "Please enter last name.";
    } else {
        $lastName = trim($_POST["lastName"]);
    }

    // Validate new password
    if (empty(trim($_POST["newPassword"]))) {
        $newPassword_err = "Please enter the new password.";
    } elseif (strlen(trim($_POST["newPassword"])) < 8) {
        $newPassword_err = "Password must have at least 8 characters.";
    } else {
        $newPassword = trim($_POST["newPassword"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirmPassword"]))) {
        $confirmPassword_err = "Please confirm the password.";
    } else {
        $confirmPassword = trim($_POST["confirmPassword"]);
        if (empty($newPassword_err) && ($newPassword != $confirmPassword)) {
            $confirmPassword_err = "Password did not match.";
        }
    }

    // Check if all validations pass
    if (empty($username_err) && empty($firstName_err) && empty($lastName_err) && empty($newPassword_err) && empty($confirmPassword_err)) {
        // Update password in the database
        $sql = "UPDATE authenticator SET passCode = ? WHERE registrationOrder = (SELECT registrationOrder FROM player WHERE userName = ? AND fName = ? AND lName = ?)";
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$hashed_password, $username, $firstName, $lastName])) {
            // Password updated successfully
            // Redirect to login page
            header("location: /children_games/public/user/login.php");
            exit;
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('/children_games/assets/images/background1.webp') no-repeat center center fixed;
            background-size: cover; /* Cover the entire viewport without repeating */
            font-family: 'Fredoka One', cursive; /* Fun and readable font */
        }
        .container {
            background-color: rgba(255, 255, 255, 0.95); /* Slightly transparent white */
            border-radius: 20px; /* Rounded borders for a soft look */
            margin-top: 100px; /* Provide space from the top */
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
        }
        h2 {
            color: #f9a825; /* A cheerful yellow */
            text-shadow: 1px 1px 0 #ffffff; /* Slight white shadow for better readability */
        }
        .btn-primary {
            background-color: #ff4081;
            border-color: #ff4081;
        }
        .btn-primary:hover {
            background-color: #e93766;
            border-color: #e93766;
        }
        label {
            color: #333; /* Dark text for better readability against the light background */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName" class="form-control <?php echo (!empty($firstName_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $firstName; ?>">
                <span class="invalid-feedback"><?php echo $firstName_err; ?></span>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName" class="form-control <?php echo (!empty($lastName_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lastName; ?>">
                <span class="invalid-feedback"><?php echo $lastName_err; ?></span>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="newPassword" class="form-control <?php echo (!empty($newPassword_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $newPassword_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirmPassword" class="form-control <?php echo (!empty($confirmPassword_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirmPassword_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
</body>
</html>
