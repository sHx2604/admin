<?php
session_start();
require_once '../admin/config/database.php';
require_once '../admin/includes/functions.php';

// Check if user is logged in
requireLogin();

// Include FPDF library - Download from http://www.fpdf.org/
require_once 'vendor/fpdf/fpdf.php';

// Custom PDF class extending FPDF
class PDF extends FPDF
{
    private $title = '';
    private $subtitle = '';

    function setReportTitle($title, $subtitle = '') {
        $this->title = $title;
        $this->subtitle = $subtitle;
    }

    // Page header
    function Header()
    {
        // Logo placeholder (add logo if needed)
        // $this->Image('logo.png',10,6,30);

        // Arial bold 15
        $this->SetFont('Arial','B',16);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30,10,'TRINITY RESTAURANT',0,0,'C');
        // Line break
        $this->Ln(8);

        // Report title
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,$this->title,0,1,'C');

        if ($this->subtitle) {
            $this->SetFont('Arial','',12);
            $this->Cell(0,8,$this->subtitle,0,1,'C');
        }

        // Line
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // Table header
    function TableHeader($headers, $widths)
    {
        $this->SetFillColor(44, 62, 80); // Dark blue
        $this->SetTextColor(255, 255, 255); // White text
        $this->SetFont('Arial', 'B', 10);

        for($i = 0; $i < count($headers); $i++) {
            $this->Cell($widths[$i], 8, $headers[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Reset colors
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 9);
    }

    // Table row
    function TableRow($data, $widths, $aligns = array(), $fill = false)
    {
        for($i = 0; $i < count($data); $i++) {
            $align = isset($aligns[$i]) ? $aligns[$i] : 'L';
            $this->Cell($widths[$i], 6, $data[$i], 1, 0, $align, $fill);
        }
        $this->Ln();
    }

    // Summary table
    function SummaryTable($title, $data)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $title, 0, 1);
        $this->Ln(2);

        $this->SetFillColor(240, 240, 240);
        $this->SetFont('Arial', '', 10);

        $fill = false;
        foreach($data as $row) {
            $this->SetFillColor($fill ? 249 : 240, $fill ? 249 : 240, $fill ? 249 : 240);
            $this->Cell(120, 8, $row[0], 1, 0, 'L', true);
            $this->Cell(70, 8, $row[1], 1, 1, 'R', true);
            $fill = !$fill;
        }
        $this->Ln(5);
    }

    // Currency formatting
    function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Get report type and parameters
