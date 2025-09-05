<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = $_FILES['file']['tmp_name'];
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK && is_uploaded_file($fileName)) {
        $handle = fopen($fileName, "r");
        if ($handle !== false) {
            // Skip header row
            fgetcsv($handle);

            $count = 0;
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                // Expected order: ISBN, Title, Author, Publisher, Year, Category, Total, Available, Shelf, Status
                $isbn            = sanitize($conn, $row[0] ?? '');
                $title           = sanitize($conn, $row[1] ?? '');
                $author          = sanitize($conn, $row[2] ?? '');
                $publisher       = sanitize($conn, $row[3] ?? '');
                $year_pub        = sanitize($conn, $row[4] ?? '');
                $category        = sanitize($conn, $row[5] ?? '');
                $copies_total    = (int)($row[6] ?? 1);
                $copies_available= (int)($row[7] ?? $copies_total);
                $shelf_location  = sanitize($conn, $row[8] ?? '');
                $status          = sanitize($conn, $row[9] ?? 'available');

                if ($title !== '') {
                    $stmt = $conn->prepare("INSERT INTO books (isbn, title, author, publisher, year_pub, category, copies_total, copies_available, shelf_location, status) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    $stmt->bind_param("ssssssiiis", $isbn, $title, $author, $publisher, $year_pub, $category, $copies_total, $copies_available, $shelf_location, $status);
                    $stmt->execute();
                    $count++;
                }
            }
            fclose($handle);
            $msg = "Successfully imported $count books.";
        } else {
            $msg = "Could not read the file.";
        }
    } else {
        $msg = "File upload error.";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Import Books</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <h3>Import Books from CSV</h3>
  <?php if ($msg): ?>
    <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Upload CSV File</label>
      <input type="file" name="file" class="form-control" accept=".csv" required>
      <div class="form-text">Format: ISBN, Title, Author, Publisher, Year, Category, Total, Available, Shelf, Status</div>
    </div>
    <button class="btn btn-primary">Import</button>
    <a href="books.php" class="btn btn-secondary">Back</a>
  </form>
</div>
</body>
</html>
