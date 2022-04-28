<?php

    $existe = file_exists('../fpdf/fpdf.php');
    if($existe){
        require('../fpdf/fpdf.php');
    }else{
        echo 'no existe el archivo'.$existe;
    }
    $pdf=new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(40,10,'Hello World!');
    $pdf->Output('../comprobantes/ejemplo.pdf', 'f');

?>