<?php
// Simple test file to verify FPDF installation
require_once 'vendor/fpdf/fpdf.php';

// Test FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'FPDF Test - Trinity System',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'FPDF Library berhasil diinstall dan berfungsi!',0,1,'C');
$pdf->Ln(10);
$pdf->Cell(0,10,'Test timestamp: ' . date('d/m/Y H:i:s'),0,1,'C');

// Output test
$pdf->Output('D', 'fpdf_test.pdf');
?>
