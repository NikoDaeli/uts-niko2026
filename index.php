<?php
require_once 'config/database.php';

$results = $pdo->query("SELECT * FROM bahan");
$semua_bahan = [];
while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $semua_bahan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jamuku - Racik Jamu Tradisionalmu | Yeremia Nicolas</title>
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
            max-width: 1400px;
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
            content: "🌿";
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

        /* Card & Table Styling */
        .form-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .form-header {
            background: #f8f6f0;
            padding: 20px 30px;
            border-bottom: 2px solid #e8dcc8;
        }

        .form-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: #2d5a3b;
            margin: 0;
        }

        .form-header h3 i {
            margin-right: 10px;
            color: #4a7c59;
        }

        .table-wrapper {
            overflow-x: auto;
            padding: 20px 25px;
        }

        .jamu-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .jamu-table thead th {
            background: #2d5a3b;
            color: white;
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .jamu-table thead th:first-child {
            border-radius: 12px 0 0 12px;
        }

        .jamu-table thead th:last-child {
            border-radius: 0 12px 12px 0;
        }

        .jamu-table tbody tr {
            background: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-radius: 12px;
        }

        .jamu-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            background: #fefef9;
        }

        .jamu-table tbody td {
            padding: 18px 20px;
            border-bottom: 1px solid #f0ebe0;
            color: #2c3e2f;
            vertical-align: middle;
        }

        /* Custom Checkbox */
        .checkbox-custom {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-custom input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #4a7c59;
            transform: scale(1.1);
            transition: transform 0.2s;
        }

        .checkbox-custom input[type="checkbox"]:hover {
            transform: scale(1.2);
        }

        .bahan-name {
            font-weight: 700;
            color: #1a472a;
            font-size: 16px;
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

        .deskripsi-text {
            font-size: 13px;
            color: #5a6e5c;
            line-height: 1.4;
            max-width: 300px;
        }

        .harga-text {
            font-weight: 700;
            color: #c9772e;
            font-size: 16px;
        }

        /* Order Section */
        .order-section {
            background: #f8f6f0;
            padding: 25px 30px;
            border-top: 2px solid #e8dcc8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .porsi-control {
            display: flex;
            align-items: center;
            gap: 20px;
            background: white;
            padding: 12px 25px;
            border-radius: 60px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .porsi-control label {
            font-weight: 600;
            color: #1a472a;
            font-size: 16px;
        }

        .porsi-control label i {
            margin-right: 8px;
            color: #4a7c59;
        }

        .porsi-control input {
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

        .porsi-control input:focus {
            outline: none;
            border-color: #4a7c59;
            box-shadow: 0 0 0 3px rgba(74,124,89,0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #c9772e 0%, #b8621f 100%);
            color: white;
            border: none;
            padding: 14px 35px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 40px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 12px rgba(201,119,46,0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(201,119,46,0.4);
            background: linear-gradient(135deg, #d4833a 0%, #c96e28 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit i {
            font-size: 18px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #8ba58b;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
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
            
            .order-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .porsi-control {
                justify-content: space-between;
            }
            
            .btn-submit {
                justify-content: center;
            }
            
            .jamu-table thead th,
            .jamu-table tbody td {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="hero-section">
        <h1>
            <i class="fas fa-mortar-pestle"></i> 
            Jamu Tradisionalku
        </h1>
        <p>Racik jamu kamu sendiri sesuai khasiat yang diinginkan! Pilih bahan-bahan alami terbaik dan ciptakan ramuan herbal yang sempurna untuk kesehatanmu.</p>
    </div>

    <div class="form-card">
        <div class="form-header">
            <h3>
                <i class="fas fa-leaf"></i> 
                Komposisi Bahan Jamu
                <span style="font-size: 14px; font-weight: normal; margin-left: 10px; color: #7c8f7e;">
                    <i class="fas fa-check-circle"></i> Pilih bahan sesuai kebutuhan
                </span>
            </h3>
        </div>
        
        <form action="keranjang.php" method="POST">
            <div class="table-wrapper">
                <?php if (count($semua_bahan) > 0): ?>
                <table class="jamu-table">
                    <thead>
                        <tr>
                            <th width="60"><i class="fas fa-check-square"></i> Pilih</th>
                            <th><i class="fas fa-flask"></i> Nama Bahan</th>
                            <th><i class="fas fa-tag"></i> Jenis</th>
                            <th><i class="fas fa-info-circle"></i> Deskripsi</th>
                            <th><i class="fas fa-money-bill-wave"></i> Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semua_bahan as $bahan): ?>
                        <tr>
                            <td class="checkbox-custom">
                                <input type="checkbox" name="bahan[]" value="<?= $bahan['id']; ?>" id="bahan_<?= $bahan['id']; ?>">
                            </td>
                            <td>
                                <div class="bahan-name">
                                    <i class="fas fa-seedling" style="color: #4a7c59; margin-right: 8px;"></i>
                                    <?= htmlspecialchars($bahan['nama']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge-jenis">
                                    <i class="fas fa-filter"></i> <?= htmlspecialchars($bahan['jenis']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="deskripsi-text">
                                    <?= htmlspecialchars($bahan['deskripsi']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="harga-text">
                                    <i class="fas fa-coins"></i> Rp <?= number_format($bahan['harga'], 0, ',', '.'); ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Belum ada bahan tersedia</h3>
                    <p>Silakan tambahkan bahan jamu terlebih dahulu.</p>
                </div>
                <?php endif; ?>
            </div>

            <div class="order-section">
                <div class="porsi-control">
                    <label>
                        <i class="fas fa-utensils"></i> 
                        <strong>Jumlah Porsi Racikan:</strong>
                    </label>
                    <input type="number" id="porsi" name="porsi" value="1" min="1" required>
                    <span style="font-size: 14px; color: #5a6e5c;">Porsi</span>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-shopping-cart"></i> 
                    Masukkan ke Keranjang Belanja
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
    
    <div class="footer-note">
        <i class="fas fa-heart" style="color: #c9772e;"></i> 
        Racik sendiri jamu herbalmu dengan bahan-bahan alami pilihan
        <i class="fas fa-leaf" style="color: #4a7c59; margin-left: 8px;"></i>
    </div>
</div>

<script>
    // Animasi tambahan untuk checkbox
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if(this.checked) {
                this.closest('tr').style.backgroundColor = '#fefef0';
            } else {
                this.closest('tr').style.backgroundColor = '';
            }
        });
    });
    
    // Validasi minimal pilih 1 bahan
    const form = document.querySelector('form');
    if(form) {
        form.addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            if(checkboxes.length === 0) {
                e.preventDefault();
                alert('⚠️ Silakan pilih minimal 1 bahan jamu terlebih dahulu!');
            }
        });
    }
</script>

</body>
</html>