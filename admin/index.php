<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login.php");
    exit;
}
include '../config/db.php';

// Tampilkan pesan jika ada
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);

// Konfirmasi hapus
$confirm_delete = isset($_GET['confirm_delete']) ? (int)$_GET['confirm_delete'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir - NyamMeow</title>
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
        .stats {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 20px; margin-bottom: 30px;
        }
        .stat-card {
            background: white; padding: 20px; border-radius: 15px;
            text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-number { font-size: 32px; font-weight: bold; color: #FF8C42; }
        table {
            width: 100%; background: white; border-radius: 15px;
            overflow: hidden; border-collapse: collapse;
        }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #FF8C42; color: white; }
        tr:hover { background: #f9f9f9; }
        .btn-status {
            padding: 6px 12px; border: none; border-radius: 10px;
            cursor: pointer; font-size: 12px; text-decoration: none;
            display: inline-block; margin: 2px;
        }
        .btn-bayar { background: #28a745; color: white; }
        .btn-masak { background: #17a2b8; color: white; }
        .btn-selesai { background: #ffc107; color: #333; }
        .filter { margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-btn {
            padding: 8px 20px; border: none; border-radius: 20px;
            cursor: pointer; background: #e9ecef;
        }
        .filter-btn.active { background: #FF8C42; color: white; }
        .status-menunggu { background: #FF8C42; color: white; padding: 4px 8px; border-radius: 10px; font-size: 11px; }
        .status-dibayar { background: #28a745; color: white; padding: 4px 8px; border-radius: 10px; font-size: 11px; }
        .status-masak { background: #17a2b8; color: white; padding: 4px 8px; border-radius: 10px; font-size: 11px; }
        .status-selesai { background: #6c757d; color: white; padding: 4px 8px; border-radius: 10px; font-size: 11px; }
        .search-box { margin-bottom: 20px; }
        .search-box input { padding: 10px; width: 300px; border-radius: 10px; border: 1px solid #ddd; }

        /* Notifikasi */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease;
            max-width: 400px;
        }
        .notification.success { background: #28a745; }
        .notification.error { background: #dc3545; }
        .notification.info { background: #17a2b8; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .notification-close {
            float: right;
            cursor: pointer;
            margin-left: 15px;
            font-weight: bold;
        }

        /* Modal Konfirmasi */
        .modal {
            display: none;
            position: fixed;
            z-index: 9998;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            max-width: 450px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            text-align: center;
            animation: slideDown 0.3s;
        }
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-icon { font-size: 50px; margin-bottom: 15px; }
        .modal h3 { margin-bottom: 10px; color: #333; }
        .modal p { margin-bottom: 20px; color: #666; }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .modal-btn {
            padding: 10px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }
        .modal-btn-danger { background: #dc3545; color: white; }
        .modal-btn-danger:hover { background: #c82333; }
        .modal-btn-secondary { background: #6c757d; color: white; }
        .modal-btn-secondary:hover { background: #5a6268; }

        /* Tombol Hapus di tabel */
        .btn-hapus {
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 11px;
            text-decoration: none;
            display: inline-block;
            margin-top: 3px;
        }
        .btn-hapus:hover { background: #c82333; }

        /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        #exportDropdown {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 220px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 10px;
            padding: 10px;
            z-index: 1000;
        }
        #exportDropdown a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: background 0.3s;
        }
        #exportDropdown a:hover {
            background-color: #f1f1f1;
        }
        #exportDropdown a[style*="color: #dc3545"]:hover {
            background-color: #ffe5e5;
        }
        #exportDropdown .divider {
            border-top: 1px solid #eee;
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Tampilkan notifikasi -->
    <?php if($message): ?>
    <div class="notification <?= $message_type ?>">
        <span class="notification-close" onclick="this.parentElement.remove()">✖</span>
        <?= $message ?>
    </div>
    <?php endif; ?>

    <!-- Modal Konfirmasi Hapus -->
    <?php if($confirm_delete > 0): 
        $pesanan_delete = $conn->query("SELECT * FROM pesanan WHERE id = $confirm_delete")->fetch_assoc();
        if($pesanan_delete):
    ?>
    <div id="deleteModal" class="modal" style="display: block;">
        <div class="modal-content">
            <div class="modal-icon">⚠️</div>
            <h3>Konfirmasi Hapus Pesanan</h3>
            <p>
                Apakah Anda yakin ingin menghapus pesanan ini?<br>
                <strong>ID: #<?= $pesanan_delete['id'] ?></strong><br>
                <strong>Pemesan: <?= htmlspecialchars($pesanan_delete['nama_pemesan']) ?></strong><br>
                <strong>Total: Rp <?= number_format($pesanan_delete['total_harga'], 0, ',', '.') ?></strong><br>
                <strong>Status: <?= $pesanan_delete['status'] ?></strong>
            </p>
            <p style="color: #dc3545; font-size: 12px;">
                ⚠️ Tindakan ini tidak dapat dibatalkan!
            </p>
            <div class="modal-buttons">
                <a href="hapus_pesanan.php?id=<?= $confirm_delete ?>&confirm=yes" class="modal-btn modal-btn-danger">✅ Ya, Hapus</a>
                <a href="index.php" class="modal-btn modal-btn-secondary">❌ Batal</a>
            </div>
        </div>
    </div>
    <?php endif; endif; ?>

    <!-- Header -->
    <div class="header">
        <h2>🐱 Dashboard Kasir NyamMeow</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <div class="dropdown">
                <button onclick="toggleDropdown()" style="background: white; color: #FF8C42; border: none; padding: 8px 15px; border-radius: 10px; cursor: pointer; font-weight: bold;">
                    📊 Export Laporan
                </button>
                <div id="exportDropdown">
                    <div style="font-weight: bold; padding: 5px 12px; color: #666; font-size: 12px; border-bottom: 1px solid #eee; margin-bottom: 5px;">
                        📋 Export Data
                    </div>
                    <a href="export_excel.php">📋 Export Semua Data</a>
                    <a href="export_excel.php?status=Menunggu Pembayaran">⏳ Export Menunggu Bayar</a>
                    <a href="export_excel.php?status=Dibayar">✅ Export Dibayar</a>
                    <a href="export_excel.php?status=Sedang Dimasak">🍳 Export Dimasak</a>
                    <a href="export_excel.php?status=Selesai">🎉 Export Selesai</a>
                    
                    <div style="border-top: 1px solid #eee; margin: 5px 0;"></div>
                    
                    <div style="font-weight: bold; padding: 5px 12px; color: #dc3545; font-size: 12px;">
                        ⚠️ Export & Hapus Data
                    </div>
                    <a href="export_excel.php?delete=true" style="color: #dc3545; background: #fff5f5;">🗑️ Export & Hapus Semua</a>
                    <a href="export_excel.php?status=Menunggu Pembayaran&delete=true" style="color: #dc3545; background: #fff5f5;">🗑️ Export & Hapus Menunggu Bayar</a>
                    <a href="export_excel.php?status=Selesai&delete=true" style="color: #dc3545; background: #fff5f5;">🗑️ Export & Hapus Selesai</a>
                    
                    <div style="border-top: 1px solid #eee; margin: 5px 0;"></div>
                    
                    <label style="display: block; padding: 8px 12px; font-size: 12px; color: #666;">
                        📅 Export Tanggal:
                        <input type="date" id="exportDate" style="display: block; margin-top: 5px; padding: 5px; border: 1px solid #ddd; border-radius: 5px; width: 100%;">
                        <div style="display: flex; gap: 5px; margin-top: 5px;">
                            <button onclick="exportByDate(false)" style="flex:1; background: #28a745; color: white; border: none; padding: 5px; border-radius: 5px; cursor: pointer; font-size: 11px;">
                                📥 Export
                            </button>
                            <button onclick="exportByDate(true)" style="flex:1; background: #dc3545; color: white; border: none; padding: 5px; border-radius: 5px; cursor: pointer; font-size: 11px;">
                                🗑️ Export & Hapus
                            </button>
                        </div>
                    </label>
                </div>
            </div>
            <a href="riwayat.php" style="color: white; text-decoration: none; padding: 8px 15px; background: rgba(255,255,255,0.2); border-radius: 10px;">
                📋 Riwayat
            </a>
            <a href="logout.php" class="logout">🚪 Logout</a>
        </div>
    </div>

    <?php
    $menunggu = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Menunggu Pembayaran'")->fetch_assoc()['count'];
    $dibayar = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Dibayar'")->fetch_assoc()['count'];
    $masak = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Sedang Dimasak'")->fetch_assoc()['count'];
    $selesai = $conn->query("SELECT COUNT(*) as count FROM pesanan WHERE status = 'Selesai'")->fetch_assoc()['count'];
    ?>

    <div class="stats">
        <div class="stat-card"><h3>⏳ Menunggu Bayar</h3><div class="stat-number"><?= $menunggu ?></div></div>
        <div class="stat-card"><h3>✅ Sudah Dibayar</h3><div class="stat-number"><?= $dibayar ?></div></div>
        <div class="stat-card"><h3>🍳 Sedang Dimasak</h3><div class="stat-number"><?= $masak ?></div></div>
        <div class="stat-card"><h3>🎉 Selesai</h3><div class="stat-number"><?= $selesai ?></div></div>
    </div>

    <div class="search-box">
        <input type="text" id="search" placeholder="🔍 Cari berdasarkan nama atau ID..." onkeyup="cariPesanan()">
    </div>

    <div class="filter">
        <button class="filter-btn active" data-filter="all">Semua</button>
        <button class="filter-btn" data-filter="Menunggu Pembayaran">⏳ Menunggu Bayar</button>
        <button class="filter-btn" data-filter="Dibayar">✅ Dibayar</button>
        <button class="filter-btn" data-filter="Sedang Dimasak">🍳 Dimasak</button>
        <button class="filter-btn" data-filter="Selesai">🎉 Selesai</button>
    </div>

    <table id="tabelPesanan">
        <thead>
            <tr><th>ID</th><th>Meja</th><th>Pemesan</th><th>Total</th><th>Metode</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php
            $pesanan = $conn->query("SELECT * FROM pesanan ORDER BY FIELD(status, 'Menunggu Pembayaran', 'Dibayar', 'Sedang Dimasak', 'Selesai'), tanggal_pesan DESC");
            if ($pesanan && $pesanan->num_rows > 0):
                while($row = $pesanan->fetch_assoc()):
                    $status_class = '';
                    if($row['status'] == 'Menunggu Pembayaran') $status_class = 'status-menunggu';
                    elseif($row['status'] == 'Dibayar') $status_class = 'status-dibayar';
                    elseif($row['status'] == 'Sedang Dimasak') $status_class = 'status-masak';
                    else $status_class = 'status-selesai';
            ?>
            <tr data-status="<?= $row['status'] ?>">
                <td>#<?= $row['id'] ?></td>
                <td>Meja <?= $row['no_meja'] ?></td>
                <td><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                <td><?= $row['metode_pembayaran'] ?? 'Cash' ?></td>
                <td><span class="<?= $status_class ?>"><?= $row['status'] ?></span></td>
                <td><?= date('H:i:s d/m', strtotime($row['tanggal_pesan'])) ?></td>
                <td>
                    <?php if($row['status'] == 'Menunggu Pembayaran'): ?>
                        <a href="update_status.php?id=<?= $row['id'] ?>&status=Dibayar" class="btn-status btn-bayar">💰 Konfirmasi Bayar</a>
                    <?php elseif($row['status'] == 'Dibayar'): ?>
                        <a href="update_status.php?id=<?= $row['id'] ?>&status=Sedang Dimasak" class="btn-status btn-masak">🍳 Proses Masak</a>
                    <?php elseif($row['status'] == 'Sedang Dimasak'): ?>
                        <a href="update_status.php?id=<?= $row['id'] ?>&status=Selesai" class="btn-status btn-selesai">✅ Selesai</a>
                    <?php else: ?>
                        <span style="color:#999;">-</span>
                    <?php endif; ?>
                    <br>
                    <a href="../nota_pesanan.php?id=<?= $row['id'] ?>" style="font-size: 11px;">📋 Nota</a>
                    <a href="index.php?confirm_delete=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Yakin hapus pesanan #<?= $row['id'] ?>?')">🗑️ Hapus</a>
                </td>
            </tr>
            <?php 
                endwhile;
            else: 
            ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                    🐾 Belum ada pesanan nih, meow~
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Fungsi toggle dropdown
function toggleDropdown() {
    var dropdown = document.getElementById("exportDropdown");
    if (dropdown.style.display === "none" || dropdown.style.display === "") {
        dropdown.style.display = "block";
    } else {
        dropdown.style.display = "none";
    }
}

// Fungsi export by date
function exportByDate(deleteAfter) {
    var date = document.getElementById("exportDate").value;
    if (date) {
        var url = "export_excel.php?date=" + date;
        if (deleteAfter) {
            url += "&delete=true";
            if (!confirm("⚠️ Data pesanan tanggal " + date + " akan DIHAPUS setelah export. Lanjutkan?")) {
                return;
            }
        }
        window.location.href = url;
    } else {
        alert("Pilih tanggal terlebih dahulu!");
    }
}

// Tutup dropdown jika klik di luar
window.onclick = function(event) {
    if (!event.target.matches('.dropdown button') && !event.target.closest('.dropdown')) {
        var dropdown = document.getElementById("exportDropdown");
        if (dropdown && dropdown.style.display === "block") {
            dropdown.style.display = "none";
        }
    }
}

// Fungsi cari pesanan
function cariPesanan() {
    let input = document.getElementById('search').value.toLowerCase();
    let rows = document.querySelectorAll('#tabelPesanan tbody tr');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}

// Filter
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        let filter = btn.dataset.filter;
        let rows = document.querySelectorAll('#tabelPesanan tbody tr');
        rows.forEach(row => {
            if(filter === 'all' || row.dataset.status === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

// ===== FITUR AUTO UPDATE STATUS =====
let previousStatus = {};

function updateStatus() {
    fetch('get_status_update.php')
        .then(response => response.json())
        .then(data => {
            // Update statistik
            document.querySelectorAll('.stat-number')[0].textContent = data.menunggu;
            document.querySelectorAll('.stat-number')[1].textContent = data.dibayar;
            document.querySelectorAll('.stat-number')[2].textContent = data.masak;
            document.querySelectorAll('.stat-number')[3].textContent = data.selesai;
            
            // Update tabel
            updateTable(data.pesanan);
            
            // Cek perubahan status untuk notifikasi
            data.pesanan.forEach(order => {
                if (previousStatus[order.id] && previousStatus[order.id] !== order.status) {
                    showNotification(`Status Pesanan #${order.id}`, 
                                   `Berubah dari "${previousStatus[order.id]}" menjadi "${order.status}"`);
                }
                previousStatus[order.id] = order.status;
            });
        })
        .catch(error => console.log('Update error:', error));
}

function updateTable(pesanan) {
    let filter = document.querySelector('.filter-btn.active')?.dataset?.filter || 'all';
    let search = document.getElementById('search')?.value?.toLowerCase() || '';
    
    let tbody = document.querySelector('#tabelPesanan tbody');
    tbody.innerHTML = '';
    
    if (pesanan.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 40px; color: #999;">🐾 Belum ada pesanan nih, meow~</td></tr>`;
        return;
    }
    
    pesanan.forEach(row => {
        if (filter !== 'all' && row.status !== filter) return;
        if (search && !row.nama_pemesan.toLowerCase().includes(search) && 
            !row.id.toString().includes(search)) return;
        
        let statusClass = getStatusClass(row.status);
        let actions = getActions(row);
        
        let tr = document.createElement('tr');
        tr.dataset.status = row.status;
        tr.innerHTML = `
            <td>#${row.id}</td>
            <td>Meja ${row.no_meja}</td>
            <td>${escapeHtml(row.nama_pemesan)}</td>
            <td>Rp ${Number(row.total_harga).toLocaleString('id-ID')}</td>
            <td>${row.metode_pembayaran || 'Cash'}</td>
            <td><span class="${statusClass}">${row.status}</span></td>
            <td>${formatDate(row.tanggal_pesan)}</td>
            <td>${actions}</td>
        `;
        tbody.appendChild(tr);
    });
}

function getStatusClass(status) {
    if (status === 'Menunggu Pembayaran') return 'status-menunggu';
    if (status === 'Dibayar') return 'status-dibayar';
    if (status === 'Sedang Dimasak') return 'status-masak';
    return 'status-selesai';
}

function getActions(row) {
    let actions = '';
    if (row.status === 'Menunggu Pembayaran') {
        actions = `<a href="update_status.php?id=${row.id}&status=Dibayar" class="btn-status btn-bayar">💰 Konfirmasi Bayar</a>`;
    } else if (row.status === 'Dibayar') {
        actions = `<a href="update_status.php?id=${row.id}&status=Sedang Dimasak" class="btn-status btn-masak">🍳 Proses Masak</a>`;
    } else if (row.status === 'Sedang Dimasak') {
        actions = `<a href="update_status.php?id=${row.id}&status=Selesai" class="btn-status btn-selesai">✅ Selesai</a>`;
    } else {
        actions = `<span style="color:#999;">-</span>`;
    }
    actions += `<br><a href="../nota_pesanan.php?id=${row.id}" style="font-size: 11px;">📋 Nota</a>`;
    actions += ` <a href="index.php?confirm_delete=${row.id}" class="btn-hapus" onclick="return confirm('Yakin hapus pesanan #${row.id}?')">🗑️ Hapus</a>`;
    return actions;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    let date = new Date(dateStr);
    return date.toLocaleTimeString('id-ID') + ' ' + date.toLocaleDateString('id-ID');
}

function showNotification(title, body) {
    if (Notification.permission === "granted") {
        new Notification(title, {
            body: body,
            icon: '🐱'
        });
    }
}

// Auto update setiap 5 detik
setInterval(updateStatus, 5000);

// Update pertama kali
updateStatus();

// Minta izin notifikasi
if (Notification.permission === "default") {
    Notification.requestPermission();
}
</script>
</body>
</html>
