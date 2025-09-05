<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_post()) {
    $id = (int)($_POST['id'] ?? 0);
    $isbn = sanitize($conn, $_POST['isbn'] ?? '');
    $title = sanitize($conn, $_POST['title'] ?? '');
    $author = sanitize($conn, $_POST['author'] ?? '');
    $publisher = sanitize($conn, $_POST['publisher'] ?? '');
    $year_pub = sanitize($conn, $_POST['year_pub'] ?? '');
    $category = sanitize($conn, $_POST['category'] ?? '');
    $copies_total = (int)($_POST['copies_total'] ?? 1);
    $copies_available = (int)($_POST['copies_available'] ?? $copies_total);
    $shelf_location = sanitize($conn, $_POST['shelf_location'] ?? '');
    $status = sanitize($conn, $_POST['status'] ?? 'available');

    // --- Handle Image Upload ---
    $imagePath = null;
    if (!empty($_FILES['book_image']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/books/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('book_', true) . '.' . strtolower($ext);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['book_image']['tmp_name'], $targetPath)) {
            // Save relative path so index.php can find it
            $imagePath = 'uploads/books/' . $filename;
        } else {
            error_log("Upload failed: " . $_FILES['book_image']['error']);
        }
    }

    if ($id > 0) {
        if ($imagePath) {
            $stmt = $conn->prepare("UPDATE books SET isbn=?, title=?, author=?, publisher=?, year_pub=?, category=?, copies_total=?, copies_available=?, shelf_location=?, status=?, image=? WHERE id=?");
            $stmt->bind_param("ssssssiiissi", $isbn, $title, $author, $publisher, $year_pub, $category, $copies_total, $copies_available, $shelf_location, $status, $imagePath, $id);
        } else {
            $stmt = $conn->prepare("UPDATE books SET isbn=?, title=?, author=?, publisher=?, year_pub=?, category=?, copies_total=?, copies_available=?, shelf_location=?, status=? WHERE id=?");
            $stmt->bind_param("ssssssiiisi", $isbn, $title, $author, $publisher, $year_pub, $category, $copies_total, $copies_available, $shelf_location, $status, $id);
        }
        $stmt->execute();
        set_flash('Book updated.');
    } else {
        $stmt = $conn->prepare("INSERT INTO books (isbn, title, author, publisher, year_pub, category, copies_total, copies_available, shelf_location, status, image) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssiiiss", $isbn, $title, $author, $publisher, $year_pub, $category, $copies_total, $copies_available, $shelf_location, $status, $imagePath);
        $stmt->execute();
        set_flash('Book added.');
    }
    header('Location: /library-inventory-frontend/admin/books.php'); exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    set_flash('Book deleted.');
    header('Location: /library-inventory-frontend/admin/books.php'); exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->prepare("SELECT * FROM books WHERE id=?");
    $res->bind_param("i", $id);
    $res->execute();
    $edit = $res->get_result()->fetch_assoc();
}

