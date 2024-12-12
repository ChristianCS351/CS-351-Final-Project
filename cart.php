<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item = array(
        'name' => $_POST['name'],
        'quantity' => $_POST['quantity'],
        'price' => $_POST['price']
    );
    $_SESSION['cart'][] = $item;
    header('Location: menu.html'); 
    exit();
}
?>
```


<form action="add_to_cart.php" method="post">
    <input type="hidden" name="name" value="Guacamole Tropical">
    <input type="hidden" name="price" value="7.99">
    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" value="1" min="1">
    <input type="submit" value="Add to Cart">
</form>
```

<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart- Taco Paraiso</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Checkout</h1>
    <table>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
        </tr>
        <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            echo "<tr>";
            echo "<td>{$item['name']}</td>";
            echo "<td>{$item['quantity']}</td>";
            echo "<td>\${$item['price']}</td>";
            echo "</tr>";
            $total += $item['price'] * $item['quantity'];
        }
        ?>
        <tr>
            <td colspan="2">Total</td>
            <td><?php echo "\$" . $total; ?></td>
        </tr>
    </table>
    <form action="submission.php" method="post">
        <input type="submit" value="Submit Order">
    </form>
</body>
</html>
```


```php
<?php
session_start();
$user_id = 1;

$servername = "localhost";
$username = "root"; 
$password = "mysql"; 
$dbname = "checkout";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_SESSION['cart'] as $item) {
        $sql = "INSERT INTO orders (user_id, menu_name, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isid", $user_id, $item['name'], $item['quantity'], $item['price']);
        $stmt->execute();
    }
    $_SESSION['cart'] = array();
    header('Location: history.php');
    exit();
}

$stmt->close();
$conn->close();
?>
```

