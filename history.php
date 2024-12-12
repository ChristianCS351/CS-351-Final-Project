<?php
session_start();
$user_id = 1; // Replace with the actual user ID from your authentication system

$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; 
$dbname = "taco_paraiso";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT item_name, quantity, price, order_date FROM orders WHERE user_id = ?";
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
            echo "<td>{$row['item_name']}</td>";
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