$report_type = isset($_GET['type']) ? $_GET['type'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
$week_start = isset($_GET['week_start']) ? $_GET['week_start'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

try {
    switch ($report_type) {
        case 'daily':
            generateDailyReport($date);
            break;
        case 'weekly':
            generateWeeklyReport($week_start);
            break;
        case 'monthly':
            generateMonthlyReport($month, $year);
            break;
        default:
            throw new Exception('Invalid report type');
    }
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

// Function to generate daily report
function generateDailyReport($date) {
    $data = getDailyReportData($date);

    // Create new PDF document
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Set report title
    $pdf->setReportTitle('LAPORAN PENJUALAN HARIAN', date('d F Y', strtotime($data['date'])));

    // Summary section
    $summaryData = array(
        array('Total Transaksi', number_format($data['summary']['total_transaksi']) . ' transaksi'),
        array('Total Pendapatan', $pdf->formatCurrency($data['summary']['total_pendapatan'])),
        array('Rata-rata per Transaksi', $pdf->formatCurrency($data['summary']['rata_rata_transaksi'])),
        array('Total Reservasi', number_format($data['reservations']['total_reservasi']) . ' reservasi'),
        array('Total Tamu Reservasi', number_format($data['reservations']['total_tamu']) . ' orang')
    );

    $pdf->SummaryTable('RINGKASAN PENJUALAN', $summaryData);

    // Top products section
    if (!empty($data['top_products'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'PRODUK TERLARIS HARI INI', 0, 1);
        $pdf->Ln(2);

        $headers = array('No', 'Nama Produk', 'Qty', 'Total Revenue');
        $widths = array(15, 100, 25, 50);

        $pdf->TableHeader($headers, $widths);

        $no = 1;
        foreach ($data['top_products'] as $product) {
            $fill = ($no % 2 == 0);
            $rowData = array(
                $no,
                $product['name'],
                number_format($product['qty_terjual']),
                $pdf->formatCurrency($product['total_revenue'])
            );
            $aligns = array('C', 'L', 'C', 'R');
            $pdf->TableRow($rowData, $widths, $aligns, $fill);
            $no++;
        }
        $pdf->Ln(5);
    }

    // Detail transactions
    if (!empty($data['transactions'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'DETAIL TRANSAKSI', 0, 1);
        $pdf->Ln(2);

        $headers = array('Invoice', 'Waktu', 'Kasir', 'Items', 'Total');
        $widths = array(30, 25, 35, 20, 80);

        $pdf->TableHeader($headers, $widths);

        $no = 1;
        foreach ($data['transactions'] as $trans) {
            if ($pdf->GetY() > 250) { // Check if need new page
                $pdf->AddPage();
                $pdf->TableHeader($headers, $widths);
            }

            $fill = ($no % 2 == 0);
            $rowData = array(
                $trans['invoice_number'],
                date('H:i', strtotime($trans['created_at'])),
                $trans['kasir'],
                $trans['jumlah_item'],
                $pdf->formatCurrency($trans['total_amount'])
            );
            $aligns = array('L', 'C', 'L', 'C', 'R');
            $pdf->TableRow($rowData, $widths, $aligns, $fill);
            $no++;
        }
    }

    // Footer info
    $pdf->SetY(-30);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Laporan digenerate pada: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Oleh: ' . $_SESSION['full_name'], 0, 1, 'R');

    // Output PDF
    $filename = 'Laporan_Harian_' . date('Ymd', strtotime($data['date'])) . '.pdf';
    $pdf->Output('D', $filename);
}

// Function to generate weekly report
function generateWeeklyReport($week_start) {
    $data = getWeeklyReportData($week_start);

    // Create new PDF document
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Set report title
    $subtitle = date('d F Y', strtotime($data['week_start'])) . ' - ' . date('d F Y', strtotime($data['week_end']));
    $pdf->setReportTitle('LAPORAN PENJUALAN MINGGUAN', $subtitle);

    // Summary section
    $summaryData = array(
        array('Total Transaksi', number_format($data['summary']['total_transaksi']) . ' transaksi'),
        array('Total Pendapatan', $pdf->formatCurrency($data['summary']['total_pendapatan'])),
        array('Rata-rata per Transaksi', $pdf->formatCurrency($data['summary']['rata_rata_transaksi']))
    );

    $pdf->SummaryTable('RINGKASAN PENJUALAN MINGGUAN', $summaryData);

    // Daily sales breakdown
    if (!empty($data['daily_sales'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'PENJUALAN PER HARI', 0, 1);
        $pdf->Ln(2);

        $headers = array('Tanggal', 'Hari', 'Transaksi', 'Total Penjualan');
        $widths = array(40, 30, 30, 90);

        $pdf->TableHeader($headers, $widths);

        $no = 1;
        foreach ($data['daily_sales'] as $day) {
            $fill = ($no % 2 == 0);
            $rowData = array(
                date('d/m/Y', strtotime($day['tanggal'])),
                getIndonesianDay($day['hari']),
                number_format($day['jumlah_transaksi']),
                $pdf->formatCurrency($day['total_penjualan'])
            );
            $aligns = array('L', 'L', 'C', 'R');
            $pdf->TableRow($rowData, $widths, $aligns, $fill);
            $no++;
        }
        $pdf->Ln(5);
    }

    // Top products section
    if (!empty($data['top_products'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'TOP 15 PRODUK TERLARIS MINGGU INI', 0, 1);
        $pdf->Ln(2);

        $headers = array('No', 'Produk', 'Harga', 'Qty', 'Revenue');
        $widths = array(15, 80, 30, 25, 40);

        $pdf->TableHeader($headers, $widths);

        $no = 1;
        foreach ($data['top_products'] as $product) {
            if ($pdf->GetY() > 250) {
                $pdf->AddPage();
                $pdf->TableHeader($headers, $widths);
            }

            $fill = ($no % 2 == 0);
            $rowData = array(
                $no,
                substr($product['name'], 0, 25), // Truncate long names
                $pdf->formatCurrency($product['price']),
                number_format($product['qty_terjual']),
                $pdf->formatCurrency($product['total_revenue'])
            );
            $aligns = array('C', 'L', 'R', 'C', 'R');
            $pdf->TableRow($rowData, $widths, $aligns, $fill);
            $no++;
        }
    }

    // Footer info
    $pdf->SetY(-30);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Laporan digenerate pada: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Oleh: ' . $_SESSION['full_name'], 0, 1, 'R');

    // Output PDF
    $filename = 'Laporan_Mingguan_' . date('Ymd', strtotime($data['week_start'])) . '-' . date('Ymd', strtotime($data['week_end'])) . '.pdf';
    $pdf->Output('D', $filename);
}

// Function to generate monthly report
function generateMonthlyReport($month, $year) {
    $data = getMonthlyReportData($month, $year);

    // Create new PDF document
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Set report title
    $pdf->setReportTitle('LAPORAN PENJUALAN BULANAN', $data['month_name']);

    // Summary section
    $summaryData = array(
        array('Total Transaksi', number_format($data['summary']['total_transaksi']) . ' transaksi'),
        array('Total Pendapatan', $pdf->formatCurrency($data['summary']['total_pendapatan'])),
        array('Rata-rata per Transaksi', $pdf->formatCurrency($data['summary']['rata_rata_transaksi'])),
        array('Total Kasir Aktif', number_format($data['summary']['total_kasir']) . ' orang')
    );

    $pdf->SummaryTable('RINGKASAN PENJUALAN BULANAN', $summaryData);

    // Weekly sales breakdown
    if (!empty($data['weekly_sales'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'PENJUALAN PER MINGGU', 0, 1);
        $pdf->Ln(2);

        $headers = array('Periode', 'Transaksi', 'Total Penjualan');
        $widths = array(80, 40, 70);

        $pdf->TableHeader($headers, $widths);

        $no = 1;
        foreach ($data['weekly_sales'] as $week) {
            $fill = ($no % 2 == 0);
            $rowData = array(
                $week['periode'],
                number_format($week['jumlah_transaksi']),
                $pdf->formatCurrency($week['total_penjualan'])
            );
            $aligns = array('L', 'C', 'R');
            $pdf->TableRow($rowData, $widths, $aligns, $fill);
            $no++;
        }
        $pdf->Ln(5);
    }

    // Cashier performance
    if (!empty($data['cashier_performance'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'PERFORMA KASIR BULAN INI', 0, 1);
        $pdf->Ln(2);

        $headers = array('Kasir', 'Transaksi', 'Total', 'Rata-rata');
        $widths = array(50, 30, 55, 55);

        $pdf->TableHeader($headers, $widths);

        $no = 1;
        foreach ($data['cashier_performance'] as $cashier) {
            $fill = ($no % 2 == 0);
            $rowData = array(
                $cashier['nama_kasir'],
                number_format($cashier['jumlah_transaksi']),
                $pdf->formatCurrency($cashier['total_penjualan']),
                $pdf->formatCurrency($cashier['rata_rata_transaksi'])
            );
            $aligns = array('L', 'C', 'R', 'R');
            $pdf->TableRow($rowData, $widths, $aligns, $fill);
            $no++;
        }
        $pdf->Ln(5);
    }

    // Add new page for top products
    $pdf->AddPage();

    // Top products section
    if (!empty($data['top_products'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'TOP 20 PRODUK TERLARIS BULAN INI', 0, 1);
        $pdf->Ln(2);

        $headers = array('No', 'Produk', 'Kategori', 'Harga', 'Qty', 'Revenue');
        $widths = array(12, 60, 35, 25, 20, 38);

        $pdf->TableHeader($headers, $widths);

        $no = 1;
        foreach ($data['top_products'] as $product) {
            if ($pdf->GetY() > 250) {
                $pdf->AddPage();
                $pdf->TableHeader($headers, $widths);
            }

            $fill = ($no % 2 == 0);
            $rowData = array(
                $no,
                substr($product['name'], 0, 20),
                substr($product['kategori'], 0, 12),
                $pdf->formatCurrency($product['price']),
                number_format($product['qty_terjual']),
                $pdf->formatCurrency($product['total_revenue'])
            );
            $aligns = array('C', 'L', 'L', 'R', 'C', 'R');
            $pdf->TableRow($rowData, $widths, $aligns, $fill);
            $no++;
        }
        $pdf->Ln(5);
    }

    // Reservation summary
    if (isset($data['reservation_summary'])) {
        $reservationData = array(
            array('Total Reservasi', number_format($data['reservation_summary']['total_reservasi']) . ' reservasi'),
            array('Total Tamu', number_format($data['reservation_summary']['total_tamu']) . ' orang'),
            array('Rata-rata Tamu per Reservasi', number_format($data['reservation_summary']['rata_rata_tamu'], 1) . ' orang')
        );

        $pdf->SummaryTable('RINGKASAN RESERVASI BULAN INI', $reservationData);
    }

    // Footer info
    $pdf->SetY(-30);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Laporan digenerate pada: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Oleh: ' . $_SESSION['full_name'], 0, 1, 'R');

    // Output PDF
    $filename = 'Laporan_Bulanan_' . $data['year'] . '-' . str_pad($data['month'], 2, '0', STR_PAD_LEFT) . '.pdf';
    $pdf->Output('D', $filename);
}
?>