$books = $conn->query("SELECT * FROM books ORDER BY title ASC");
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Books</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body>
<?php include __DIR__ . '/_nav.php'; ?>
<div class="container py-4">
  <?php flash_message(); ?>
  <div class="row g-3">
    <div class="col-md-5">
      <div class="card"><div class="card-body">
        <h5 class="card-title"><?php echo $edit ? 'Edit Book' : 'Add Book'; ?></h5>
        <form method="post" enctype="multipart/form-data" class="row g-2">
          <input type="hidden" name="id" value="<?php echo $edit['id'] ?? 0; ?>">
          <div class="col-6"><label class="form-label">ISBN</label><input name="isbn" class="form-control" value="<?php echo htmlspecialchars($edit['isbn'] ?? ''); ?>"></div>
          <div class="col-6"><label class="form-label">Title</label><input name="title" class="form-control" required value="<?php echo htmlspecialchars($edit['title'] ?? ''); ?>"></div>
          <div class="col-6"><label class="form-label">Author</label><input name="author" class="form-control" value="<?php echo htmlspecialchars($edit['author'] ?? ''); ?>"></div>
          <div class="col-6"><label class="form-label">Publisher</label><input name="publisher" class="form-control" value="<?php echo htmlspecialchars($edit['publisher'] ?? ''); ?>"></div>
          <div class="col-4"><label class="form-label">Year</label><input name="year_pub" class="form-control" value="<?php echo htmlspecialchars($edit['year_pub'] ?? ''); ?>"></div>
          <div class="col-8"><label class="form-label">Category</label><input name="category" class="form-control" value="<?php echo htmlspecialchars($edit['category'] ?? ''); ?>"></div>
          <div class="col-4"><label class="form-label">Total</label><input type="number" name="copies_total" class="form-control" min="1" value="<?php echo htmlspecialchars($edit['copies_total'] ?? 1); ?>"></div>
          <div class="col-4"><label class="form-label">Available</label><input type="number" name="copies_available" class="form-control" min="0" value="<?php echo htmlspecialchars($edit['copies_available'] ?? 1); ?>"></div>
          <div class="col-4"><label class="form-label">Shelf</label><input name="shelf_location" class="form-control" value="<?php echo htmlspecialchars($edit['shelf_location'] ?? ''); ?>"></div>
          <div class="col-12">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <?php $st = $edit['status'] ?? 'available'; ?>
              <option value="available" <?php echo $st=='available'?'selected':''; ?>>available</option>
              <option value="unavailable" <?php echo $st=='unavailable'?'selected':''; ?>>unavailable</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Book Image</label>
            <input type="file" name="book_image" class="form-control" accept="image/*">
            <?php if (!empty($edit['image'])): ?>
              <div class="mt-2">
                <img src="/library-inventory-frontend/<?php echo $edit['image']; ?>" alt="Book Image" style="max-height:80px;" class="border rounded">
              </div>
            <?php endif; ?>
          </div>
          <div class="col-12 d-grid"><button class="btn btn-primary"><?php echo $edit ? 'Update' : 'Add'; ?></button></div>
        </form>
      </div></div>
    </div>
    <div class="col-md-7">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="m-0">Books List</h5>
        <div>
          <a class="btn btn-outline-secondary btn-sm" href="/library-inventory-frontend/admin/export_books_csv.php">Export CSV</a>
          <a class="btn btn-outline-success btn-sm" href="/library-inventory-frontend/admin/import_books.php">Import CSV</a>
        </div>
      </div>


      <div class="table-responsive">
        <table class="table table-sm table-striped align-middle">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Year Publish</th>
              <th>Author</th>
              <th>ISBN</th>
              <th>Avail/Total</th>
              <th>Shelf</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php while($b=$books->fetch_assoc()): ?>
            <tr>
              <td>
                <?php if (!empty($b['image'])): ?>
                  <img src="/library-inventory-frontend/<?php echo $b['image']; ?>" class="rounded" style="height:50px;" alt="book">
                <?php else: ?>
                  <img src="https://via.placeholder.com/40x50?text=Book" class="rounded" alt="placeholder">
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($b['title']); ?></td>
              <td><?php echo htmlspecialchars($b['year_pub']); ?></td>
              <td><?php echo htmlspecialchars($b['author']); ?></td>
              <td><?php echo htmlspecialchars($b['isbn']); ?></td>
              <td><?php echo (int)$b['copies_available'].'/'.(int)$b['copies_total']; ?></td>
              <td><?php echo htmlspecialchars($b['shelf_location']); ?></td>
              <td><span class="badge text-bg-<?php echo $b['status']=='available'?'success':'secondary'; ?>"><?php echo $b['status']; ?></span></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="?edit=<?php echo $b['id']; ?>">Edit</a>
                <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo $b['id']; ?>" onclick="return confirm('Delete book?')">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>

          
        </table>
      </div>
    </div>
  </div>
</div>
</body></html>






















