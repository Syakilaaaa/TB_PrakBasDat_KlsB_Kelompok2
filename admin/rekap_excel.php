<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit;
}

$admin_aktif = $_SESSION['admin_nama'];
$hari_ini = date('Y-m-d');

$sql = "SELECT * FROM pesanan WHERE DATE(tanggal_pesan) = '$hari_ini' ORDER BY id ASC";
$result = $conn->query($sql);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Omset_MeowResto_".$hari_ini.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border="1">
    <tr>
        <th colspan="7" style="background-color: #FF8C42; color: white; font-weight: bold; text-align: center; height: 35px; font-size:15px;">LAPORAN REKAP OMSET PENJUALAN HARIAN - NYAMMEOW RESTO</th>
    </tr>
    <tr>
        <td colspan="3" style="height:25px;"><b>Tanggal Hari Ini:</b> <?= date('d-m-Y') ?></td>
        <td colspan="4" align="right"><b>Admin Kasir Penanggung Jawab Berjaga:</b> <?= htmlspecialchars($admin_aktif) ?></td>
    </tr>
    <tr style="background-color: #333333; color: white; font-weight: bold;">
        <th>ID Order</th>
        <th>No Meja</th>
        <th>Nama Pemesan</th>
        <th>Metode</th>
        <th>Status Akhir</th>
        <th>Dikonfirmasi Oleh</th>
        <th>Total Harga (IDR)</th>
    </tr>
    <?php 
    $total_omset = 0;
    while($row = $result->fetch_assoc()): 
        $total_omset += $row['total_harga'];
        $print_status = empty($row['status']) ? 'Menunggu Pembayaran' : $row['status'];
        $petugas_konfirmasi = empty($row['dikonfirmasi_oleh']) ? '-' : $row['dikonfirmasi_oleh'];
    ?>
    <tr>
        <td align="center">#<?= $row['id'] ?></td>
        <td align="center">Meja <?= $row['no_meja'] ?></td>
        <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
        <td align="center"><?= $row['metode_pembayaran'] ?: 'Cash' ?></td>
        <td align="center"><?= $print_status ?></td>
        <td align="center"><?= htmlspecialchars($petugas_konfirmasi) ?></td>
        <td align="right">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
    </tr>
    <?php endwhile; ?>
    <tr style="font-weight: bold; background-color: #f2f2f2; height:30px;">
        <td colspan="6" align="right">GRAND TOTAL ONDAPATAN OMSET SHIFT INI:</td>
        <td align="right" style="color: #28a745;">Rp <?= number_format($total_omset, 0, ',', '.') ?></td>
    </tr>
</table>