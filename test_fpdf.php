<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Bypass login for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['full_name'] = 'Administrator';
    $_SESSION['role'] = 'admin';
}

// Check if FPDF library exists
if (!file_exists('vendor/fpdf/fpdf.php')) {
    die('Error: FPDF library not found at vendor/fpdf/fpdf.php');
}

require_once 'vendor/fpdf/fpdf.php';

// Simple test PDF
class TestPDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'TRINITY RESTAURANT - TEST PDF',0,1,'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}

// Create PDF
try {
    $pdf = new TestPDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Test PDF Generation',0,1);
    $pdf->Ln(5);

    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,7,'Ini adalah file PDF test untuk memastikan library FPDF berfungsi dengan baik.');
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,8,'Informasi:',0,1);

    $pdf->SetFont('Arial','',10);
    $pdf->Cell(60,6,'Generated Date:',0,0);
    $pdf->Cell(0,6,date('d F Y H:i:s'),0,1);

    $pdf->Cell(60,6,'User:',0,0);
    $pdf->Cell(0,6,$_SESSION['full_name'],0,1);

    $pdf->Cell(60,6,'PHP Version:',0,0);
    $pdf->Cell(0,6,phpversion(),0,1);

    $pdf->Ln(10);

    // Test table
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,8,'Test Table',0,1);

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(44, 62, 80);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(30,8,'No',1,0,'C',true);
    $pdf->Cell(80,8,'Item',1,0,'C',true);
    $pdf->Cell(40,8,'Qty',1,0,'C',true);
    $pdf->Cell(40,8,'Price',1,1,'C',true);

    $pdf->SetFont('Arial','',10);
    $pdf->SetTextColor(0);

    for ($i = 1; $i <= 5; $i++) {
        $fill = ($i % 2 == 0);
        $pdf->SetFillColor($fill ? 240 : 255);
        $pdf->Cell(30,6,$i,1,0,'C',$fill);
        $pdf->Cell(80,6,'Test Item ' . $i,1,0,'L',$fill);
        $pdf->Cell(40,6,rand(1, 100),1,0,'C',$fill);
        $pdf->Cell(40,6,'Rp ' . number_format(rand(10000, 100000), 0, ',', '.'),1,1,'R',$fill);
    }

    // Output PDF
    $filename = 'Test_PDF_' . date('YmdHis') . '.pdf';
    $pdf->Output('D', $filename);

} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p style='color:red'>" . $e->getMessage() . "</p>";
    echo "<p><a href='dashboard.php'>Back to Dashboard</a></p>";
}
?>
