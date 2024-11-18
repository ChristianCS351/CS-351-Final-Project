<<?php
$host = 'localhost'; 
$dbname = 'bookes'; 
$user = 'christian'; 
$pass = 'passwd';
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

// Handle book search
$search_results = null;
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $search_sql = 'SELECT id, author, title, publisher, published, genre FROM books WHERE title LIKE :search';
    $search_stmt = $pdo->prepare($search_sql);
    $search_stmt->execute(['search' => $search_term]);
    $search_results = $search_stmt->fetchAll();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['author']) && isset($_POST['title']) && isset($_POST['publisher']) && isset($_POST['published']) && isset($_POST['genre'])) {
        // Insert new entry
        $author = htmlspecialchars($_POST['author']);
        $title = htmlspecialchars($_POST['title']);
        $publisher = htmlspecialchars($_POST['publisher']);
        $published = htmlspecialchars($_POST['published']);
        $genre = htmlspecialchars($_POST['genre']);
        
        $insert_sql = 'INSERT INTO books (author, title, publisher, published, genre) VALUES (:author, :title, :publisher, :published, :genre)';
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute(['author' => $author, 'title' => $title, 'publisher' => $publisher, 'published' => $published, 'genre' => $genre]);
    } elseif (isset($_POST['delete_id'])) {
        // Delete an entry
        $delete_id = (int) $_POST['delete_id'];
        
        $delete_sql = 'DELETE FROM books WHERE id = :id';
        $stmt_delete = $pdo->prepare($delete_sql);
        $stmt_delete->execute(['id' => $delete_id]);
    }
}

// Get all books for main table
$sql = 'SELECT id, author, title, publisher, published, genre FROM books';
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Christian's Book Unbanning Program - Project 3 Altered Database</title>
    <link rel="stylesheet" href="stylesP3.css">
    <link rel="icon" type="image/x-icon" href="faviconbook.ico">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Christian's Book Unbanning Program - Project 3 Altered Database</h1>
        <p class="hero-subtitle">"What is better than banning books? Unbanning the banned books of course!"</p>
        
        <!-- Search moved to hero section -->
        <div class="hero-search">
            <h2>Search for a Book to Advocate to Unban:</h2>
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
                                    <th>ID</th>
                                    <th>Author</th>
                                    <th>Title</th>
                                    <th>Publisher</th>
                                    <th>Published</th>
                                    <th>Genre</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['publisher']); ?></td>
                                    <td><?php echo htmlspecialchars($row['published']); ?></td>
                                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                                    <td>
                                        <form action="index4.php" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <input type="submit" value="Remove!" onclick="return confirm('Do you really want to unban this book?');">
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No books were found during your search, please type a valid book.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table section with container -->
    <div class="table-container">
        <h2>All Books in Database to be Unbanned</h2>
        <table class="half-width-left-align">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Author</th>
                    <th>Title</th>
                    <th>Publisher</th>
                    <th>Published</th>
                    <th>Genre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['publisher']); ?></td>
                    <td><?php echo htmlspecialchars($row['published']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre']); ?></td>
                    <td>
                        <form action="index5.php" method="post" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Remove!" onclick="return confirm('Do you really want to remove this book from being unbanned?');">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Form section with container -->
    <div class="form-container">
        <h2>Bring a Book Back to Life Today!</h2>
        <form action="index5.php" method="post">
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required>
            <br><br>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <br><br>
            <label for="publisher">Publisher:</label>
            <input type="text" id="publisher" name="publisher" required>
            <br><br>
            <label for="published">Published:</label>
            <input type="date" id="published" name="published" required>
            <br><br>
            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" required>
            <br><br>
            <input type="submit" value="Bring Back!">
        </form>
    </div>
</body>
</html>