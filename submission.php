<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "mysql"; // Replace with your database password
$dbname = "checkout";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        die("Cart is empty.");
    }

    foreach ($_SESSION['cart'] as $item) {
        $sql = "INSERT INTO onlineorder (user_id, menu_name, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("isid", $user_id, $item['name'], $item['quantity'], $item['price']);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
    }
    $_SESSION['cart'] = array(); // Clear the cart
    header('Location: history.php');
    exit();
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
