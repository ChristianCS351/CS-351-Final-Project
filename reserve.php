<?php

session_start();
require_once 'auth.php';

// Check if user is logged in
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

// Initialize error message
$error_message = '';

// Handle book search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT party, members, timing, dates FROM reserved WHERE dates LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['party']) && isset($_POST['members']) && isset($_POST['timing']) && isset($_POST['dates'])) {
        // Validate inputs
        $party = htmlspecialchars($_POST['party']);
        $members = (int) htmlspecialchars($_POST['members']);
        $timing = htmlspecialchars($_POST['timing']);
        $dates = htmlspecialchars($_POST['dates']);
        
        // Check members limit
        if ($members > 10) {
            $error_message = "Members cannot exceed 10.";
        }

        // Check time range
        $time = strtotime($timing);
        $start_time = strtotime('09:00');
        $end_time = strtotime('23:00');
        if ($time < $start_time || $time > $end_time) {
            $error_message = "Time must be between 9 AM and 11 PM.";
        }

        // Check date range (Monday to Saturday)
        $day_of_week = date('N', strtotime($dates));
        if ($day_of_week > 6) {
            $error_message = "Date must be between Monday and Saturday.";
        }

        // Check booking within two-week window
        $current_date = date('Y-m-d');
        $max_date = date('Y-m-d', strtotime('+2 weeks'));
        if ($dates < $current_date || $dates > $max_date) {
            $error_message = "Date must be within a two-week window from today.";
        }

        // Check if the date is today or tomorrow
        $tomorrow_date = date('Y-m-d', strtotime('+1 day'));
        if ($dates == $current_date || $dates == $tomorrow_date) {
            $error_message = "You cannot make a reservation for today or tomorrow.";
        }

        // If no errors, insert new entry
        if (empty($error_message)) {
            $insert_sql = 'INSERT INTO reserved (party, members, timing, dates) VALUES (:party, :members, :timing, :dates)';
            $stmt_insert = $pdo->prepare($insert_sql);
            $stmt_insert->execute(['party' => $party, 'members' => $members, 'timing' => $timing, 'dates' => $dates]);
        }
    } elseif (isset($_POST['delete_party'])) {
        // Delete a reservation entry
        $delete_party = htmlspecialchars($_POST['delete_party']);
        
        $delete_sql = 'DELETE FROM reserved WHERE party = :party';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['party' => $delete_party]);
    }
}

// Get all reserved for main table
$sql = 'SELECT party, members, timing, dates FROM reserved';
$stmt = $pdo->query($sql);
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
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Taco Paraiso - Registration</h1>
        <p class="hero-subtitle">"Make your reservation today"</p>
        
        <!-- Search moved to hero section -->
        <div class="hero-search">
            <h2>Search for a date for registrations:</h2>
            <form action="" method="GET" class="search-form">
                <label for="search">Search by Book Title:</label>
                <input type="text" id="search" name="search" required>
                <input type="submit" value="Search">
            </form>
            
            <?php if (isset($_GET['search'])): ?>
                <div class="search-results">
                    <h3>Search Results</h3>
                    <?php if ($search_results && count($search_results) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Party Name:</th>
                                    <th>Members in Party:</th>
                                    <th>Time:</th>
                                    <th>Date:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                                    <td><?php echo htmlspecialchars($row['members']); ?></td>
                                    <td><?php echo htmlspecialchars($row['timing']); ?></td>
                                    <td><?php echo htmlspecialchars($row['dates']); ?></td>
                                    <td>
                                        <form action="reserve.php" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_party" value="<?php echo $row['party']; ?>">
                                            <input type="submit" value="Remove!" onclick="return confirm('Does your registration look good to you?');">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>There are no registrations for this particular day.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table section with container -->
    <div class="table-container">
        <h2>All Recorded registrations!</h2>
        <table class="half-width-left-align">
            <thead>
                <tr>
                     <th>Party Name:</th>
                     <th>Members in Party:</th>
                     <th>Time:</th>
                     <th>Date:</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                    <td><?php echo htmlspecialchars($row['members']); ?></td>
                    <td><?php echo htmlspecialchars($row['timing']); ?></td>
                    <td><?php echo htmlspecialchars($row['dates']); ?></td>
                    <td>
                        <form action="reserve.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_party" value="<?php echo $row['party']; ?>">
                            <input type="submit" value="Remove!" onclick="return confirm('Do you really want to remove this registration?');">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Form section with container -->
    <div class="form-container">
        <h2>Please Make Your Registration Here!</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="reserve.php" method="post">
            <label for="party">Party Name:</label>
            <input type="text" id="party" name="party" required>
            <br><br>
            <label for="members">Total Members in Party:</label>
            <input type="number" id="members" name="members" required>
            <br><br>
            <label for="timing">Time:</label>
            <input type="time" id="timing" name="timing" required>
            <br><br>
            <label for="dates">Date:</label>
            <input type="date" id="dates" name="dates" required>
            <br><br>
            <input type="submit" value="Book Your Registration!">
        </form>
    </div>
</body>
</html>
```

