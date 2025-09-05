<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';


// Start session if not already started

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

if (isset($_SESSION['admin_id'])) { 
    header('Location: /library-inventory-frontend/admin/dashboard.php'); 
    exit; 
}

$msg = '';
if (is_post()) {
    $username = sanitize($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ✅ Use correct column name
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_username'] = $username;
        header('Location: /library-inventory-frontend/admin/dashboard.php'); 
        exit;
    } else { 
        $msg = 'Invalid credentials'; 
    }
}
?>

<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="/library-inventory-frontend/public/assets/css/style.css">
</head><body class="bg-light">
<div class="container py-5">
  <div class="row"><div class="col-md-4 mx-auto">
    <div class="card shadow-sm"><div class="card-body">
      <h5 class="card-title text-center">Admin Login</h5>
      <?php if ($msg): ?><div class="alert alert-danger"><?php echo $msg; ?></div><?php endif; ?>
      <form method="post" class="row g-3">
        <div class="col-12"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="col-12 d-grid"><button class="btn btn-primary">Login</button></div>
        <div class="col-12 text-center"><a href="/library-inventory-frontend/index.php" class="small">← Back to site</a></div>
      </form>
    </div></div>
    <!-- <a href="register.php" class="btn btn-success w-100">Register New Admin</a>  -->
  </div></div>
</div>
</body></html>
