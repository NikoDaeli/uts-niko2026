<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bahan'])) {
    $_SESSION['porsi'] = (int)$_POST['porsi'];
    $_SESSION['keranjang_bahan'] = $_POST['bahan'];
}

$porsi = isset($_SESSION['porsi']) ? $_SESSION['porsi'] : 1;
$id_bahan_dipilih = isset($_SESSION['keranjang_bahan']) ? $_SESSION['keranjang_bahan'] : [];

if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus' && isset($_GET['id'])) {
    $id_hapus = $_GET['id'];
    if (($key = array_search($id_hapus, $_SESSION['keranjang_bahan'])) !== false) {
        unset($_SESSION['keranjang_bahan'][$key]);
        // Re-index array
        $_SESSION['keranjang_bahan'] = array_values($_SESSION['keranjang_bahan']);
    }
    header("Location: keranjang.php");
    exit;
}

if (isset($_GET['aksi']) && $_GET['aksi'] === 'update_porsi' && isset($_POST['porsi_baru'])) {
    $_SESSION['porsi'] = (int)$_POST['porsi_baru'];
    header("Location: keranjang.php");
    exit;
}

$bahan_keranjang = [];
$total_harga_bahan = 0;

if (!empty($id_bahan_dipilih)) {
    $ids_string = implode(',', array_map('intval', $id_bahan_dipilih));
    $results = $pdo->query("SELECT * FROM bahan WHERE id IN ($ids_string)");
    
    while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
        $bahan_keranjang[] = $row;
        $total_harga_bahan += $row['harga'];
    }
}

