<?php
require_once('tfpdf.php');

$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
$pdf->SetFont('DejaVu', '', 14);
$pdf->Write(10, 'Primjer teksta: č, ć, ž, š, đ');
$pdf->Output();
