<?php
// login.php - Login page
session_start();
require_once 'config.php';
require_once 'auth.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (login_user($pdo, $username, $password)) {
            header('Location: index.html');
            exit;
        } else {
            $error_message = 'Sorry, your username or password is incorrect';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - Taco Paraíso</title>
    <link rel="stylesheet" href="styles3.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body style="background-color:  #f19748;">
    <a style="margin-left: 100px;"><img src="Taco Paraiso Official Logo.png", alt="Official Logo of Taco Paraiso", style="height:406px; width:400px;"></a><br><br>
    <div class="auth-container">
        <div class="login-page">
            <h2 style="color: rgb(220, 65, 57); font-family: 'Broadway'; font-size: 60px; text-align: center;">*Login Page*</h1>
                <br><br><hr style=" border-radius: 2px; border: 3px dotted rgb(145, 123, 104);"><br></br>
            <main>
               <?php if ($error_message): ?>
                   <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
               <?php endif; ?>

               <form method="POST" action="" class="auth-form">
                <div>
                    <label for="username"> Username:</label><br>
                    <input type="text" id="username" name="username" required style="margin-left: 300px; width: 450px; text-align: center;">
                </div>
                <div>
                    <label for="password"> Password:</label><br>
                    <input type="password" id="password" name="password" required style="margin-left: 300px; width: 450px; text-align: center;">
                </div><br><br>
                <button type="submit" name="login" style="margin-left: 300px;">Login</button>
                </form><br><br><br>
                <p style="font-size: 23px; color: rgb(89, 168, 142); font-family: 'Cooper'
                ; word-spacing: 3px; font-weight: bold;">No paradise account just yet? | <a href="register.php",>Register Here to Begin Your Paradise!</a></p>
            </main>
        </div>
    </div>
    <br><br><br>
</body>
</html>