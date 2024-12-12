<?php

session_start();
require_once 'auth.php';

// This will check if user is logged in or not
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
} 

$host = 'localhost'; 
$dbname = 'reserving'; 
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
    $search_term = $_GET['search'];

    if (DateTime::createFromFormat('Y-m-d', $search_term) !== false) {
        $search_sql = 'SELECT party, members, timing, dates FROM reserved WHERE dates LIKE :search';
        $search_stmt = $pdo->prepare($search_sql);
        $search_stmt->execute(['search' => '%' . $search_term . '%']);
        $search_results = $search_stmt->fetchAll();
    } else {
        $error_message = "Please enter a valid date in the format YYYY-MM-DD.";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['party']) && isset($_POST['members']) && isset($_POST['timing']) && isset($_POST['dates'])) {
       
        $party = htmlspecialchars($_POST['party']);
        $members = (int) htmlspecialchars($_POST['members']);
        $timing = htmlspecialchars($_POST['timing']);
        $dates = htmlspecialchars($_POST['dates']);
        
      
        if ($members > 10) {
            $error_message = "Members cannot exceed 10.";
        }

       
        $time = strtotime($timing);
        $start_time = strtotime('09:00');
        $end_time = strtotime('23:00');
        if ($time < $start_time || $time > $end_time) {
            $error_message = "Time must be between 9 AM and 11 PM.";
        }

       
        $day_of_week = date('N', strtotime($dates));
        if ($day_of_week > 6) {
            $error_message = "Date must be between Monday and Saturday.";
        }

    
        $current_date = date('Y-m-d');
        $max_date = date('Y-m-d', strtotime('+2 weeks'));
        if ($dates < $current_date || $dates > $max_date) {
            $error_message = "Date must be within a two-week window from today.";
        }

      
        $tomorrow_date = date('Y-m-d', strtotime('+1 day'));
        if ($dates == $current_date || $dates == $tomorrow_date) {
            $error_message = "You cannot make a reservation for today or tomorrow.";
        }

   
        if (empty($error_message)) {
            $insert_sql = 'INSERT INTO reserved (party, members, timing, dates) VALUES (:party, :members, :timing, :dates)';
            $stmt_insert = $pdo->prepare($insert_sql);
            $stmt_insert->execute(['party' => $party, 'members' => $members, 'timing' => $timing, 'dates' => $dates]);
        }
    } elseif (isset($_POST['delete_party'])) {
      
        $delete_party = htmlspecialchars($_POST['delete_party']);
        
        $delete_sql = 'DELETE FROM reserved WHERE party = :party';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['party' => $delete_party]);
    }
}

$sql = 'SELECT party, members, timing, dates FROM reserved';
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=2.0">
    <title>Registration Page - Taco Paraíso</title>
    <link rel="stylesheet" href="styles3.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
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

<body style="background-color: bisque;">
    <nav class="nav">
        <table class="nav-table">
            <tr>
                <th><a href="index.html">Home</a></th>
                <th><a href="about.html">About</a></th>
                <th><a href="menu.html">Menu</a></th>
                <th><a href="news.html">News</a></th>
                <th><a href="contact.html">Contact</a></th>
                <th><a href="contesting.html">Contest</a></th>
            </tr>
        </table>
    </nav>
    <br><br><br><br>
    <h5 style="margin-bottom: 25px; color: rgb(199, 88, 19);">Taco Paraiso - Registration</h5>
    <hr style="  border: 3px dashed rgb(255, 98, 20);"><br><br><br>
    <div class="hero-section">
        
        <div class="hero-search"><br><br>
            <h2 style="font-family: Algerian; font-weight: bold; font-size: 45px; color: rgb(255, 157, 44); text-align: center;"><u>Search for a date for registrations:</u></h2><br>
            <form action="" method="GET" class="search-form">
                <label for="search">Search for Date (YYYY-MM-DD):</label>
                <input type="text" id="search" name="search" required>
                <input type="submit" value="Search"  style="width: 350px;">
            </form>
            
            <?php if (isset($_GET['search'])): ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <?php if ($search_results && count($search_results) > 0): ?>
                        <table>
                            <thead>
                                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 30px; color: brown; margin-bottom: 20px;">
                                    <th>Party Name:</th>
                                    <th>Members in Party:</th>
                                    <th>Time:</th>
                                    <th>Date:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 30px; color: brown; word-spacing: 2px; margin-bottom: 21px">
                                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                                    <td><?php echo htmlspecialchars($row['members']); ?></td>
                                    <td><?php echo htmlspecialchars($row['timing']); ?></td>
                                    <td><?php echo htmlspecialchars($row['dates']); ?></td>
                                    <td>
                                        <form action="reserve.php" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_party" value="<?php echo $row['party']; ?>">
                                            <input type="submit" value="Remove!" style= "width: 400px;"onclick="return confirm('Are You Sure??');">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="font-style: italic; color: rgb(255, 102, 0);"><br><br>*There are no registrations for this particular day, enter another date*</p><br><br>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-container">
        <h2>All Recorded registrations!</h2>
        <hr style="border: 2px solid rgb(145, 121, 109); width: 37%"><br><br><br>
        <table class="half-width-left-align">
            <thead>
                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 30px; color: brown; margin-bottom: 20px;">
                     <th>Party Name:</th>
                     <th>Members in Party:</th>
                     <th>Time:</th>
                     <th>Date:</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr style="font-weight: bold; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 30px; color: brown; word-spacing: 2px; margin-bottom: 21px;">
                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                    <td><?php echo htmlspecialchars($row['members']); ?></td>
                    <td><?php echo htmlspecialchars($row['timing']); ?></td>
                    <td><?php echo htmlspecialchars($row['dates']); ?></td>
                    <td>
                        <form action="reserve.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_party" value="<?php echo $row['party']; ?>">
                            <input type="submit" value="Remove!" style= "width: 200px; margin-left: 300px;" onclick="return confirm('Do you really want to remove this registration?');">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="form-container">
        <h2>Please Make Your Registration Here!</h2>
        <hr style="border: 2px solid rgb(145, 121, 109); width: 49.5%"><br><br><br>
        <?php if (!empty($error_message)): ?>
            <p class="error-message" style="font-style: italic; color: darkred; font-size: 45px; text-align: center;"><?php echo $error_message; ?></p><br><br>
        <?php endif; ?>
        <form action="reserve.php" method="post">
            <label for="party" style="font-size: 30px; font-family: Georgia, 'Times New Roman', Times, serif; color: rgb(255, 124, 63);">Party Name:</label>
            <input type="text" id="party" name="party" required style="width: 840px;">
            <br><br>
            <label for="members" style="font-size: 30px; font-family: Georgia, 'Times New Roman', Times, serif; color: rgb(255, 124, 63);">Total Members in Party:</label>
            <input type="number" id="members" name="members" required>
            <br><br>
            <label for="timing" style="font-size: 30px; font-family: Georgia, 'Times New Roman', Times, serif; color: rgb(255, 124, 63);">Time:</label>
            <input type="time" id="timing" name="timing" required>
            <br><br>
            <label for="dates" style="font-size: 30px; font-family: Georgia, 'Times New Roman', Times, serif; color: rgb(255, 124, 63);">Date:</label>
            <input type="date" id="dates" name="dates" required>
            <br><br><br><br>
            <input type="submit" value="--> Book Your Registration! <--" style="margin-left: 750px; width: 35%;">
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