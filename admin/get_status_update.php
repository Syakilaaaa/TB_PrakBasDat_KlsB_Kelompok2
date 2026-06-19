<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    die(json_encode(['error' => 'Unauthorized']));
}

include '../config/db.php';

// Ambil statistik
$menunggu = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Menunggu Pembayaran'")->fetch_assoc()['count'];
$dibayar = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Dibayar'")->fetch_assoc()['count'];
$masak = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Sedang Dimasak'")->fetch_assoc()['count'];
$selesai = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Selesai'")->fetch_assoc()['count'];

// Ambil semua pesanan
$pesanan = $conn->query("SELECT * FROM pesanan ORDER BY FIELD(status, 'Menunggu Pembayaran', 'Dibayar', 'Sedang Dimasak', 'Selesai'), tanggal_pesan DESC");
$data_pesanan = [];
while($row = $pesanan->fetch_assoc()) {
    $data_pesanan[] = $row;
}

echo json_encode([
    'menunggu' => $menunggu,
    'dibayar' => $dibayar,
    'masak' => $masak,
    'selesai' => $selesai,
    'pesanan' => $data_pesanan
]);
?>