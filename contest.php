<?php

session_start();
require_once 'auth.php';

// This will check if user is logged in or not
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
} 

$host = 'localhost'; 
$dbname = 'winter-contest'; 
$user = 'root'; 
$pass = 'mysql';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}


$error_message = '';


$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT email, types, item, descriptions FROM contest WHERE types LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && isset($_POST['types']) && isset($_POST['item']) && isset($_POST['desciptions'])) {
       
        $email = htmlspecialchars($_POST['email']);
        $types = (int) htmlspecialchars($_POST['types']);
        $item = htmlspecialchars($_POST['item']);
        $desciptions = htmlspecialchars($_POST['descriptions']);

        $valid_types = ['Appetizer', 'Entree', 'Alcohol', 'Desert', 'Drink'];
        if (!in_array($types, $valid_types)) {
            $error_message = "Invalid menu type. Must be one of: Appetizer, Entree, Alcohol, Desert, or Drink.";
        }


        $check_sql = 'SELECT COUNT(*) FROM contest WHERE email = :email';
        $stmt_check = $pdo->prepare($check_sql);
        $stmt_check->execute(['email' => $email]);
        $email_count = $stmt_check->fetchColumn();
        
        if ($email_count > 0) {
            $error_message = "This email has already entered the contest.";
        }

   
        if (empty($error_message)) {
            $insert_sql = 'INSERT INTO contest (email, types, item, descriptions) VALUES (:email, :types, :item, :descriptions)';
            $stmt_insert = $pdo->prepare($insert_sql);
            $stmt_insert->execute(['email' => $email, 'types' => $types, 'item' => $item, 'descriptions' => $desciptions]);
        }
    } elseif (isset($_POST['delete_email'])) {
      
        $delete_email = htmlspecialchars($_POST['delete_email']);
        
        $delete_sql = 'DELETE FROM contest WHERE email = :email';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['email' => $delete_email]);
    }
}

$sql = 'SELECT email, types, item, descriptions FROM contest';
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=2.0">
    <title>Winter Contest Page - Taco Paraíso</title>
    <link rel="stylesheet" href="styles4.css">
    <link rel="icon" type="image/x-icon" href="favicon1.ico">
</head>
<header>
    <br>
    <a href="https://x.com/">
    <img src="twitter-app-new-logo-x-black-background_1017-45425.avif", alt="Logo of X and Link", class="image-x"></a>
    <a href="https://www.instagram.com/">
    <img src="OIP.jpg", alt="Logo of Instagram and Link", class="image-i"></a>
    <a href="https://www.facebook.com/">
    <img src="facebook.jpg", alt="Logo of Facebook and Link", class="image-f"></a>
    <img src="Taco Paraiso Official Logo.png", alt="Logo of Taco Paraíso", style="height:406px; width:400px;">
</header>

