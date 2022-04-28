<?php

session_start();
require_once('../../lib/nusoap.php');
require_once('class/generarPDF.php');

$claveAcceso = $_POST['claveAcceso'];
$service = $_POST['service'];


//EndPoint
//Endpoint de pruebas cambia a produccion
$servicio = "https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl"; //url del servicio
$parametros = array(); //parametros de la llamada
$parametros['claveAccesoComprobante'] = $claveAcceso;

$client = new nusoap_client($servicio);


$error = $client->getError();



$client->soap_defencoding = 'utf-8';


$result = $client->call("autorizacionComprobante", $parametros, "http://ec.gob.sri.ws.autorizacion");
$_SESSION['autorizacionComprobante'] = $result;
$response = array();

$file = fopen("../../log.txt", "a+");
fwrite($file, "Servicio: " . $service . PHP_EOL);
fwrite($file, "Clave Acceso: " . $claveAcceso . PHP_EOL);




if ($client->fault) {

    fwrite($file, "Respuesta: " . print_r($result, true) . PHP_EOL);

    $file_error = fopen('../../errores/' . $claveAcceso . ".txt", "w");
    fwrite($file_error, "Servicio: " . $service . PHP_EOL);
    fwrite($file_error, "Clave Acceso: " . $claveAcceso . PHP_EOL);
    fwrite($file_error, "Respuesta: " . print_r($result, true) . PHP_EOL);
    fwrite($file_error, "\n__________________________________________________________________\n" . PHP_EOL);
    fclose($file_error);
    echo serialize($result);
} else {
    $error = $client->getError();
    if ($error) {

        fwrite($file, "Respuesta: " . print_r($error, true) . PHP_EOL);

        $file_error = fopen('../../errores/' . $claveAcceso . ".txt", "w");
        fwrite($file_error, "Servicio: " . $service . PHP_EOL);
        fwrite($file_error, "Clave Acceso: " . $claveAcceso . PHP_EOL);
        fwrite($file_error, "Respuesta: " . print_r($error, true) . PHP_EOL);
        fwrite($file_error, "\n__________________________________________________________________\n" . PHP_EOL);
        fclose($file_error);
        echo serialize($error);
    } else {

       echo serialize($result);
        fwrite($file, "Respuesta: " . print_r($result, true) . PHP_EOL);
        if ($result['autorizaciones']['autorizacion']['estado'] != 'AUTORIZADO') {

            $file_error = fopen('../../errores/' . $claveAcceso . ".txt", "w");
            fwrite($file_error, "Servicio: " . $service . PHP_EOL);
            fwrite($file_error, "Clave Acceso: " . $claveAcceso . PHP_EOL);
            fwrite($file_error, "Respuesta: " . print_r($result, true) . PHP_EOL);
            fwrite($file_error, "\n__________________________________________________________________\n" . PHP_EOL);
            fclose($file_error);
        } else {
            if (!empty($result['autorizaciones']['autorizacion']['comprobante'])) {
                $file_comprobante = fopen('../../comprobantes/' . $claveAcceso . ".xml", "w");
                $comprobante = $client->responseData;


                $simplexml = simplexml_load_string(utf8_encode($comprobante));
                $dom = new DOMDocument('1.0');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $xml = str_replace(['&lt;', '&gt;'], ['<', '>'], $comprobante);

                fwrite($file_comprobante, $xml . PHP_EOL);
                fclose($file_comprobante);
                

                $dataComprobante = simplexml_load_string(utf8_encode($result['autorizaciones']['autorizacion']['comprobante']));
                if ($dataComprobante->infoFactura) {
                    //     var_dump($dataComprobante->infoFactura);

                    // $facturaPDF = new generarPDF();
                    // $facturaPDF->facturaPDF($dataComprobante, $claveAcceso);
                }
                if ($dataComprobante->infoNotaCredito) {
                    //     var_dump($dataComprobante->infoFactura);
                    // $facturaPDF = new generarPDF();
                    // $facturaPDF->notaCreditoPDF($dataComprobante, $claveAcceso);
                }
                if ($dataComprobante->infoCompRetencion) {
                    //     var_dump($dataComprobante->infoFactura);
                    // $facturaPDF = new generarPDF();
                    // $facturaPDF->comprobanteRetencionPDF($dataComprobante, $claveAcceso);
                }
                if ($dataComprobante->infoGuiaRemision) {
                    //     var_dump($dataComprobante->infoFactura);
                    // $facturaPDF = new generarPDF();
                    // $facturaPDF->guiaRemisionPDF($dataComprobante, $claveAcceso);
                }

                if ($dataComprobante->infoNotaDebito) {
                    //     var_dump($dataComprobante->infoFactura);
                    // $facturaPDF = new generarPDF();
                    // $facturaPDF->notaDebitoPDF($dataComprobante, $claveAcceso);
                }
            }
        }
    }
}
fwrite($file, "\n__________________________________________________________________\n" . PHP_EOL);
fclose($file);


echo json_encode(
    array(
        'resultado' => $result
    )
    );