$total_pembayaran = $total_harga_bahan * $porsi;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Jamuku | Yeremia Nicolas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8edf2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header Section */
        .hero-section {
            background: linear-gradient(135deg, #1a472a 0%, #2d5a3b 100%);
            border-radius: 28px;
            padding: 40px 50px;
            margin-bottom: 40px;
            color: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: "🛒";
            font-size: 180px;
            position: absolute;
            bottom: -30px;
            right: -30px;
            opacity: 0.1;
            pointer-events: none;
        }

        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .hero-section h1 i {
            margin-right: 15px;
            font-size: 48px;
        }

        .hero-section p {
            font-size: 18px;
            opacity: 0.95;
            max-width: 600px;
            line-height: 1.6;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: #1a472a;
            padding: 12px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .back-link:hover {
            transform: translateX(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: #f8f6f0;
        }

        /* Cart Card */
        .cart-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .cart-header {
            background: #f8f6f0;
            padding: 20px 30px;
            border-bottom: 2px solid #e8dcc8;
        }

        .cart-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: #2d5a3b;
            margin: 0;
        }

        .cart-header h3 i {
            margin-right: 10px;
            color: #4a7c59;
        }

        /* Porsi Control */
        .porsi-wrapper {
            padding: 25px 30px;
            background: linear-gradient(135deg, #fefef9 0%, #faf8f3 100%);
            border-bottom: 1px solid #e8dcc8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .porsi-card {
            display: flex;
            align-items: center;
            gap: 20px;
            background: white;
            padding: 12px 25px;
            border-radius: 60px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .porsi-card label {
            font-weight: 600;
            color: #1a472a;
            font-size: 16px;
        }

        .porsi-card label i {
            margin-right: 8px;
            color: #4a7c59;
        }

        .porsi-card input {
            width: 80px;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            border: 2px solid #d4e2d0;
            border-radius: 40px;
            transition: all 0.3s;
            font-family: 'Inter', monospace;
        }

        .porsi-card input:focus {
            outline: none;
            border-color: #4a7c59;
            box-shadow: 0 0 0 3px rgba(74,124,89,0.1);
        }

        .btn-update {
            background: #4a7c59;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-update:hover {
            background: #2d5a3b;
            transform: translateY(-2px);
        }

        /* Table Styling */
        .table-wrapper {
            overflow-x: auto;
            padding: 20px 25px;
        }

        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .cart-table thead th {
            background: #2d5a3b;
            color: white;
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .cart-table thead th:first-child {
            border-radius: 12px 0 0 12px;
        }

        .cart-table thead th:last-child {
            border-radius: 0 12px 12px 0;
        }

        .cart-table tbody tr {
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-radius: 12px;
        }

        .cart-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            background: #fefef9;
        }

        .cart-table tbody td {
            padding: 18px 20px;
            border-bottom: 1px solid #f0ebe0;
            color: #2c3e2f;
            vertical-align: middle;
        }

        .bahan-name {
            font-weight: 700;
            color: #1a472a;
            font-size: 16px;
        }

        .bahan-name i {
            color: #4a7c59;
            margin-right: 8px;
        }

        .badge-jenis {
            display: inline-block;
            padding: 5px 12px;
            background: linear-gradient(135deg, #e8f0e6 0%, #d4e2d0 100%);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #2d5a3b;
        }

        .harga-text {
            font-weight: 700;
            color: #c9772e;
            font-size: 16px;
        }

        .btn-hapus {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-hapus:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220,53,69,0.3);
        }

        /* Total Section */
        .total-section {
            background: linear-gradient(135deg, #1a472a 0%, #2d5a3b 100%);
            margin: 20px 25px 25px 25px;
            padding: 30px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .total-info {
            color: white;
        }

        .total-info h4 {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .total-info h2 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }

        .total-info p {
            margin-top: 8px;
            font-size: 14px;
            opacity: 0.8;
        }

        .btn-bayar {
            background: linear-gradient(135deg, #c9772e 0%, #b8621f 100%);
            color: white;
            border: none;
            padding: 16px 35px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 60px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 12px rgba(201,119,46,0.3);
        }

        .btn-bayar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(201,119,46,0.4);
            background: linear-gradient(135deg, #d4833a 0%, #c96e28 100%);
        }

        .btn-bayar:active {
            transform: translateY(0);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #8ba58b;
        }

        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #5a7c5e;
        }

        .empty-state p {
            margin-bottom: 30px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #c9772e 0%, #b8621f 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 40px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(201,119,46,0.3);
        }

        /* Footer */
        .footer-note {
            text-align: center;
            margin-top: 30px;
            color: #7c8f7e;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 30px 25px;
            }
            
            .hero-section h1 {
                font-size: 32px;
            }
            
            .total-section {
                flex-direction: column;
                text-align: center;
            }
            
            .porsi-wrapper {
                flex-direction: column;
                align-items: stretch;
            }
            
            .porsi-card {
                justify-content: space-between;
            }
            
            .cart-table thead th,
            .cart-table tbody td {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="hero-section">
        <h1>
            <i class="fas fa-shopping-cart"></i> 
            Keranjang Racikan Jamu
        </h1>
        <p>Review komposisi jamu pilihanmu sebelum diproses. Sesuaikan porsi dan pastikan bahan-bahan yang dipilih sudah sesuai dengan kebutuhan.</p>
    </div>

    <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> 
        Kembali Meracik / Tambah Bahan
    </a>

    <div class="cart-card">
        <div class="cart-header">
            <h3>
                <i class="fas fa-mortar-pestle"></i> 
                Daftar Racikan Jamu
                <span style="font-size: 14px; font-weight: normal; margin-left: 10px; color: #7c8f7e;">
                    <i class="fas fa-flask"></i> Komposisi herbal pilihan
                </span>
            </h3>
        </div>

        <?php if (empty($bahan_keranjang)): ?>
            <div class="empty-state">
                <i class="fas fa-shopping-basket"></i>
                <h3>Keranjang Kamu Kosong</h3>
                <p>Silakan pilih bahan jamu terlebih dahulu di halaman utama.</p>
                <a href="index.php" class="btn-primary">
                    <i class="fas fa-leaf"></i> Mulai Racik Jamu
                </a>
            </div>
        <?php else: ?>

        <div class="porsi-wrapper">
            <div class="porsi-card">
                <label>
                    <i class="fas fa-utensils"></i> 
                    <strong>Jumlah Porsi Racikan:</strong>
                </label>
                <form action="keranjang.php?aksi=update_porsi" method="POST" style="display: flex; gap: 10px; align-items: center;">
                    <input type="number" id="porsi_baru" name="porsi_baru" value="<?= $porsi; ?>" min="1">
                    <button type="submit" class="btn-update">
                        <i class="fas fa-sync-alt"></i> Update Porsi
                    </button>
                </form>
            </div>
            <div style="font-size: 14px; color: #5a6e5c;">
                <i class="fas fa-info-circle"></i> 
                Harga akan dikalikan dengan jumlah porsi
            </div>
        </div>

        <div class="table-wrapper">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-flask"></i> Nama Bahan</th>
                        <th><i class="fas fa-tag"></i> Jenis</th>
                        <th><i class="fas fa-money-bill-wave"></i> Harga Satuan</th>
                        <th><i class="fas fa-trash-alt"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bahan_keranjang as $item): ?>
                    <tr>
                        <td>
                            <div class="bahan-name">
                                <i class="fas fa-seedling"></i>
                                <?= htmlspecialchars($item['nama']); ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge-jenis">
                                <i class="fas fa-filter"></i> <?= htmlspecialchars($item['jenis']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="harga-text">
                                <i class="fas fa-coins"></i> Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                            </div>
                        </td>
                        <td>
                            <a href="keranjang.php?aksi=hapus&id=<?= $item['id']; ?>" 
                               class="btn-hapus"
                               onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars($item['nama']); ?> dari racikan?')">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-info">
                <h4><i class="fas fa-chart-line"></i> RINGKASAN BELANJA</h4>
                <p>Total Harga Komposisi (1 Porsi)</p>
                <h2>Rp <?= number_format($total_harga_bahan, 0, ',', '.'); ?></h2>
                <p><i class="fas fa-times-circle"></i> Dikalikan <?= $porsi; ?> porsi</p>
            </div>
            <div class="total-info" style="text-align: right;">
                <h4><i class="fas fa-receipt"></i> TOTAL YANG HARUS DIBAYAR</h4>
                <h2 style="font-size: 42px; color: #ffd700;">Rp <?= number_format($total_pembayaran, 0, ',', '.'); ?></h2>
                <p><i class="fas fa-clock"></i> Pesanan akan diproses setelah pembayaran</p>
            </div>
            <button class="btn-bayar" onclick="confirmPayment(<?= $total_pembayaran; ?>, <?= $porsi; ?>)">
                <i class="fas fa-check-circle"></i> 
                Bayar Sekarang
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <?php endif; ?>
    </div>
    
    <div class="footer-note">
        <i class="fas fa-heart" style="color: #c9772e;"></i> 
        Racikan jamu herbal alami tanpa pengawet buatan
        <i class="fas fa-leaf" style="color: #4a7c59; margin-left: 8px;"></i>
    </div>
</div>

<script>
    function confirmPayment(total, porsi) {
        let formattedTotal = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(total);
        
        Swal.fire({
            title: 'Konfirmasi Pesanan',
            html: `
                <div style="text-align: left;">
                    <p><strong>📋 Detail Pesanan:</strong></p>
                    <p>🍵 Jumlah Porsi: <strong>${porsi}</strong></p>
                    <p>💰 Total Pembayaran: <strong style="color: #c9772e;">${formattedTotal}</strong></p>
                    <hr>
                    <p>✅ Pesanan akan segera diproses</p>
                    <p>📦 Estimasi selesai: 15-20 menit</p>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'Ya, Bayar Sekarang',
            cancelButtonText: 'Batal',
            showCancelButton: true,
            background: '#fff',
            confirmButtonColor: '#c9772e',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Terima Kasih! 🎉',
                    html: `
                        <p>Pesanan jamu Anda sedang diracik dengan penuh cinta ❤️</p>
                        <p>Mohon tunggu sebentar ya!</p>
                        <br>
                        <p><small>Nomor Antrian: #JAMU-${Math.floor(Math.random() * 10000)}</small></p>
                    `,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4a7c59'
                }).then(() => {
                    // Optional: redirect ke halaman pesanan atau kosongkan keranjang
                    // window.location.href = 'index.php';
                });
            }
        });
    }
</script>

<!-- SweetAlert2 CDN untuk alert yang lebih cantik -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (empty($bahan_keranjang)): ?>
<script>
    // SweetAlert untuk empty cart (opsional)
    if(window.location.search.indexOf('empty') === -1) {
        // Tidak menampilkan alert otomatis
    }
</script>
<?php endif; ?>

</body>
</html>