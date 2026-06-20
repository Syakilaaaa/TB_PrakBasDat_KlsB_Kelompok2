<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login.php");
    exit;
}
include '../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan - NyamMeow</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header {
            background: #FF8C42; color: white; padding: 20px;
            border-radius: 15px; margin-bottom: 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .logout { background: #dc3545; color: white; padding: 8px 15px; border-radius: 10px; text-decoration: none; }
        table {
            width: 100%; background: white; border-radius: 15px;
            overflow: hidden; border-collapse: collapse;
        }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #FF8C42; color: white; }
        tr:hover { background: #f9f9f9; }
        .btn-export {
            background: #28a745; color: white; padding: 10px 20px;
            border: none; border-radius: 10px; cursor: pointer;
            text-decoration: none; display: inline-block;
        }
        .btn-export:hover { background: #218838; }
        .filter-box { 
            background: white; padding: 20px; border-radius: 15px;
            margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap;
            align-items: center;
        }
        .filter-box select, .filter-box input {
            padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd;
        }
        .filter-box button {
            padding: 8px 20px; background: #FF8C42; color: white;
            border: none; border-radius: 8px; cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>📋 Riwayat Pesanan NyamMeow</h2>
        <div>
            <a href="export_excel.php" class="btn-export">📊 Export Excel</a>
            <a href="index.php" style="color: white; margin: 0 10px;">🏠 Dashboard</a>
            <a href="logout.php" class="logout">🚪 Logout</a>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-box">
        <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
            <select name="status">
                <option value="">Semua Status</option>
                <option value="Menunggu Pembayaran" <?= isset($_GET['status']) && $_GET['status'] == 'Menunggu Pembayaran' ? 'selected' : '' ?>>⏳ Menunggu Bayar</option>
                <option value="Dibayar" <?= isset($_GET['status']) && $_GET['status'] == 'Dibayar' ? 'selected' : '' ?>>✅ Dibayar</option>
                <option value="Sedang Dimasak" <?= isset($_GET['status']) && $_GET['status'] == 'Sedang Dimasak' ? 'selected' : '' ?>>🍳 Dimasak</option>
                <option value="Selesai" <?= isset($_GET['status']) && $_GET['status'] == 'Selesai' ? 'selected' : '' ?>>🎉 Selesai</option>
            </select>
            <input type="date" name="date" value="<?= isset($_GET['date']) ? $_GET['date'] : '' ?>">
            <input type="text" name="search" placeholder="Cari nama atau ID..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <button type="submit">🔍 Filter</button>
            <a href="riwayat.php" style="padding: 8px 20px; background: #6c757d; color: white; border-radius: 8px; text-decoration: none;">Reset</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Meja</th>
                <th>Pemesan</th>
                <th>Total</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM pesanan WHERE 1=1";
            
            if(isset($_GET['status']) && $_GET['status'] != '') {
                $sql .= " AND status = '" . $conn->real_escape_string($_GET['status']) . "'";
            }
            if(isset($_GET['date']) && $_GET['date'] != '') {
                $sql .= " AND DATE(tanggal_pesan) = '" . $conn->real_escape_string($_GET['date']) . "'";
            }
            if(isset($_GET['search']) && $_GET['search'] != '') {
                $search = $conn->real_escape_string($_GET['search']);
                $sql .= " AND (nama_pemesan LIKE '%$search%' OR id LIKE '%$search%')";
            }
            
            $sql .= " ORDER BY tanggal_pesan DESC";
            $result = $conn->query($sql);
            
            if($result->num_rows == 0):
            ?>
            <tr><td colspan="8" style="text-align: center; padding: 40px;">🐾 Belum ada pesanan</td></tr>
            <?php else: ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td>Meja <?= $row['no_meja'] ?></td>
                    <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                    <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                    <td><?= $row['metode_pembayaran'] ?? 'Cash' ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                    <td>
                        <a href="../nota_pesanan.php?id=<?= $row['id'] ?>" style="color: #FF8C42;">📋 Nota</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>