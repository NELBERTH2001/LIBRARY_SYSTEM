<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=books_export.csv');
$output = fopen('php://output', 'w');
fputcsv($output, ['id','isbn','title','author','publisher','year_pub','category','copies_total','copies_available','shelf_location','status','created_at','updated_at']);
$res = $conn->query("SELECT * FROM books ORDER BY id ASC");
while ($row = $res->fetch_assoc()) { fputcsv($output, $row); }
fclose($output); exit;
