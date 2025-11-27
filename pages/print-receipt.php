<?php
require_once '../core/functions.php';
checkAuth();

// Validasi dan sanitasi sale_id
$sale_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$error_message = null;
$sale = null;
$items = [];

if (!$sale_id || $sale_id <= 0) {
    $error_message = 'Invalid Sale ID. Please provide a valid sale ID.';
    http_response_code(400);
} else {
    $sale = getSaleById($sale_id);

    if (!$sale) {
        $error_message = 'The sale with ID ' . htmlspecialchars($sale_id) . ' does not exist.';
        http_response_code(404);
    } else {
        $items = getSaleItems($sale_id);

        if (empty($items)) {
            $error_message = 'No items found for this sale.';
            http_response_code(400);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - <?= isset($sale['invoice_number']) ? htmlspecialchars($sale['invoice_number']) : 'Error' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .error-container {
            background: white;
            border: 2px solid #ef4444;
            border-radius: 8px;
            padding: 40px;
            max-width: 400px;
            margin: 50px auto;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .error-container h2 {
            color: #ef4444;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .error-container p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .error-container button {
            padding: 10px 25px;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid #ef4444;
            background: #ef4444;
            color: white;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .error-container button:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        .receipt {
            background: white;
            border: 2px dashed #333;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .header-subtitle {
            font-size: 12px;
            color: #666;
        }

        .info {
            margin-bottom: 15px;
            font-size: 12px;
        }

        .info div {
            margin-bottom: 4px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .info strong {
            display: inline-block;
            width: 80px;
        }

        .items {
            margin: 20px 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dotted #ddd;
        }

        .item-details {
            flex: 1;
            margin-right: 10px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-qty {
            font-size: 11px;
            color: #666;
        }

        .item-amount {
            text-align: right;
            font-weight: bold;
        }

        .separator {
            border-top: 1px dashed #333;
            margin: 15px 0;
        }

        .summary {
            font-size: 12px;
            margin: 10px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #333;
            font-size: 11px;
            line-height: 1.6;
            color: #666;
        }

        .button-group {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 25px;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid #333;
            background: white;
            margin: 5px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #333;
            color: white;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .btn-print:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        @media print {
            body {
                padding: 0;
                background: white;
            }
            .receipt {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .button-group {
                display: none;
            }
            .no-print {
                display: none !important;
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .receipt {
                padding: 15px;
            }
            .header h1 {
                font-size: 20px;
            }
            .info strong {
                width: 70px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <?php if ($error_message): ?>
    <div class="error-container">
        <h2>❌ Error</h2>
        <p><?= $error_message ?></p>
        <button onclick="window.history.back()">← Kembali</button>
    </div>
    <?php else: ?>
    <div class="receipt">
        <div class="header">
            <h1>TRINITY RESTAURANT</h1>
            <div class="header-subtitle">Point of Sale System</div>
        </div>

        <div class="info">
            <div><strong>Invoice:</strong> <?= htmlspecialchars($sale['invoice_number']) ?></div>
            <div><strong>Tanggal:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($sale['created_at']))) ?></div>
            <div><strong>Kasir:</strong> <?= htmlspecialchars($sale['cashier_name'] ?? 'N/A') ?></div>
            <?php if (!empty($sale['customer_name'])): ?>
            <div><strong>Pelanggan:</strong> <?= htmlspecialchars($sale['customer_name']) ?></div>
            <?php endif; ?>
            <div><strong>Pembayaran:</strong> <?= htmlspecialchars(ucfirst($sale['payment_method'])) ?></div>
        </div>

        <div class="separator"></div>

        <div class="items">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                <div class="item">
                    <div class="item-details">
                        <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                        <div class="item-qty"><?= intval($item['quantity']) ?> x <?= formatCurrency($item['price']) ?></div>
                    </div>
                    <div class="item-amount"><?= formatCurrency($item['total']) ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; color: #999; padding: 20px;">
                    Tidak ada item dalam struk ini
                </div>
            <?php endif; ?>
        </div>

        <div class="separator"></div>

        <div class="total">
            <div>TOTAL:</div>
            <div><?= formatCurrency($sale['total_amount']) ?></div>
        </div>

        <div class="footer">
            <div>Terima kasih atas kunjungan Anda!</div>
            <div>Silakan datang kembali</div>
            <div style="margin-top: 5px; font-size: 10px;">
                Invoice: <?= htmlspecialchars($sale['invoice_number']) ?>
            </div>
        </div>
    </div>

    <div class="button-group no-print">
        <button class="btn btn-print" onclick="window.print()">
            🖨️ Cetak Struk
        </button>
        <button class="btn" onclick="window.close()">
            ❌ Tutup
        </button>
    </div>
    <?php endif; ?>

    <script>
        // Auto print when page loads (optional)
        // Uncomment line below to enable auto-print
        // window.onload = function() {
        //     window.print();
        // };

        // Handle print completion
        window.addEventListener('afterprint', function() {
            // Optional: handle after print action
            // window.close();
        });

        // Prevent closing if user cancels print
        window.addEventListener('beforeprint', function() {
            // Optional: handle before print action
        });
    </script>
</body>
</html>
