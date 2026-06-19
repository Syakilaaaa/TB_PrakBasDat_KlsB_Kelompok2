<?php
include 'config/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) { header("Location: index.php"); exit; }

$pesanan = $conn->query("SELECT * FROM pesanan WHERE id = $id")->fetch_assoc();
if (!$pesanan) { header("Location: index.php"); exit; }

$detail_result = $conn->query("SELECT d.*, m.nama_menu FROM detail_pesanan d JOIN menu m ON d.menu_id = m.id WHERE d.pesanan_id = $id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>NyamMeow - Nota #<?= $id ?></title>
    <style>
        body { font-family: monospace; background: #f4f4f4; padding: 20px; }
        .receipt { background: white; max-width: 400px; margin: auto; padding: 20px; border: 1px dashed #333; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .box { background: #fffdf9; padding: 10px; border-radius: 5px; margin-top: 15px; border-left: 4px solid #FF8C42; font-size: 13px; }
        .btn { padding: 8px 15px; background: #333; color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 10px; border:none; cursor:pointer; font-weight: bold;}
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="receipt">
        <h3 style="text-align: center;">🐱 NYAMMEOW RESTO 🐱</h3>
        <p style="text-align: center; font-size:12px;">Nota Transaksi Penjualan #<?= $id ?></p>
        <div class="divider"></div>
        <p><b>Nama:</b> <?= htmlspecialchars($pesanan['nama_pemesan']) ?> (Meja <?= $pesanan['no_meja'] ?>)</p>
        <p><b>Metode:</b> <?= $pesanan['metode_pembayaran'] ?></p>
        <p><b>Waktu:</b> <?= $pesanan['tanggal_pesan'] ?></p>
        <div class="divider"></div>
        <table width="100%">
            <?php $total = 0; while($row = $detail_result->fetch_assoc()): $total += $row['subtotal']; ?>
            <tr>
                <td><?= $row['nama_menu'] ?> x <?= $row['jumlah'] ?></td>
                <td align="right">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <div class="divider"></div>
        <h4>TOTAL: <span style="float: right;">Rp <?= number_format($total, 0, ',', '.') ?></span></h4>
        <div class="divider"></div>
        
        <div class="box">
            <strong>📌 STATUS INFRASTRUKTUR:</strong><br>
            <?php if($pesanan['metode_pembayaran'] == 'Cash'): ?>
                <p style="color: #d9534f; margin-top:5px;">➔ Status: Menunggu Transaksi Tunai.<br>Silakan tunjukkan nota cetak ini ke meja Kasir Admin untuk diverifikasi lunas.</p>
            <?php else: ?>
                <p style="color: #28a745; margin-top:5px;">➔ Status: Berhasil Dibayar (QRIS Lunas).<br>Data pesanan Anda sudah dikirim ke antrean monitor dapur.</p>
                <div style="text-align: center; margin-top:12px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=NyamMeow-Lunas-<?= $id ?>" alt="QRIS"><br>
                    <small style="color: grey; font-size:10px;">SIMULASI SISTEM QRIS OK</small>
                </div>
            <?php endif; ?>
        </div>
        <div style="text-align: center; margin-top: 15px;" class="no-print">
            <a href="index.php" class="btn">🏠 Beranda Menu</a>
            <button onclick=\"window.print()\" class="btn" style="background:#FF8C42;">🖨️ Cetak Fisik</button>
        </div>
    </div>
</body>
</html>