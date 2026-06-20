<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$allowed_status = ['Menunggu Pembayaran', 'Dibayar', 'Sedang Dimasak', 'Selesai'];

if ($id > 0 && in_array($status, $allowed_status)) {
    $conn->query("UPDATE pesanan SET status = '$status' WHERE id = $id");
    
    if ($status == 'Dibayar') {
        $conn->query("UPDATE pesanan SET waktu_dibayar = NOW() WHERE id = $id");
    }
}

header("Location: index.php");
?>
