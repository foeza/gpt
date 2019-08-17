<?php
 
header("Content-type: application/pdf");

App::import('Vendor','xtcpdf');
 
$pdf = new XTCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetMargins(22, 15, 22);

$pdf->AddPage();

$pdf->writeHTML($this->fetch('content'), true, 0, true, 0, '');

$pdf->lastPage();
 
echo $pdf->Output($title_for_layout.'.pdf', 'D');