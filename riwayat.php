<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NyamMeow - Riwayat Pesanan</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Style tambahan khusus riwayat */
        .riwayat-container {
            background: white;
            border-radius: 20px;
            padding: 20px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .riwayat-table th,
        .riwayat-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .riwayat-table th {
            background: #FF8C42;
            color: white;
            font-weight: 600;
        }
        
        .riwayat-table tr:hover {
            background: #FFF5EB;
        }
        
        .status-diproses {
            background: #FFE0B5;
            color: #E6781A;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-selesai {
            background: #A8E6CF;
            color: #2D6A4F;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .btn-detail {
            background: #FF8C42;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: 0.3s;
            display: inline-block;
        }
        
        .btn-detail:hover {
            background: #E6781A;
        }
        
        .export-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-export {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        
        .btn-export:hover {
            background: #218838;
        }
        
        .btn-export-excel {
            background: #1D6F42;
        }
        
        .btn-export-copy {
            background: #17a2b8;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .empty-riwayat {
            text-align: center;
            padding: 50px;
            color: #999;
            background: white;
            border-radius: 20px;
        }
        
        @media (max-width: 768px) {
            .riwayat-table th,
            .riwayat-table td {
                padding: 8px 10px;
                font-size: 12px;
            }
            
            .btn-export {
                padding: 8px 15px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐱 RIWAYAT PESANAN NYAMMEOW 🐱</h1>
            <p>Lihat dan export pesanan Meow-mu di sini</p>
        </div>

        <div class="nav">
            <a href="index.php">🏠 Menu</a>
            <a href="riwayat.php">📋 Riwayat Pesanan</a>
        </div>

        <?php if(isset($_GET['sukses'])): ?>
            <div class="alert alert-success">✅ Pesanan berhasil disimpan meow!</div>
        <?php endif; ?>

        <div class="export-buttons">
            <button onclick="exportToExcel()" class="btn-export btn-export-excel">📊 Export ke Excel</button>
            <button onclick="copyToClipboard()" class="btn-export btn-export-copy">📋 Salin Tabel</button>
        </div>

        <div class="riwayat-container" id="riwayatTable">
            <?php
            $sql = "SELECT p.*, 
                           (SELECT SUM(subtotal) FROM detail_pesanan WHERE pesanan_id = p.id) as total_detail
                    FROM pesanan p 
                    ORDER BY p.tanggal_pesan DESC";
            $result = $conn->query($sql);
            
            if($result->num_rows == 0):
            ?>
                <p class="empty-riwayat">🐾 Belum ada pesanan meow~ Yuk pesan dulu!</p>
            <?php else: ?>
                <table class="riwayat-table" id="tabelRiwayat">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Meja</th>
                            <th>Pemesan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td>Meja <?= $row['no_meja'] ?></td>
                            <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td>
                                <span class="status-<?= strtolower($row['status']) ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars(substr($row['catatan'] ?? '-', 0, 50)) ?> 
                             <?= strlen($row['catatan'] ?? '') > 50 ? '...' : '' ?> 
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                            <td>
                                <a href="detail_pesanan.php?id=<?= $row['id'] ?>" class="btn-detail">🐱 Detail</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function exportToExcel() {
            let table = document.getElementById("tabelRiwayat");
            if (!table) {
                alert("Tidak ada data untuk di export!");
                return;
            }
            
            let html = table.outerHTML;
            let blob = new Blob([html], { type: "application/vnd.ms-excel" });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "riwayat_pesanan_nyammeow.xls";
            link.click();
            URL.revokeObjectURL(link.href);
        }

        function copyToClipboard() {
            let table = document.getElementById("tabelRiwayat");
            if (!table) {
                alert("Tidak ada data untuk di salin!");
                return;
            }
            
            let range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            
            try {
                document.execCommand('copy');
                alert("✅ Tabel berhasil disalin! Sekarang bisa di-paste ke Excel, Google Sheets, atau dokumen lain.");
            } catch(err) {
                alert("❌ Gagal menyalin. Silakan coba manual dengan seleksi tabel.");
            }
            
            window.getSelection().removeAllRanges();
        }
    </script>
</body>
</html>