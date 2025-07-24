<?php
session_start();
require_once '../admin/config/database.php';
require_once '../admin/includes/functions.php';

// Check if user is logged in
requireLogin();

// Include FPDF library
require_once 'vendor/fpdf/fpdf.php';

// Simple PDF class extending FPDF
class SimplePDF extends FPDF
{
    private $reportTitle = '';
    private $reportDate = '';

    function setReport($title, $date) {
        $this->reportTitle = $title;
        $this->reportDate = $date;
    }

    // Page header
    function Header()
    {
        // Arial bold 16
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'TRINITY RESTAURANT',0,1,'C');

        // Report title
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,$this->reportTitle,0,1,'C');

        // Date
        $this->SetFont('Arial','',12);
        $this->Cell(0,6,$this->reportDate,0,1,'C');

        // Line
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY() + 2, 200, $this->GetY() + 2);
        $this->Ln(8);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo(),0,0,'C');
    }

    // Summary section
    function addSummary($data) {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,8,'RINGKASAN PENJUALAN',0,1);
        $this->Ln(2);

        $this->SetFont('Arial','',10);
        $this->SetFillColor(240, 240, 240);

        foreach($data as $key => $value) {
            $this->Cell(120, 7, $key, 1, 0, 'L', true);
            $this->Cell(70, 7, $value, 1, 1, 'R', true);
        }
        $this->Ln(5);
    }

    // Simple table
    function addTable($title, $headers, $data, $widths) {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,8,$title,0,1);
        $this->Ln(2);

        // Header
        $this->SetFillColor(44, 62, 80);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);

        for($i = 0; $i < count($headers); $i++) {
            $this->Cell($widths[$i], 8, $headers[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // Data
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 9);

        $fill = false;
        foreach($data as $row) {
            for($i = 0; $i < count($row); $i++) {
                $align = ($i == 0) ? 'L' : (($i == count($row) - 1) ? 'R' : 'C');
                $this->Cell($widths[$i], 6, $row[$i], 1, 0, $align, $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        $this->Ln(5);
    }

    function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Get parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
$week_start = isset($_GET['week_start']) ? $_GET['week_start'] : '';

try {
    switch ($type) {
        case 'daily':
            generateSimpleDailyReport($date);
            break;
        case 'weekly':
            generateSimpleWeeklyReport($week_start);
            break;
        default:
            throw new Exception('Invalid report type');
    }
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

function generateSimpleDailyReport($date) {
    $data = getDailyReportData($date);

    $pdf = new SimplePDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->setReport('LAPORAN PENJUALAN HARIAN', date('d F Y', strtotime($data['date'])));

    // Summary
    $summary = array(
        'Total Transaksi' => number_format($data['summary']['total_transaksi']) . ' transaksi',
        'Total Pendapatan' => $pdf->formatCurrency($data['summary']['total_pendapatan']),
        'Rata-rata Transaksi' => $pdf->formatCurrency($data['summary']['rata_rata_transaksi']),
        'Total Reservasi' => number_format($data['reservations']['total_reservasi']) . ' reservasi',
        'Total Tamu' => number_format($data['reservations']['total_tamu']) . ' orang'
    );

    $pdf->addSummary($summary);

    // Top products
    if (!empty($data['top_products'])) {
        $headers = array('No', 'Produk', 'Qty', 'Revenue');
        $widths = array(20, 90, 30, 50);
        $tableData = array();

        $no = 1;
        foreach($data['top_products'] as $product) {
            $tableData[] = array(
                $no,
                substr($product['name'], 0, 30),
                number_format($product['qty_terjual']),
                $pdf->formatCurrency($product['total_revenue'])
            );
            $no++;
            if ($no > 10) break; // Limit to top 10
        }

        $pdf->addTable('TOP PRODUK TERJUAL', $headers, $tableData, $widths);
    }

    // Footer info
    $pdf->SetY(-30);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Laporan digenerate: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Oleh: ' . $_SESSION['full_name'], 0, 1, 'R');

    $filename = 'Laporan_Harian_' . date('Ymd', strtotime($data['date'])) . '.pdf';
    $pdf->Output('D', $filename);
}

function generateSimpleWeeklyReport($week_start) {
    $data = getWeeklyReportData($week_start);

    $pdf = new SimplePDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $dateRange = date('d M Y', strtotime($data['week_start'])) . ' - ' . date('d M Y', strtotime($data['week_end']));
    $pdf->setReport('LAPORAN PENJUALAN MINGGUAN', $dateRange);

    // Summary
    $summary = array(
        'Total Transaksi' => number_format($data['summary']['total_transaksi']) . ' transaksi',
        'Total Pendapatan' => $pdf->formatCurrency($data['summary']['total_pendapatan']),
        'Rata-rata Transaksi' => $pdf->formatCurrency($data['summary']['rata_rata_transaksi'])
    );

    $pdf->addSummary($summary);

    // Daily sales
    if (!empty($data['daily_sales'])) {
        $headers = array('Tanggal', 'Hari', 'Transaksi', 'Penjualan');
        $widths = array(35, 30, 35, 90);
        $tableData = array();

        foreach($data['daily_sales'] as $day) {
            $tableData[] = array(
                date('d/m/Y', strtotime($day['tanggal'])),
                getIndonesianDay($day['hari']),
                number_format($day['jumlah_transaksi']),
                $pdf->formatCurrency($day['total_penjualan'])
            );
        }

        $pdf->addTable('PENJUALAN PER HARI', $headers, $tableData, $widths);
    }

    // Top products
    if (!empty($data['top_products'])) {
        $headers = array('No', 'Produk', 'Qty', 'Revenue');
        $widths = array(20, 90, 30, 50);
        $tableData = array();

        $no = 1;
        foreach($data['top_products'] as $product) {
            $tableData[] = array(
                $no,
                substr($product['name'], 0, 30),
                number_format($product['qty_terjual']),
                $pdf->formatCurrency($product['total_revenue'])
            );
            $no++;
            if ($no > 10) break; // Limit to top 10
        }

        $pdf->addTable('TOP PRODUK MINGGUAN', $headers, $tableData, $widths);
    }

    // Footer info
    $pdf->SetY(-30);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Laporan digenerate: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
    $pdf->Cell(0, 5, 'Oleh: ' . $_SESSION['full_name'], 0, 1, 'R');

    $filename = 'Laporan_Mingguan_' . date('Ymd', strtotime($data['week_start'])) . '.pdf';
    $pdf->Output('D', $filename);
}
?>
