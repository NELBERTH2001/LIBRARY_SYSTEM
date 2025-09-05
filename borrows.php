<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// ✅ Set Philippine Timezone
date_default_timezone_set('Asia/Manila');

if (is_post()) {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $notes = sanitize($conn, $_POST['notes'] ?? '');
    $now = date('Y-m-d H:i:s'); // Philippine time

    $stmt = $conn->prepare("SELECT * FROM borrow_transactions WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $tr = $stmt->get_result()->fetch_assoc();

    if ($tr) {
        if ($action === 'issue' && $tr['status']=='requested') {
            $conn->query("UPDATE books SET copies_available = GREATEST(0, copies_available - ".$tr['qty'].") WHERE id=".$tr['book_id']);
            $due = date('Y-m-d H:i:s', strtotime('+7 days')); 
            $upd = $conn->prepare("UPDATE borrow_transactions SET status='issued', issued_at=?, due_at=?, notes=? WHERE id=?");
            $upd->bind_param("sssi", $now, $due, $notes, $id);
            $upd->execute();
            set_flash('Request issued.');
        } elseif ($action === 'return' && ($tr['status']=='issued' || $tr['status']=='overdue')) {
            $conn->query("UPDATE books SET copies_available = copies_available + ".$tr['qty']." WHERE id=".$tr['book_id']);
            $upd = $conn->prepare("UPDATE borrow_transactions SET status='returned', returned_at=?, notes=? WHERE id=?");
            $upd->bind_param("ssi", $now, $notes, $id);
            $upd->execute();
            set_flash('Book returned.');
        } elseif ($action === 'deny' && $tr['status']=='requested') {
            $upd = $conn->prepare("UPDATE borrow_transactions SET status='denied', notes=? WHERE id=?");
            $upd->bind_param("si", $notes, $id);
            $upd->execute();
            set_flash('Request denied.');
        } elseif ($action === 'overdue' && $tr['status']=='issued') {
            $upd = $conn->prepare("UPDATE borrow_transactions SET status='overdue', notes=? WHERE id=?");
            $upd->bind_param("si", $notes, $id);
            $upd->execute();
            set_flash('Marked as overdue.');
        }
    }
    header('Location: /library-inventory-frontend/admin/borrows.php'); exit;
}

// ✅ Fetch all borrow transactions including book cover image and year published
$res = $conn->query("
  SELECT bt.*, b.title, b.year_pub, b.image, s.student_no, s.name
  FROM borrow_transactions bt
  JOIN books b ON bt.book_id=b.id
  JOIN students s ON bt.student_id=s.id
  ORDER BY bt.requested_at DESC
");
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Borrow/Return</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body>
<?php include __DIR__ . '/_nav.php'; ?>
<div class="container py-4">
  <?php flash_message(); ?>
  <div class="table-responsive">

    <h2 class="mb-3">Borrow Transactions</h2>
    <p class="small text-muted mb-4">Manage borrow requests and track their status.</p>
    

    <?php if ($res->num_rows === 0): ?>
      <div class="alert alert-info">No borrow transactions found.</div>
    <?php endif; ?>
  
    <table class="table table-sm table-striped align-middle">
      <thead>
        <tr>
          <th>Code</th>
          <th>Student ID</th>
          <th>Student Name</th>
          <th>Book (Year Published)</th>
          <th>Image</th>
          <th>Qty</th>
          <th>Status</th>
          <th>Requested</th>
          <th>Issued</th>
          <th>Due</th>
          <th>Returned</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php while($r=$res->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['borrow_code']); ?></td>
          <td><?php echo htmlspecialchars($r['student_no']); ?></td>
          <td><?php echo htmlspecialchars($r['name']); ?></td>
          <td>
            <?php echo htmlspecialchars($r['title']); ?>
            <?php if (!empty($r['year_pub'])): ?>
              <br><small class="text-muted">(<?php echo htmlspecialchars($r['year_pub']); ?>)</small>
            <?php endif; ?>
          </td>
          
          <td>
            <?php if (!empty($r['image'])): ?>
                <img src="/library-inventory-frontend/<?php echo htmlspecialchars($r['image']); ?>" 
     class="rounded" style="height:50px;" alt="book">
            <?php else: ?>
                <img src="https://via.placeholder.com/50x75?text=No+Cover" 
                     class="rounded" style="height:50px;" alt="no cover">
            <?php endif; ?>
          </td>

          <td><?php echo (int)$r['qty']; ?></td>

          <td>
            <span class="badge text-bg-<?php
              echo $r['status']=='issued' ? 'warning' :
                   ($r['status']=='returned' ? 'success' :
                   ($r['status']=='denied' ? 'secondary' :
                   ($r['status']=='overdue' ? 'danger':'info'))); ?>">
              <?php echo htmlspecialchars($r['status']); ?>
            </span>
          </td>
          
          <td><?php echo htmlspecialchars($r['requested_at']); ?></td>
          <td><?php echo htmlspecialchars($r['issued_at']); ?></td>
          <td><?php echo htmlspecialchars($r['due_at']); ?></td>
          <td><?php echo htmlspecialchars($r['returned_at']); ?></td>

          <td>
            <form method="post" class="d-flex gap-1">
              <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
              <?php if ($r['status']=='requested'): ?>
                <button name="action" value="issue" class="btn btn-sm btn-outline-primary">Issue</button>
                <button name="action" value="deny" class="btn btn-sm btn-outline-danger">Deny</button>
              <?php elseif ($r['status']=='issued'): ?>
                <button name="action" value="overdue" class="btn btn-sm btn-outline-warning">Overdue</button>
                <button name="action" value="return" class="btn btn-sm btn-outline-success">Return</button>
              <?php elseif ($r['status']=='overdue'): ?>
                <button name="action" value="return" class="btn btn-sm btn-outline-success">Return</button>
              <?php else: ?>
                <span class="text-muted small">No actions</span>
              <?php endif; ?>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>