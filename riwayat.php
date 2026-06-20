<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NyamMeow - Riwayat Pesanan Saya</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .status-menunggu { background: #FF8C42; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .status-dibayar { background: #28a745; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .status-masak { background: #17a2b8; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .status-selesai { background: #6c757d; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .riwayat-container { background: white; border-radius: 20px; padding: 20px; overflow-x: auto; margin-top: 20px; }
        .riwayat-table { width: 100%; border-collapse: collapse; }
        .riwayat-table th, .riwayat-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .riwayat-table th { background: #FF8C42; color: white; }
        .btn-detail { background: #FF8C42; color: white; padding: 5px 12px; border-radius: 15px; text-decoration: none; font-size: 12px; }
        .search-box { 
            background: white; 
            padding: 20px; 
            border-radius: 15px; 
            margin-bottom: 20px;
            text-align: center;
        }
        .search-box input { 
            padding: 12px; 
            width: 70%; 
            max-width: 300px; 
            border-radius: 10px; 
            border: 1px solid #ddd;
            margin-right: 10px;
        }
        .search-box button {
            padding: 12px 24px;
            background: #FF8C42;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .info-text { color: #666; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐱 RIWAYAT PESANAN SAYA 🐱</h1>
            <p>Cek status pesanan Anda di sini</p>
        </div>

        <div class="nav">
            <a href="index.php">🏠 Menu</a>
            <a href="riwayat.php">📋 Riwayat Saya</a>
        </div>

        <?php if(isset($_GET['sukses'])): ?>
            <div class="alert alert-success">✅ Pesanan berhasil! Simpan nota untuk ditunjukkan ke kasir.</div>
        <?php endif; ?>

        <!-- Form Pencarian Nama -->
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="nama" placeholder="Masukkan nama pemesan" value="<?= isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : '' ?>" required>
                <button type="submit">🔍 Lihat Pesanan Saya</button>
            </form>
            <p class="info-text">💡 Masukkan nama yang Anda gunakan saat memesan</p>
        </div>

        <div class="riwayat-container">
            <?php
            $nama_pemesan = isset($_GET['nama']) ? $_GET['nama'] : '';
            
            if ($nama_pemesan == ''):
            ?>
                <p class="empty-riwayat">🐾 Masukkan nama Anda di atas untuk melihat riwayat pesanan.</p>
            <?php else:
                // Hanya tampilkan pesanan dengan nama yang sesuai
                $sql = "SELECT * FROM pesanan WHERE nama_pemesan LIKE '%" . $conn->real_escape_string($nama_pemesan) . "%' ORDER BY tanggal_pesan DESC";
                $result = $conn->query($sql);
                
                if($result->num_rows == 0):
            ?>
                <p class="empty-riwayat">🐾 Tidak ada pesanan dengan nama "<?= htmlspecialchars($nama_pemesan) ?>". Yuk pesan dulu!</p>
            <?php else: ?>
                <table class="riwayat-table" id="tabelRiwayat">
                    <thead>
                        <tr>
                            <th>ID</th><th>Meja</th><th>Total</th><th>Status</th><th>Metode</th><th>Waktu</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                            $status_class = '';
                            if($row['status'] == 'Menunggu Pembayaran') $status_class = 'status-menunggu';
                            elseif($row['status'] == 'Dibayar') $status_class = 'status-dibayar';
                            elseif($row['status'] == 'Sedang Dimasak') $status_class = 'status-masak';
                            else $status_class = 'status-selesai';
                        ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td>Meja <?= $row['no_meja'] ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><span class="<?= $status_class ?>"><?= $row['status'] ?></span></td>
                            <td><?= $row['metode_pembayaran'] ?? 'Cash' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                            <td>
                                <a href="nota_pesanan.php?id=<?= $row['id'] ?>" class="btn-detail">🐱 Lihat Nota</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; endif; ?>
        </div>
    </div>
</body>
</html>
