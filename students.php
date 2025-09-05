<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE students SET status = IF(status='active','inactive','active') WHERE id=$id");
    set_flash('Student status updated.');
    header('Location: /library-inventory-frontend/admin/students.php'); exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id");
    set_flash('Student deleted.');
    header('Location: /library-inventory-frontend/admin/students.php'); exit;
}
$students = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
?>



<?php include __DIR__ . '/../includes/header.php'; ?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Students</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body>
<?php include __DIR__ . '/_nav.php'; ?>
<div class="container py-4">
  <?php flash_message(); ?>
  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
      <thead><tr><th>Student No.</th><th>Name</th><th>Course</th><th>Year</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <?php while($s=$students->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($s['student_no']); ?></td>
          <td><?php echo htmlspecialchars($s['name']); ?></td>
          <td><?php echo htmlspecialchars($s['course']); ?></td>
          <td><?php echo htmlspecialchars($s['year_level']); ?></td>
          <td><?php echo htmlspecialchars($s['email']); ?></td>
          <td><?php echo htmlspecialchars($s['phone']); ?></td>
          <td><span class="badge text-bg-<?php echo $s['status']=='active'?'success':'secondary'; ?>"><?php echo $s['status']; ?></span></td>
          <td>
            <a class="btn btn-sm btn-outline-secondary" href="?toggle=<?php echo $s['id']; ?>">Toggle</a>
            <a class="btn btn-sm btn-outline-danger" href="?delete=<?php echo $s['id']; ?>" onclick="return confirm('Delete student?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>
