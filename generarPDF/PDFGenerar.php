<?php

    require('./fpdf/fpdf.php');


    class GenerarPDF{

        public function generarFactura($xml, $claveacceso){
            $decodeXML = simplexml_load_file($xml);
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->setFont('Arial', 'B', 8);
            if ($decodeXML->infoFactura->obligadoContabilidad == 'SI') {

                $contabilidad = "Obligado a llevar contabilidad : SI";
            } else {
                $contabilidad = "Obligado a llevar contabilidad : NO";
            }

            $pdf->SetXY(10, 0);
            $pdf->image('./src/services/uploads/Logo.jpg', null, null, 80, 30);
            $pdf->SetXY(110, 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(100, 10, "RUC: " . $decodeXML->infoTributaria->ruc, 0, 'J', true);
        $pdf->SetXY(110, 15);
        $pdf->MultiCell(100, 10, "Factura Nro: " . $decodeXML->infoTributaria->estab . $decodeXML->infoTributaria->ptoEmi . $decodeXML->infoTributaria->secuencial, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(110, 20);
        $pdf->MultiCell(100, 10, 'Nro Autorizacion: ', 0);
        $pdf->SetXY(110, 25);
        $pdf->MultiCell(100, 10, $claveacceso, 0);
        $pdf->SetXY(110, 30);
        if ($decodeXML->infoTributaria->ambiente == 2) {
            $ambiente = 'PRODUCCION';
        } else {
            $ambiente = 'PRUEBAS';
        }
        $pdf->MultiCell(100, 10, 'Ambiente: ' . $ambiente, 0);
        $pdf->SetXY(110, 35);
        if ($decodeXML->infoTributaria->tipoEmision == 1) {
            $emision = 'NORMAL';
        } else {
            $emision = 'NORMAL';
        }
        $pdf->MultiCell(100, 10, 'Emision: ' . $emision, 0);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(10, 20);
        $pdf->MultiCell(100, 10, utf8_decode($decodeXML->infoTributaria->razonSocial), 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(10, 25);
        $pdf->MultiCell(100, 10, $decodeXML->infoTributaria->dirMatriz, 0);
        $pdf->SetXY(10, 30);
        $pdf->MultiCell(100, 10, $contabilidad, 0);
        //Codigo de barras

        $pdf->SetXY(110, 60);
        // $this->generarCodigoBarras($claveacceso);
        $pdf->image('./src/services/uploads/codigo_mod.png', null, null, 100, 20);
        $pdf->SetXY(110, 78);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(100, 10, $claveacceso, 0, 0, "C", true);

        //informacion del cliente
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);

        $pdf->SetXY(10, 35);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(100, 10, "INFORMACION DEL CLIENTE", 0);

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(10, 40);
        $pdf->MultiCell(100, 10, "RUC/CI: " . $decodeXML->infoFactura->identificacionComprador, 0);
        $pdf->SetXY(10, 45);
        $pdf->MultiCell(100, 10, "Razon Social/Nombre: " . $decodeXML->infoFactura->razonSocialComprador, 0);
        $pdf->SetXY(10, 50);
        $pdf->MultiCell(100, 10, "Direccion: " . $decodeXML->infoFactura->direccionComprador, 0);
        $pdf->SetXY(10, 55);
        $pdf->MultiCell(100, 10, "Fecha Emision: " . $decodeXML->infoFactura->fechaEmision, 0);

        $ejeX = 65;

        $pdf->SetXY(10, $ejeX);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(100, 10, "FORMAS DE PAGO", 0);
        $pdf->SetFont('Arial', '', 8);
        $ejeX = $ejeX + 10;
        $pdf->SetXY(10, $ejeX);
        foreach ($decodeXML->infoFactura->pagos->pago as $e => $f) {
            if ($f->formaPago == '01') {
                $pdf->SetXY(22, $ejeX);
                $pdf->Cell(30, 10, 'Sin utilizacion del sistema financiero', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(0, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }
            if ($f->formaPago == '15') {
                $pdf->SetXY(22, $ejeX);
                $pdf->Cell(30, 10, 'Compensacion de deudas', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(0, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }
            if ($f->formaPago == '16') {
                $pdf->SetXY(22, $ejeX);
                $pdf->Cell(30, 10, 'Tarjeta debito', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(0, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }
            if ($f->formaPago == '17') {
                $pdf->SetXY(22, $ejeX);
                $pdf->Cell(30, 10, 'Dinero Electronico', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(0, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }
            if ($f->formaPago == '18') {
                $pdf->SetXY(22, $ejeX);
                $pdf->Cell(30, 10, 'Tarjeta Prepago', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(0, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }
            if ($f->formaPago == '19') {
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(20, 10, 'Tarjeta de credito', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(1, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }
            if ($f->formaPago == '20') {
                $pdf->SetXY(22, $ejeX);
                $pdf->Cell(30, 10, 'Otros con utilizacion del sistema financiero', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(0, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }
            if ($f->formaPago == '21') {
                $pdf->SetXY(22, $ejeX);
                $pdf->Cell(30, 10, 'Endoso de titulos', 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(4, $ejeX);
                $pdf->Cell(30, 10, 'Total: ' . $f->total, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(0, $ejeX);
                $pdf->Cell(30, 10, 'Plazo: ' . $f->plazo, 0, 0, "C", true);
                $ejeX = $ejeX + 6;
                $pdf->SetXY(10, $ejeX);
                $pdf->Cell(30, 10, 'Unidad de tiempo: ' . $f->unidadTiempo, 0, 0, "C", true);
            }

            $ejeX = $ejeX + 10;
            $pdf->SetX($ejeX);
        }





        //detalle de la factura
        $pdf->SetXY(10, $ejeX + 10);
        $pdf->SetFillColor(255, 0, 0);
        $pdf->SetTextColor(0, 255, 255);
        $pdf->Cell(30, 10, "Codigo", 1, 0, "C", true);
        $pdf->Cell(15, 10, "Cod. Aux", 1, 0, "C", true);
        $pdf->Cell(55, 10, "Descripcion", 1, 0, "C", true);
        $pdf->Cell(25, 10, "Cantidad", 1, 0, "C", true);
        $pdf->Cell(25, 10, "Precio", 1, 0, "C", true);
        $pdf->Cell(25, 10, "% Desc", 1, 0, "C", true);
        $pdf->Cell(25, 10, "Total", 1, 0, "C", true);

        $ejeX = $ejeX + 20;
        $pdf->SetXY(10, $ejeX);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);

        foreach ($decodeXML->detalles->detalle as $a => $b) {
            $pdf->Cell(30, 10, $b->codigoPrincipal, 1, 0, "C", true);
            $pdf->Cell(15, 10, $b->codigoAuxiliar, 1, 0, "C", true);
            $pdf->Cell(55, 10, $b->descripcion, 1, 0, "C", true);
            $pdf->Cell(25, 10, $b->cantidad, 1, 0, "C", true);
            $pdf->Cell(25, 10, number_format(floatval($b->precioUnitario), 2), 1, 0, "C", true);
            $pdf->Cell(25, 10, $b->descuento, 1, 0, "C", true);
            $pdf->Cell(25, 10, $b->precioTotalSinImpuesto, 1, 0, "C", true);
            $ejeX = $ejeX + 10;
            $pdf->SetXY(10, $ejeX);
        }

        //Total de la factura



        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(255, 255, 255);
        $iva = 0;
        $ice = 0;
        $IRBPNR = 0;
        $subtotal12 = 0;
        $subtotal0 = 0;
        $subtotal_no_impuesto = 0;
        $subtotal_no_iva = 0;
        foreach ($decodeXML->infoFactura->totalConImpuestos->totalImpuesto as $a => $b) {
            if ($b->codigo == 2) {
                $iva = $b->valor;
                if ($b->codigoPorcentaje == 0) {
                    $subtotal0 = $b->baseImponible;
                }
                if ($b->codigoPorcentaje == 2) {
                    $subtotal12 = $b->baseImponible;
                    //    $iva = $b->valor;
                }
                if ($b->codigoPorcentaje == 6) {
                    $subtotal_no_impuesto = $b->baseImponible;
                }
                if ($b->codigoPorcentaje == 7) {
                    $subtotal_no_iva = $b->baseImponible;
                }
            }
            if ($b->codigo == 3) {
                $ice = $b->valor;
            }
            if ($b->codigo == 5) {
                $IRBPNR = $b->valor;
            }
        }
        $pdf->SetXY(130, $ejeX + 10);
        $pdf->Cell(25, 10, "Subtotal 12%: ", 0, 0, "L", true);
        $pdf->SetXY(180, $ejeX + 10);
        $pdf->Cell(25, 10, " $subtotal12 ", 0, 0, "R", true);
        $pdf->SetXY(130, $ejeX + 16);
        $pdf->Cell(25, 10, "SubTotal 0%: ", 0, 0, "L", true);
        $pdf->SetXY(180, $ejeX + 16);
        $pdf->Cell(25, 10, $subtotal0, 0, 0, "R", true);
        $pdf->SetXY(130, $ejeX + 22);
        $pdf->Cell(25, 10, "SubTotal no sujeto de IVA: ", 0, 0, "L", true);
        $pdf->SetXY(180, $ejeX + 22);
        $pdf->Cell(25, 10, $subtotal_no_impuesto, 0, 0, "R", true);
        $pdf->SetXY(130, $ejeX + 28);
        $pdf->Cell(25, 10, "SubTotal exento de IVA: ", 0, 0, "L", true);
        $pdf->SetXY(180, $ejeX + 28);
        $pdf->Cell(25, 10, $subtotal_no_iva, 0, 0, "R", true);
        $pdf->SetXY(130, $ejeX + 34);
        $pdf->Cell(25, 10, "SubTotal sin Impuestos: ", 0, 0, "L", true);
        $pdf->SetXY(180, $ejeX + 34);
        $pdf->Cell(25, 10, $decodeXML->infoFactura->totalDescuento, 0, 0, "R", true);
        $pdf->SetXY(130, $ejeX + 40);
        $pdf->Cell(25, 10, "Descuento: ", 0, 0, "L", true);
        $pdf->SetXY(180, $ejeX + 40);
        $pdf->Cell(25, 10, $decodeXML->infoFactura->totalDescuento, 0, 0, "R", true);
        $pdf->SetXY(130, $ejeX + 46);
        $pdf->Cell(25, 10, "IVA 12%: ", 0, 0, "L");
        $pdf->SetXY(180, $ejeX + 46);
        $pdf->Cell(25, 10, $iva, 0, 0, "R");
        $pdf->SetXY(130, $ejeX + 52);
        $pdf->Cell(25, 10, "ICE: ", 0, 0, "L");
        $pdf->SetXY(180, $ejeX + 52);
        $pdf->Cell(25, 10, $ice, 0, 0, "R");
        $pdf->SetXY(130, $ejeX + 58);
        $pdf->Cell(25, 10, "IRBPNR: ", 0, 0, "L");
        $pdf->SetXY(180, $ejeX + 58);
        $pdf->Cell(25, 10, $IRBPNR, 0, 0, "R");
        $pdf->SetXY(130, $ejeX + 64);
        $pdf->Cell(25, 10, "Valor Total: ", 0, 0, "L");
        $pdf->SetXY(180, $ejeX + 64);
        $pdf->Cell(25, 10, $decodeXML->infoFactura->importeTotal, 0, 0, "R");

        $infoAdicional = "";
        $correo = "";

        $adicional = $decodeXML->infoAdicional->campoAdicional;


        if(!empty($adicional)){
            
                foreach ($decodeXML->infoAdicional->campoAdicional as $a) {
                    foreach ($a->attributes() as $b) {
                        if ($b == 'Email' || $b == 'email' || $b == '=correo' || $b == 'Correo') {
                            $correo = $a;
                            $infoAdicional .= $b . ': ' . $a . "\n";
                        } else {
                            $infoAdicional .= $b . ': ' . $a . "\n";
                        }
                    }
                }
                
        }

        $pdf->SetXY(10, $ejeX + 10);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->MultiCell(100, 10, "Informacion Adicional", 0);
        $pdf->SetXY(10, $ejeX + 30);
        $pdf->SetFont('Arial', '', 7);
        $pdf->MultiCell(100, 5, "" . $infoAdicional . "", 0);

            $pdf->Output('./comprobantes/'.$claveacceso.'_'. $decodeXML->infoFactura->razonSocialComprador .'.pdf', 'f');
        }
    }





?>