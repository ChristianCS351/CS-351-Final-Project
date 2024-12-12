<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
} 

$servername = "localhost";
$username = "root"; 
$password = "mysql"; 
$dbname = "checkout";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT item_name, quantity, price, order_date FROM onlineorder WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order History - Taco Paraiso</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Order History</h1>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<table>";
            echo "<tr><th>Item</th><th>Quantity</th><th>Price</th><th>Date</th></tr>";
            echo "<tr>";
            echo "<td>{$row['menu_name']}</td>";
            echo "<td>{$row['quantity']}</td>";
            echo "<td>\${$row['price']}</td>";
            echo "<td>{$row['order_date']}</td>";
            echo "</tr>";
            echo "</table><br>";
        }
    } else {
        echo "<p>No orders found.</p>";
    }
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>