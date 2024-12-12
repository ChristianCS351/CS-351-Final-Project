<?php
session_start();
$user_id = 1; // Replace with the actual user ID from your authentication system

$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "taco_paraiso";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_SESSION['cart'] as $item) {
        $sql = "INSERT INTO orders (user_id, item_name, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isid", $user_id, $item['name'], $item['quantity'], $item['price']);
        $stmt->execute();
    }
    $_SESSION['cart'] = array(); // Clear the cart
    header('Location: history.php');
    exit();
}

$stmt->close();
$conn->close();
?>