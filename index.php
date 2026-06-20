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
            <a href="index.php">🏠 Menu</a>
            <a href="riwayat.php">📋 Riwayat Pesanan</a>
        </div>

        <div class="meja-section">
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
        </div>

        <div class="main-content">
            <div class="menu-section">
                <div class="kategori-filter">
                    <button class="filter-btn active" data-filter="all">😺 Semua</button>
                    <button class="filter-btn" data-filter="Makanan">🍚 Makanan</button>
                    <button class="filter-btn" data-filter="Minuman">🥤 Minuman</button>
                </div>
                
                <div class="menu-grid" id="menu-grid">
                    <?php
                    $menu = $conn->query("SELECT * FROM menu ORDER BY kategori, nama_menu");
                    while($row = $menu->fetch_assoc()):
                    ?>
                    <div class="menu-card" data-kategori="<?= $row['kategori'] ?>">
                        <div class="menu-emoji"><?= $row['kategori'] == 'Makanan' ? '🍚' : '🥤' ?></div>
                        <h3><?= $row['nama_menu'] ?></h3>
                        <p class="harga">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                        <button class="btn-tambah" 
                                data-id="<?= $row['id'] ?>" 
                                data-nama="<?= $row['nama_menu'] ?>" 
                                data-harga="<?= $row['harga'] ?>">
                            😻 Tambah ke Pesanan
                        </button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="cart-section">
                <h3>🛒 Pesanan Meow-mu</h3>
                <div id="cart-items">
                    <p class="empty-cart">Belum ada pesanan nih, meow~</p>
                </div>
                <div class="cart-total">
                    <strong>🐟 Total: Rp <span id="total-harga">0</span></strong>
                </div>
                <textarea id="catatan" placeholder="Catatan khusus (misal: tanpa bawang, pedas level 2)"></textarea>
                
                <div class="payment-section">
                    <label>🐾 Pilih Metode Pembayaran:</label>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment" value="Cash" checked> 💵 Cash (Bayar di Kasir)
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="QRIS"> 📱 QRIS (Scan di Kasir)
                        </label>
                    </div>
                </div>
                
                <button id="btn-checkout" class="btn-checkout">✅ Konfirmasi Pesanan</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function updateCart() {
            const cartDiv = document.getElementById('cart-items');
            const totalSpan = document.getElementById('total-harga');
            let total = 0;
            
            if(cart.length === 0) {
                cartDiv.innerHTML = '<p class="empty-cart">Belum ada pesanan nih, meow~</p>';
                totalSpan.innerText = '0';
                return;
            }
            
            let html = '<table class="cart-table">';
            cart.forEach((item, index) => {
                total += item.harga * item.jumlah;
                html += `
                    <tr>
                        <td>${item.nama}</td>
                        <td>
                            <button class="qty-btn" onclick="ubahJumlah(${index}, -1)">-</button>
                            ${item.jumlah}
                            <button class="qty-btn" onclick="ubahJumlah(${index}, 1)">+</button>
                        </td>
                        <td>Rp ${(item.harga * item.jumlah).toLocaleString()}</td>
                        <td><button class="hapus-btn" onclick="hapusItem(${index})">✖</button></td>
                    </tr>
                `;
            });
            html += '</table>';
            cartDiv.innerHTML = html;
            totalSpan.innerText = total.toLocaleString();
        }

        function ubahJumlah(index, delta) {
            let newJumlah = cart[index].jumlah + delta;
            if(newJumlah < 1) {
                hapusItem(index);
            } else {
                cart[index].jumlah = newJumlah;
                updateCart();
            }
        }

        function hapusItem(index) {
            cart.splice(index, 1);
            updateCart();
        }

        document.querySelectorAll('.btn-tambah').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const nama = btn.dataset.nama;
                const harga = parseInt(btn.dataset.harga);
                
                const existing = cart.find(item => item.id === id);
                if(existing) {
                    existing.jumlah++;
                } else {
                    cart.push({ id, nama, harga, jumlah: 1 });
                }
                updateCart();
            });
        });

        document.getElementById('btn-checkout').addEventListener('click', async () => {
            const no_meja = document.getElementById('no_meja').value;
            const nama_pemesan = document.getElementById('nama_pemesan').value;
            const catatan = document.getElementById('catatan').value;
            const metode_pembayaran = document.querySelector('input[name="payment"]:checked').value;
            
            if(!nama_pemesan) {
                alert('Meow~ Masukkan nama pemesan dulu yaa!');
                return;
            }
            if(cart.length === 0) {
                alert('Meow~ Belum ada pesanan nih, pilih menu dulu yaa!');
                return;
            }
            
            const btn = document.getElementById('btn-checkout');
            btn.textContent = '⏳ Memproses...';
            btn.disabled = true;
            
            const response = await fetch('proses_pesan.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    no_meja, nama_pemesan, catatan, items: cart, metode_pembayaran
                })
            });
            
            const result = await response.json();
            if(result.success) {
                window.location.href = `nota_pesanan.php?id=${result.id_pesanan}`;
            } else {
                alert('Meow~ Error: ' + result.error);
                btn.textContent = '✅ Konfirmasi Pesanan';
                btn.disabled = false;
            }
        });

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
    </script>
</body>
</html>
