<?php
session_start();
include '../config/db.php'; 

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: login.php");
    exit;
}
$admin_aktif = $_SESSION['admin_nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NyamMeow</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <meta http-equiv="refresh" content="5">
</head>
<body>
    <div class="container">
        <div class="header" style="background: #333333;">
            <h1>👔 PANEL ADMIN &amp; KASIR 👔</h1>
            <p>Petugas Kasir Aktif Berjaga: <b>🟢 <?= htmlspecialchars($admin_aktif) ?></b></p>
        </div>

        <div class="nav">
            <a href="index.php" class="active">📋 Antrean Pesanan</a>
            <a href="logout.php" style="color: red;">🚪 Logout Akun</a>
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 15px; color:#333;">🐾 Antrean Dapur &amp; Kasir (Belum Selesai)</h3>
            <table class="menu-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f2f2f2;">
                        <th>ID</th>
                        <th>Meja</th>
                        <th>Nama Pemesan</th>
                        <th>Total Tagihan</th>
                        <th>Metode</th>
                        <th>Status Saat Ini</th>
                        <th>Aksi Urus Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $pesanan = $conn->query("SELECT * FROM pesanan WHERE status != 'Selesai' ORDER BY id DESC");
                    if($pesanan->num_rows == 0) {
                        echo "<tr><td colspan='7' align='center' style='padding:20px; color:grey;'>Sedang tidak ada orderan aktif, meow~</td></tr>";
                    }
                    while($row = $pesanan->fetch_assoc()):
                        $status_print = empty($row['status']) ? 'Menunggu Pembayaran' : $row['status'];
                        
                        $class_badge = 'status-menunggu';
                        if($row['status'] == 'Dibayar') $class_badge = 'status-dibayar';
                        elseif($row['status'] == 'Sedang Dimasak') $class_badge = 'status-masak';
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td><b>#<?= $row['id'] ?></b></td>
                        <td>Meja <?= $row['no_meja'] ?></td>
                        <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><b><?= $row['metode_pembayaran'] ?: 'Cash' ?></b></td>
                        <td><span class="<?= $class_badge ?>"><?= $status_print ?></span></td>
                        <td>
                            <?php if($status_print == 'Menunggu Pembayaran'): ?>
                                <a href="update_status.php?id=<?= $row['id'] ?>&status=Dibayar" class="btn-action btn-pay" style="background:#28a745; text-decoration:none; padding:6px 12px; color:white; border-radius:20px; font-size:12px; font-weight:bold;">💵 Konfirmasi Pembayaran</a>
                            <?php elseif($status_print == 'Dibayar'): ?>
                                <a href="update_status.php?id=<?= $row['id'] ?>&status=Sedang Dimasak" class="btn-action btn-cook" style="background:#17a2b8; text-decoration:none; padding:6px 12px; color:white; border-radius:20px; font-size:12px; font-weight:bold;">🍳 Mulai Masak</a>
                            <?php elseif($status_print == 'Sedang Dimasak'): ?>
                                <a href="update_status.php?id=<?= $row['id'] ?>&status=Selesai" class="btn-action btn-done" style="background:#6c757d; text-decoration:none; padding:6px 12px; color:white; border-radius:20px; font-size:12px; font-weight:bold;">✅ Selesai &amp; Sajikan</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 25px; background: #e2f0d9; border: 1px solid #a9d08e; padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h4 style="color: #2e75b6; margin-bottom:4px;">📊 Rekapitulasi Laporan Harian Spreadsheet</h4>
                <p style="font-size: 13px; color: #555;">Sistem mendeteksi penanggung jawab tanda tangan berkas: <b><?= htmlspecialchars($admin_aktif) ?></b></p>
            </div>
            <a href="rekap_excel.php" style="background: #28a745; color: white; padding: 10px 18px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size:14px;">📥 Download Excel</a>
        </div>
    </div>
</body>
</html>