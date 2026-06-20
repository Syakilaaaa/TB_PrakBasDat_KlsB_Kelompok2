<?php
include 'config/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM pesanan WHERE id = $id";
$result = $conn->query($sql);
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    header("Location: index.php");
    exit;
}

$sql_detail = "SELECT d.*, m.nama_menu, m.harga 
               FROM detail_pesanan d 
               JOIN menu m ON d.menu_id = m.id 
               WHERE d.pesanan_id = $id";
$detail_result = $conn->query($sql_detail);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NyamMeow - Nota Pesanan #<?= $id ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Courier New', monospace;
            padding: 20px;
        }
        .nota {
            max-width: 400px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .nota-header {
            background: #FF8C42;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .nota-header h1 { font-size: 24px; margin-bottom: 5px; }
        .nota-body { padding: 20px; }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .divider { border-top: 1px dashed #ccc; margin: 15px 0; }
        .items-table { width: 100%; margin: 15px 0; }
        .items-table td { padding: 6px 0; font-size: 13px; }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #FF8C42;
        }
        .instruction {
            background: #FFF5EB;
            padding: 15px;
            border-radius: 12px;
            margin: 15px 0;
            text-align: center;
            font-size: 13px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-primary { background: #FF8C42; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .footer {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            font-size: 11px;
        }
        @media print {
            body { background: white; }
            .no-print { display: none; }
            .nota { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="nota">
        <div class="nota-header">
            <h1>🐱 NYAMMEOW 🐱</h1>
            <p>NOTA PESANAN #<?= $id ?></p>
        </div>
        
        <div class="nota-body">
            <div class="info-row">
                <span>📅 Tanggal:</span>
                <span><?= date('d/m/Y H:i:s', strtotime($pesanan['tanggal_pesan'])) ?></span>
            </div>
            <div class="info-row">
                <span>🪑 Meja:</span>
                <span>Meja <?= $pesanan['no_meja'] ?></span>
            </div>
            <div class="info-row">
                <span>😺 Pemesan:</span>
                <span><?= htmlspecialchars($pesanan['nama_pemesan']) ?></span>
            </div>
            
            <div class="divider"></div>
            
            <table class="items-table">
                <?php 
                $total = 0;
                while($item = $detail_result->fetch_assoc()): 
                    $total += $item['subtotal'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama_menu']) ?> x<?= $item['jumlah'] ?></td>
                    <td align="right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            
            <div class="total-row">
                <span>TOTAL</span>
                <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            
            <div class="instruction">
                <strong>📌 INSTRUKSI:</strong><br><br>
                <?php if($pesanan['metode_pembayaran'] == 'Cash'): ?>
                    💵 <strong>Metode: CASH</strong><br>
                    ➡️ <strong>Tunjukkan NOTA ini ke KASIR</strong> untuk membayar<br>
                    ➡️ Setelah bayar, pesanan akan dimasak<br>
                    ➡️ Pesanan akan diantar ke meja Anda
                <?php else: ?>
                    📱 <strong>Metode: QRIS</strong><br>
                    ➡️ <strong>Scan QRIS</strong> yang tersedia di kasir<br>
                    ➡️ Setelah scan, pesanan akan diproses
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin-top:12px;">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=NyamMeow-Lunas-<?= $id ?>" alt="QRIS"><br>
    <small style="color: grey; font-size:10px;">SIMULASI SISTEM QRIS OK</small>
</div>
            
            <div class="divider"></div>
            <div style="text-align: center; font-size: 12px;">
                <p>🐾 Simpan nota ini sebagai bukti pesanan 🐾</p>
            </div>
        </div>
        
        <div class="footer no-print">
            <div class="btn-group">
                <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak Nota</button>
                <a href="index.php" class="btn btn-secondary">🏠 Kembali</a>
            </div>
        </div>
    </div>
</body>
</html>
