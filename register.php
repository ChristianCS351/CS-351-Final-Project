<?php
// register.php - Registration page
session_start();
require_once 'config.php';
require_once 'auth.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($password !== $confirm_password) {
            $error_message = 'Your password does not seem to match.';
        } else if (strlen($password) < 6) {
            $error_message = 'Your password must be at least 8 characters.';
        } else {
            if (register_user($pdo, $username, $password)) {
                $success_message = 'Your registration was successful! You can now login to begin unbanning books.';
            } else {
                $error_message = 'This username already exists.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page - Taco Paraíso</title>
    <link rel="stylesheet" href="styles3.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
    <body style="background-color:  #f19748;">
        <div class="auth-container", style="width: 100%;">
            <div class="register-page", style="width: 100%;">
                <h2 style="color: rgb(65, 130, 74); font-family: 'Broadway'; font-size: 60px; text-align: center;">*Registration Page*</h1>
                    <br><br><hr style=" border-radius: 2px; border: 3px dotted rgb(145, 123, 104);"><br></br>
                <main>
                   <?php if ($error_message): ?>
                       <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
                   <?php endif; ?>
                   <?php if ($success_message): ?>
                       <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
                   <?php endif; ?>
    
                   <form method="POST" action="" class="auth-form">
                     <div>
                          <label for="username">Username:</label><br>
                          <input type="text" id="username" name="username" required style="margin-left: 300px; width: 450px; text-align: center;">
                     </div>
                     <div>
                          <label for="password">Password:</label><br>
                          <input type="password" id="password" name="password" required style="margin-left: 300px; width: 450px; text-align: center;">
                    </div>
                    <div>
                          <label for="confirm_password">Confirm Password:</label><br>
                          <input type="password" id="confirm_password" name="confirm_password" required  style="margin-left: 300px; width: 450px; text-align: center;">
                        </div><br><br>
                        <form method="POST" action="" class="auth-form">
                            <button type="submit" name="register" style="margin-left: 300px;">Register</button>
                        </form><br><br><br>
                    <p style="font-size: 23px; color: rgb(89, 168, 113); font-family: 'Cooper'
                    ; word-spacing: 3px; font-weight: bold;">Already have a paradise account with us? | <a href="login.php",>Please Login Here Then!</a></p>
                </main>
            </div>
        </div>
    <br><br><br>
</body>
</html>