<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status_baru = $conn->real_escape_string($_GET['status']);
    $admin_aktif = $conn->real_escape_string($_SESSION['admin_nama']);

    $query = "UPDATE pesanan SET status = '$status_baru'";
    if ($status_baru === 'Dibayar') {
        $query .= ", waktu_dibayar = NOW(), dikonfirmasi_oleh = '$admin_aktif'";
    }
    $query .= " WHERE id = $id";
    
    $conn->query($query);
}

header("Location: index.php");
exit;