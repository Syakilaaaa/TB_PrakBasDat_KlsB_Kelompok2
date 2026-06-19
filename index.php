<?php include 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NyamMeow - Restoran Pecinta Kucing</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐱 NYAMMEOW 🐱</h1>
            <p>Restoran untuk pecinta kucing | Makanan lezat untuk Meow-mu!</p>
        </div>

        <div class="nav">
            <a href="index.php" class="active">🏠 Menu</a>
            <a href="riwayat.php">📋 Riwayat Pesanan</a>
        </div>

        <div class="meja-section">
            <form id="form-pemesanan-meow">
                <div class="form-group">
                    <label>🐾 Nomor Meja:</label>
                    <select id="no_meja">
                        <?php for($i=1; $i<=10; $i++): ?>
                            <option value="<?= $i ?>">Meja <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>🐱 Nama Pemesan:</label>
                    <input type="text" id="nama_pemesan" placeholder="Contoh: Meowmy, Pawpaw" required>
                </div>
                <div class="form-group">
                    <label>📝 Catatan Tambahan:</label>
                    <input type="text" id="catatan" placeholder="Contoh: Pedas, Tanpa es batu">
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 8px;">💳 Metode Pembayaran:</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="cursor: pointer;"><input type="radio" name="metode_pembayaran" value="Cash" checked> 💵 Uang Tunai (Cash)</label>
                        <label style="cursor: pointer;"><input type="radio" name="metode_pembayaran" value="QRIS"> 📱 QRIS (E-Wallet)</label>
                    </div>
                </div>
                <button type="button" id="btn-konfirmasi" class="btn-action btn-pay" style="width: 100%; padding: 12px; font-size: 16px; border-radius: 10px; margin-top: 15px; font-weight: bold; background: #FF8C42; color: white; border: none; cursor: pointer;">✅ Konfirmasi Pesanan</button>
            </form>
        </div>

        <div style="margin-top: 30px;">
            <div class="filter-container" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="Makanan">🍽️ Makanan</button>
                <button class="filter-btn" data-filter="Minuman">🍹 Minuman</button>
            </div>

            <div class="menu-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                <?php
                $menu_query = $conn->query("SELECT * FROM menu");
                while($item = $menu_query->fetch_assoc()):
                ?>
                <div class="menu-card" data-kategori="<?= $item['kategori'] ?>" style="background: #fff; padding: 15px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center;">
                    <h4 style="margin: 10px 0 5px 0;"><?= htmlspecialchars($item['nama_menu']) ?></h4>
                    <p style="color: #FF8C42; font-weight: bold; margin-bottom: 10px;">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                    <input type="number" class="qty-input" data-id="<?= $item['id'] ?>" data-nama="<?= htmlspecialchars($item['nama_menu']) ?>" data-harga="<?= $item['harga'] ?>" min="0" value="0" style="width: 60px; padding: 5px; text-align: center; border-radius: 6px; border: 1px solid #ccc;">
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        // Filter Kategori
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.dataset.filter;
                document.querySelectorAll('.menu-card').forEach(card => {
                    if(filter === 'all' || card.dataset.kategori === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Proses Tombol Submit Async Fetch Background
        document.getElementById('btn-konfirmasi').addEventListener('click', async function(e) {
            e.preventDefault();
            
            const no_meja = document.getElementById('no_meja').value;
            const nama_pemesan = document.getElementById('nama_pemesan').value.trim();
            const catatan = document.getElementById('catatan').value;
            
            // Mengumpulkan menu yang diinput jumlahnya > 0
            let cart = [];
            document.querySelectorAll('.qty-input').forEach(input => {
                let qty = parseInt(input.value);
                if (qty > 0) {
                    cart.push({
                        id: input.dataset.id,
                        nama: input.dataset.nama,
                        harga: parseInt(input.dataset.harga),
                        jumlah: qty
                    });
                }
            });

            if(cart.length === 0) {
                alert('Meow~ Pilih minimal 1 makanan/minuman dulu di bawah!');
                return;
            }

            if(nama_pemesan === "") {
                alert('Meow~ Mohon isi nama pemesan terlebih dahulu!');
                return;
            }

            const metode_pembayaran = document.querySelector('input[name="metode_pembayaran"]:checked').value;
            const btn = document.getElementById('btn-konfirmasi');
            btn.textContent = '⏳ Memproses Pesanan...';
            btn.disabled = true;
            
            try {
                const response = await fetch('proses_pesan.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        no_meja, nama_pemesan, catatan, items: cart, metode_pembayaran
                    })
                });
                
                const result = await response.json();
                if(result.success) {
                    // DIALIKHAN KE NOTA_PESANAN SECARA OTOMATIS!
                    window.location.href = `nota_pesanan.php?id=${result.id_pesanan}`;
                } else {
                    alert('Meow~ Gagal: ' + result.error);
                    btn.textContent = '✅ Konfirmasi Pesanan';
                    btn.disabled = false;
                }
            } catch (error) {
                alert('Terjadi kesalahan pengiriman sistem meow~');
                btn.textContent = '✅ Konfirmasi Pesanan';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>