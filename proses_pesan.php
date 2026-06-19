<?php
session_start();
header('Content-Type: application/json');
include 'config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Data tidak valid']);
    exit;
}

$no_meja = $input['no_meja'];
$nama_pemesan = $input['nama_pemesan'];
$catatan = $input['catatan'] ?? '';
$items = $input['items'];
$metode_pembayaran = $input['metode_pembayaran'] ?? 'Cash';

$total_harga = 0;
foreach($items as $item) {
    $total_harga += $item['harga'] * $item['jumlah'];
}

$sql = "INSERT INTO pesanan (no_meja, nama_pemesan, total_harga, status, catatan, metode_pembayaran) 
        VALUES ('$no_meja', '$nama_pemesan', '$total_harga', 'Menunggu Pembayaran', '$catatan', '$metode_pembayaran')";
if ($conn->query($sql)) {
    $id_pesanan = $conn->insert_id;
    
    foreach($items as $item) {
        $menu_id = $item['id'];
        $jumlah = $item['jumlah'];
        $subtotal = $item['harga'] * $jumlah;
        
        $conn->query("INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, subtotal) 
                      VALUES ('$id_pesanan', '$menu_id', '$jumlah', '$subtotal')");
    }
    
    echo json_encode(['success' => true, 'id_pesanan' => $id_pesanan]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>