<body style="background-color: rgb(208, 255, 245);">
    <nav class="nav">
        <table class="nav-table">
            <tr>
                <th><a href="home.html">Home</a></th>
                <th><a href="about.html">About</a></th>
                <th><a href="menu.html">Menu</a></th>
                <th><a href="news.html">News</a></th>
                <th><a href="contact.html">Contact</a></th>
                <th><a href="contesting.html">Contest</a></th>
            </tr>
        </table>
    </nav>
    <br><br><br><br>
    <h5 style="margin-bottom: 25px; color: rgb(6, 38, 162);">Taco Paraiso - Winter Menu Contest</h5>
    <hr style="  border: 3px dashed rgb(119, 229, 187);"><br><br><br>
    <div class="hero-section">
        
        <div class="hero-search"><br><br>
            <h2 style="font-family: Algerian; font-style: italic; font-size: 48px; color: rgb(235, 92, 121); text-align: center;"><u>Search for Created Menu Items:</u></h2><br>
            <form action="" method="GET" class="search-form">
                <label for="search">Search For Menu Contest Type:</label>
                <input type="text" id="search" name="search" required>
                <input type="submit" value="Search"  style="width: 350px;">
            </form>
            
            <?php if (isset($_GET['search'])): ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <?php if ($search_results && count($search_results) > 0): ?>
                        <table>
                            <thead>
                                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 23px; color: rgb(53, 120, 70); margin-bottom: 21px;">
                                    <th>Your Email:</th>
                                    <th>Menu Type (Entree, Drink, Alcohol, Desert, Appetizer):</th>
                                    <th>Menu Item:</th>
                                    <th>Description of Item:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 23px; color: rgb(53, 120, 70); word-spacing: 2px; margin-bottom: 21px">
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['types']); ?></td>
                                    <td><?php echo htmlspecialchars($row['item']); ?></td>
                                    <td><?php echo htmlspecialchars($row['descriptions']); ?></td>
                                    <td>
                                        <form action="contest.php" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_email" value="<?php echo $row['email']; ?>">
                                            <input type="submit" value="Remove!" style= "width: 400px;"onclick="return confirm('Ho! Ho! Ho! Are You Sure?');">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="font-style: italic; color: rgb(255, 102, 0);"><br><br>*There are entrees with this food menu type, maybe try making this type:*</p><br><br>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-container">
        <h2>All Contest Participants!</h2>
        <hr style="border: 2px dashed rgba(120, 168, 171, 0.959); width: 32%"><br><br><br>
        <table class="half-width-left-align">
            <thead>
                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 23px; color: rgb(53, 120, 70); margin-bottom: 21px;">
                    <th>Your Email:</th>
                    <th>Menu Type (Entree, Drink, Alcohol, Desert, Appetizer):</th>
                    <th>Menu Item:</th>
                    <th>Description of Item:</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 23px; color: rgb(53, 120, 70); margin-bottom: 21px;">
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['types']); ?></td>
                    <td><?php echo htmlspecialchars($row['item']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <form action="contest.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_email" value="<?php echo $row['email']; ?>">
                            <input type="submit" value="Remove!" style= "width: 200px; margin-left: 300px;" onclick="return confirm('Ho! Ho! Ho! Are You Sure?');">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="form-container">
        <h2>Please Fill Out Your Menu Item Here!</h2>
        <hr style="border: 2px dashed rgba(120, 168, 171, 0.952); width: 49.5%"><br><br><br>
        <?php if (!empty($error_message)): ?>
            <p class="error-message" style="font-style: italic; color: darkred; font-size: 45px; text-align: center;"><?php echo $error_message; ?></p><br><br>
        <?php endif; ?>
        <form action="contest.php" method="post">
            <label for="email" style="font-size: 27px; font-family: Georgia, 'Times New Roman', Times, serif; color: rgb(46, 161, 115);">Enter Email:</label>
            <input type="text" id="email" name="email" required style="width: 840px;">
            <br><br>
            <label for="types" style="font-size: 27px; font-family: Georgia, 'Times New Roman', Times, serif; color:rgb(46, 161, 115);">Menu Type (Entree, Drink, Alcohol, Desert, Appetizer):</label>
            <input type="text" id="types" name="types" required  style="width: 840px;">
            <br><br>
            <label for="item" style="font-size: 27px; font-family: Georgia, 'Times New Roman', Times, serif; color:rgb(46, 161, 115);">Enter Menu Item Name:</label>
            <input type="text" id="item" name="item" required  style="width: 840px;">
            <br><br>
            <label for="descriptions" style="font-size: 27px; font-family: Georgia, 'Times New Roman', Times, serif; color:rgb(46, 161, 115);">Description of Item:</label>
            <input type="text" id="descriptions" name="decriptions" required  style="width: 840px;">
            <br><br><br><br>
            <input type="submit" value="--> Enter Contest! <--" style="margin-left: 750px; width: 35%;">
        </form>
    </div>
    <br><br><br>
    <footer>
    <p class="footer-nav"><br><br>
        <h4><a href="index.html" style="color: rgb(98, 76, 49); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-decoration: none;">Home: |</a>
            <a href="about.html" style="color: rgb(98, 76, 49); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-decoration: none;">About: |</a>
            <a href="menu.html" style="color: rgb(98, 76, 49); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-decoration: none;">Menu: |</a>
            <a href="news.html" style="color: rgb(98, 76, 49); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-decoration: none;">News: |</a>
            <a href="contact.html" style="color: rgb(98, 76, 49); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-decoration: none;">Contact: | </a>
            <a href="contesting.html" style="color: rgb(98, 76, 49); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-decoration: none;">Contesting:</a>
        </p>
    <p class="footer-bottom"><br><br><br>*Copyright 2024. Paraiso's Inc. All Rights Reserved </p>
    <a href="https://x.com/">
    <img src="twitter-app-new-logo-x-black-background_1017-45425.avif", alt="Logo of X and Link", class="image-x"></a>
    <a href="https://www.instagram.com/">
    <img src="OIP.jpg", alt="Logo of Instagram and Link", class="image-i"></a>
    <a href="https://www.facebook.com/">
    <img src="facebook.jpg", alt="Logo of Facebook and Link", class="image-f"></a>
    </footer>

</body>
</html>