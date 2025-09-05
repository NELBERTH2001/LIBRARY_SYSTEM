<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';
$counts = ['books'=>0,'students'=>0,'requested'=>0,'issued'=>0];
$res = $conn->query("SELECT COUNT(*) c FROM books"); $counts['books']=$res->fetch_assoc()['c'];
$res = $conn->query("SELECT COUNT(*) c FROM students"); $counts['students']=$res->fetch_assoc()['c'];
$res = $conn->query("SELECT COUNT(*) c FROM borrow_transactions WHERE status='requested'"); $counts['requested']=$res->fetch_assoc()['c'];
$res = $conn->query("SELECT COUNT(*) c FROM borrow_transactions WHERE status='issued'"); $counts['issued']=$res->fetch_assoc()['c'];
?>



<?php include __DIR__ . '/../includes/header.php'; ?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body>
<?php include __DIR__.'/_nav.php'; ?>
<div class="container py-4">
  <div class="row g-3">
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6>Books</h6><div class="display-6"><?php echo $counts['books']; ?></div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6>Students</h6><div class="display-6"><?php echo $counts['students']; ?></div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6>Requested</h6><div class="display-6"><?php echo $counts['requested']; ?></div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6>Issued</h6><div class="display-6"><?php echo $counts['issued']; ?></div></div></div></div>
  </div>
</div>
</body></html>












