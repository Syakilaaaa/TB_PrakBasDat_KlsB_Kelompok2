<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

// Set header untuk download file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=riwayat_pesanan_" . date('Y-m-d_H-i-s') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Fungsi untuk menulis cell dengan style
function writeCell($data, $bold = false, $bgColor = '') {
    $style = '';
    if ($bold) $style .= 'font-weight:bold;';
    if ($bgColor) $style .= 'background-color:' . $bgColor . ';';
    echo '<td style="' . $style . 'border:1px solid #ddd;padding:8px;">' . htmlspecialchars($data) . '</td>';
}

// Ambil filter status jika ada
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Buat query
$sql = "SELECT * FROM pesanan WHERE 1=1";
if ($status_filter) {
    $sql .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
}
if ($date_filter) {
    $sql .= " AND DATE(tanggal_pesan) = '" . $conn->real_escape_string($date_filter) . "'";
}
$sql .= " ORDER BY tanggal_pesan DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Riwayat Pesanan</title>
    <style>
        /* Style untuk Excel */
        table {
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 11px;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #FF8C42;
            color: white;
            font-weight: bold;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 15px 0;
            background-color: #FFF5EB;
        }
        .footer {
            text-align: center;
            font-style: italic;
            padding: 10px 0;
            background-color: #f5f5f5;
            margin-top: 10px;
        }
        .status-menunggu { background-color: #FF8C42; color: white; padding: 2px 6px; border-radius: 3px; }
        .status-dibayar { background-color: #28a745; color: white; padding: 2px 6px; border-radius: 3px; }
        .status-masak { background-color: #17a2b8; color: white; padding: 2px 6px; border-radius: 3px; }
        .status-selesai { background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; }
        .summary {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <table>
        <!-- Header -->
        <tr>
            <td colspan="9" class="header-title">
                🐱 NYAMMEOW - LAPORAN RIWAYAT PESANAN<br>
                <span style="font-size: 14px; font-weight: normal;">
                    Tanggal Export: <?= date('d/m/Y H:i:s') ?>
                    <?php if($status_filter): ?>
                        | Status: <?= $status_filter ?>
                    <?php endif; ?>
                    <?php if($date_filter): ?>
                        | Tanggal: <?= date('d/m/Y', strtotime($date_filter)) ?>
                    <?php endif; ?>
                </span>
            </td>
        </tr>
        
        <!-- Summary -->
        <tr>
            <td colspan="9" class="summary">
                <strong>📊 RINGKASAN:</strong><br>
                <?php
                $total_pesanan = $result->num_rows;
                $total_pendapatan = 0;
                $status_count = [
                    'Menunggu Pembayaran' => 0,
                    'Dibayar' => 0,
                    'Sedang Dimasak' => 0,
                    'Selesai' => 0
                ];
                
                // Reset pointer
                $result->data_seek(0);
                while($row = $result->fetch_assoc()) {
                    $total_pendapatan += $row['total_harga'];
                    if(isset($status_count[$row['status']])) {
                        $status_count[$row['status']]++;
                    }
                }
                $result->data_seek(0);
                ?>
                Total Pesanan: <?= $total_pesanan ?> pesanan<br>
                Total Pendapatan: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?><br>
                ⏳ Menunggu Pembayaran: <?= $status_count['Menunggu Pembayaran'] ?> | 
                ✅ Dibayar: <?= $status_count['Dibayar'] ?> | 
                🍳 Dimasak: <?= $status_count['Sedang Dimasak'] ?> | 
                🎉 Selesai: <?= $status_count['Selesai'] ?>
            </td>
        </tr>
        
        <!-- Table Header -->
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pesanan</th>
                <th>Meja</th>
                <th>Nama Pemesan</th>
                <th>Total Harga</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th>Catatan</th>
                <th>Tanggal Pesan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($row = $result->fetch_assoc()): 
                $status_label = $row['status'];
                $status_class = 'status-' . strtolower(str_replace(' ', '-', $row['status']));
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>#<?= $row['id'] ?></td>
                <td><?= $row['no_meja'] ?></td>
                <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                <td style="text-align: right;">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                <td><?= $row['metode_pembayaran'] ?? 'Cash' ?></td>
                <td>
                    <span style="<?php
                        if($row['status'] == 'Menunggu Pembayaran') echo 'background-color:#FF8C42;color:white;';
                        elseif($row['status'] == 'Dibayar') echo 'background-color:#28a745;color:white;';
                        elseif($row['status'] == 'Sedang Dimasak') echo 'background-color:#17a2b8;color:white;';
                        else echo 'background-color:#6c757d;color:white;';
                    ?>padding:2px 6px;border-radius:3px;">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['catatan'] ?? '-') ?></td>
                <td><?= date('d/m/Y H:i:s', strtotime($row['tanggal_pesan'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
        
        <!-- Footer with total -->
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        
        <!-- Footer -->
        <tr>
            <td colspan="9" class="footer">
                Laporan ini dihasilkan secara otomatis oleh NyamMeow System<br>
                <?= date('d/m/Y H:i:s') ?>
            </td>
        </tr>
    </table>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

// Cek apakah ingin hapus setelah export
$delete_after_export = isset($_GET['delete']) && $_GET['delete'] == 'true';

// Set header untuk download file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=riwayat_pesanan_" . date('Y-m-d_H-i-s') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Query
$sql = "SELECT * FROM pesanan WHERE 1=1";
if ($status_filter) {
    $sql .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
}
if ($date_filter) {
    $sql .= " AND DATE(tanggal_pesan) = '" . $conn->real_escape_string($date_filter) . "'";
}
$sql .= " ORDER BY tanggal_pesan DESC";

$result = $conn->query($sql);

// Simpan ID pesanan yang akan dihapus
$ids_to_delete = [];
$total_pendapatan = 0;
$status_count = [
    'Menunggu Pembayaran' => 0,
    'Dibayar' => 0,
    'Sedang Dimasak' => 0,
    'Selesai' => 0
];

// Kumpulkan data
$data_pesanan = [];
while($row = $result->fetch_assoc()) {
    $data_pesanan[] = $row;
    $ids_to_delete[] = $row['id'];
    $total_pendapatan += $row['total_harga'];
    if(isset($status_count[$row['status']])) {
        $status_count[$row['status']]++;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Riwayat Pesanan</title>
    <style>
        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11px; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #FF8C42; color: white; font-weight: bold; }
        .header-title { font-size: 18px; font-weight: bold; text-align: center; padding: 15px 0; background-color: #FFF5EB; }
        .footer { text-align: center; font-style: italic; padding: 10px 0; background-color: #f5f5f5; margin-top: 10px; }
        .summary { margin: 15px 0; padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="9" class="header-title">
                🐱 NYAMMEOW - LAPORAN RIWAYAT PESANAN<br>
                <span style="font-size: 14px; font-weight: normal;">
                    Tanggal Export: <?= date('d/m/Y H:i:s') ?>
                    <?php if($status_filter): ?>
                        | Status: <?= $status_filter ?>
                    <?php endif; ?>
                    <?php if($date_filter): ?>
                        | Tanggal: <?= date('d/m/Y', strtotime($date_filter)) ?>
                    <?php endif; ?>
                    <?php if($delete_after_export): ?>
                        | ⚠️ DATA AKAN DIHAPUS SETELAH EXPORT
                    <?php endif; ?>
                </span>
            </td>
        </tr>
        
        <tr>
            <td colspan="9" class="summary">
                <strong>📊 RINGKASAN:</strong><br>
                Total Pesanan: <?= count($data_pesanan) ?> pesanan<br>
                Total Pendapatan: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?><br>
                ⏳ Menunggu Pembayaran: <?= $status_count['Menunggu Pembayaran'] ?> | 
                ✅ Dibayar: <?= $status_count['Dibayar'] ?> | 
                🍳 Dimasak: <?= $status_count['Sedang Dimasak'] ?> | 
                🎉 Selesai: <?= $status_count['Selesai'] ?>
            </td>
        </tr>
        
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pesanan</th>
                <th>Meja</th>
                <th>Nama Pemesan</th>
                <th>Total Harga</th>
                <th>Metode Bayar</th>
                <th>Status</th>
                <th>Catatan</th>
                <th>Tanggal Pesan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($data_pesanan as $row): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>#<?= $row['id'] ?></td>
                <td><?= $row['no_meja'] ?></td>
                <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                <td style="text-align: right;">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                <td><?= $row['metode_pembayaran'] ?? 'Cash' ?></td>
                <td>
                    <span style="<?php
                        if($row['status'] == 'Menunggu Pembayaran') echo 'background-color:#FF8C42;color:white;';
                        elseif($row['status'] == 'Dibayar') echo 'background-color:#28a745;color:white;';
                        elseif($row['status'] == 'Sedang Dimasak') echo 'background-color:#17a2b8;color:white;';
                        else echo 'background-color:#6c757d;color:white;';
                    ?>padding:2px 6px;border-radius:3px;">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['catatan'] ?? '-') ?></td>
                <td><?= date('d/m/Y H:i:s', strtotime($row['tanggal_pesan'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td style="text-align: right;">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        
        <tr>
            <td colspan="9" class="footer">
                Laporan ini dihasilkan secara otomatis oleh NyamMeow System<br>
                <?= date('d/m/Y H:i:s') ?>
                <?php if($delete_after_export): ?>
                    <br>⚠️ Data pesanan telah dihapus dari sistem setelah export
                <?php endif; ?>
            </td>
        </tr>
    </table>
</body>
</html>

<?php
// Jika delete_after_export true, hapus data setelah export
if ($delete_after_export && !empty($ids_to_delete)) {
    $conn->begin_transaction();
    try {
        foreach ($ids_to_delete as $id) {
            // Hapus detail
            $conn->query("DELETE FROM detail_pesanan WHERE pesanan_id = $id");
            // Hapus pesanan
            $conn->query("DELETE FROM pesanan WHERE id = $id");
        }
        $conn->commit();
        
        // Set session message
        session_start();
        $_SESSION['message'] = "✅ " . count($ids_to_delete) . " pesanan berhasil diexport dan dihapus dari sistem!";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        $conn->rollback();
        session_start();
        $_SESSION['message'] = "❌ Gagal menghapus data: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
}
?>
