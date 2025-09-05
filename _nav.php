<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/library-inventory-frontend/admin/dashboard.php">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/library-inventory-frontend/admin/books.php">Books</a></li>
        <li class="nav-item"><a class="nav-link" href="/library-inventory-frontend/admin/borrows.php">Borrow/Return</a></li>
        <li class="nav-item"><a class="nav-link" href="/library-inventory-frontend/admin/students.php">Students</a></li>
         <button class="btn btn-primary" onclick="window.print()">Print Report</button>
      </ul>
      <a class="btn btn-outline-light btn-sm" href="/library-inventory-frontend/admin/logout.php">Logout</a>
    </div>
  </div>
</nav>
