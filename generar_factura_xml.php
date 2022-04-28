<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../jwt/src/BeforeValidException.php';
require_once '../jwt/src/ExpiredException.php';
require_once '../jwt/src/SignatureInvalidException.php';
require_once '../jwt/src/JWT.php';
use \Firebase\JWT\JWT;

define ('SECRET_KEY', '4956andres.'); // la clave secreta puede ser una cadena aleatoria y mantenerse en secreto para cualquier persona
define ('ALGORITMO', 'HS256'); // Algoritmo utilizado para firmar el token

$json = file_get_contents('php://input');

$jsonFactura = json_decode($json);

if (!$jsonFactura) {
    exit("No hay datos para registrar");
}

$jwt = $jsonFactura->token;


try {
    JWT::$leeway = 10;
    $decoded = JWT::decode($jwt, SECRET_KEY, array(ALGORITMO));

    // Access is granted. Add code of the operation here 

    include ("../conexion/bd.php");
    include('modulo11.php');
    
    $empresa = $jsonFactura->nombre_empresa;
    $rucempresa = $jsonFactura->ciruc_empresa;
    $dirempresa = $jsonFactura->direccion_empresa;
    $fechaemision = date("d/m/Y");
    $cliente = $jsonFactura->nombre_cliente;
    $ciruccliente = $jsonFactura->ciruc_cli;
    $tipoidentificacion = $jsonFactura->tipoidentificacion;
    $formapago = $jsonFactura->formapago;
    $emailcliente = $jsonFactura->email_cliente;
    $dircliente = $jsonFactura->direccion_cliente;
    $totalsinimpu = $jsonFactura->totalsinimpu;
    $totalFactura = $jsonFactura->totalFactura;
    $subtotal0 = $jsonFactura->subtotal0;
    $subtotal12 = $jsonFactura->subtotal12;
    $ivatotal = $jsonFactura->ivatotal;
    $number = $jsonFactura->secuencial;

    $itemsfactura = json_decode($jsonFactura->items);
    
    // añado ceros al numero de la factura secuencial se queda en un tamaño de 9 incluidos 0
    // number sera enviado desde el front end, este sera extraido de la base de datos
    $length = 9;
    $secuencial = substr(str_repeat(0, $length).$number, - $length);
    
    $fechaactual = date("dmY");
    
    // 01 factura, 04 nota de credito, 05 nota de debito, 06 guia remision, comprobante de retencion, 07
    $tipocomprobante = '01';

    // 1 pruebas, 2 produccion
    $tipoambiente = '1';

    // sacar de la base de datos
    $serie = '001001';

    // codigo cualquiera de 8 numeros
    $codigonumerico = '12345678';
    $tipoemision = '1';
    
    // orden de la clave de acceso
    // fechaactual ("dmY"), tipo comprobante, ruc, tipoambiente, serie, secuencial, codigonumerico, tipoemision
    // y finalmente digito verificador con modulo 11
    $mod = new Modulo();
    $codigo = $fechaactual.$tipocomprobante.$rucempresa.$tipoambiente.$serie.$secuencial.$codigonumerico.$tipoemision;
    $claveacceso = $codigo.$mod->getmodulo11($codigo);

    $xmlfactura = new XMLWriter();
    $xmlfactura->openURI($claveacceso.".xml"); //creacion del archivo xml con ese nombre

    $xmlfactura->setIndent(true); //valor booleano para establecer niveles de nodos xml y que queden identados
    $xmlfactura->setIndentString("\t"); //corresponde a una tabulacion

    $xmlfactura->startDocument('1.0', 'UTF-8'); //inicio del documento
    $xmlfactura->startElement("factura");
        $xmlfactura->writeAttribute('id', 'comprobante');//atributos de la etiqueta factura
        $xmlfactura->writeAttribute('version', '1.0.0');

        // seccion de informacion tributaria
        $xmlfactura->startElement("infoTributaria"); //inicio de la etiqueta raiz
            $xmlfactura->writeElement("ambiente", "1");
            $xmlfactura->writeElement("tipoEmision", "1");
            $xmlfactura->writeElement("razonSocial", $empresa);
            $xmlfactura->writeElement("nombreComercial", $empresa);
            $xmlfactura->writeElement("ruc", $rucempresa);
            $xmlfactura->writeElement("claveAcceso", $claveacceso);
            $xmlfactura->writeElement("codDoc", '01');
            $xmlfactura->writeElement("estab", '001');
            $xmlfactura->writeElement("ptoEmi", '001');
            $xmlfactura->writeElement("secuencial", $secuencial);
            $xmlfactura->writeElement("dirMatriz", $dirempresa);
        $xmlfactura->endElement(); // fin de la etiqueta raiz


        // inicio de la seccion infofactura
        $xmlfactura->startElement("infoFactura");
            $xmlfactura->writeElement("fechaEmision", $fechaemision);
            $xmlfactura->writeElement("dirEstablecimiento", $dirempresa);
            $xmlfactura->writeELement("obligadoContabilidad", "SI");
            $xmlfactura->writeElement("tipoIdentificacionComprador", $tipoidentificacion);
            $xmlfactura->writeElement("razonSocialComprador", $cliente);
            $xmlfactura->writeElement("identificacionComprador", $ciruccliente);
            $xmlfactura->writeElement("direccionComprador", $dircliente);
            $xmlfactura->writeELement("totalSinImpuestos", $totalsinimpu);
            $xmlfactura->writeElement("totalDescuento", '0.00');

            // inicio de la seccion total con impuestos
            // en esta seccion se coloca los totales del iva 0 e iva 12
            // estos valores son los totales no crear un valor por detalle
            
            $xmlfactura->startElement("totalConImpuestos");

            if($subtotal0 != '0' && $subtotal12 != '0'){

                $xmlfactura->startElement("totalImpuesto");
                        $xmlfactura->writeElement("codigo", '2');
                        $xmlfactura->writeElement("codigoPorcentaje", '0');
                        $xmlfactura->writeELement("baseImponible", $subtotal0);
                        $xmlfactura->writeELement("tarifa", '0');
                        $xmlfactura->writeElement("valor", '0');
                $xmlfactura->endELement();

                $xmlfactura->startElement("totalImpuesto");
                    $xmlfactura->writeElement("codigo", '2');
                    $xmlfactura->writeElement("codigoPorcentaje", '2');
                    $xmlfactura->writeELement("baseImponible", $subtotal12);
                    $xmlfactura->writeELement("tarifa", '12');
                    $xmlfactura->writeElement("valor", $ivatotal);
                $xmlfactura->endELement();

            }else if($subtotal0 != '0' && $subtotal12 == '0'){

                $xmlfactura->startElement("totalImpuesto");
                        $xmlfactura->writeElement("codigo", '2');
                        $xmlfactura->writeElement("codigoPorcentaje", '0');
                        $xmlfactura->writeELement("baseImponible", $subtotal0);
                        $xmlfactura->writeELement("tarifa", '0');
                        $xmlfactura->writeElement("valor", '0');
                $xmlfactura->endELement();

            }else if($subtotal12 != '0' && $subtotal0 == '0'){

                $xmlfactura->startElement("totalImpuesto");
                    $xmlfactura->writeElement("codigo", '2');
                    $xmlfactura->writeElement("codigoPorcentaje", '2');
                    $xmlfactura->writeELement("baseImponible", $subtotal12);
                    $xmlfactura->writeELement("tarifa", '12');
                    $xmlfactura->writeElement("valor", $ivatotal);
                $xmlfactura->endELement();
            }
            
            $xmlfactura->endELement();

            $xmlfactura->writeElement("propina", '0.00');
            // el importe total es el valor final de la factura debes colocar tambien
            $xmlfactura->writeElement("importeTotal", $totalFactura);
            $xmlfactura->writeElement("moneda", "DOLAR");

            // inicion de la seccion pagos
            $xmlfactura->startElement("pagos");
                $xmlfactura->startElement("pago");
                    $xmlfactura->writeElement("formaPago", $formapago);
                    $xmlfactura->writeElement("total", $totalFactura);
                $xmlfactura->endElement();
            $xmlfactura->endElement();
        $xmlfactura->endElement();

        // inicio seccion detalles de la factura
        $xmlfactura->startElement("detalles");
            // Aqui realiza un bucle para colocar todos los detalles que tengas 
            for ($i = 0; $i < count($itemsfactura); $i++){

                // comprobamos el tipo de item para colocarlo con IVA o SIN EL

                // comprobacion del iva cerpo 0
                if($itemsfactura[$i]->IVA_proser == '0'){

                    $xmlfactura->startElement("detalle");
                        $xmlfactura->writeElement("codigoPrincipal", $itemsfactura[$i]->id_proser);
                        $xmlfactura->writeElement("descripcion", $itemsfactura[$i]->nombre_proser);
                        $xmlfactura->writeElement("cantidad", $itemsfactura[$i]->cantidadvendida);
                        $xmlfactura->writeElement("precioUnitario", $itemsfactura[$i]->precio_proser);
                        $xmlfactura->writeElement("descuento", '0.00');
                        $xmlfactura->writeElement("precioTotalSinImpuesto", $itemsfactura[$i]->subtotal0);
            
                        // inicio de seccion impuestos
                        $xmlfactura->startElement("impuestos");
                            $xmlfactura->startElement("impuesto");
                                // el codigo hace referencia a 
                                // 2 = IVA
                                // 3 = ICE
                                // 5 = IRBPNR
                                $xmlfactura->writeElement("codigo", "2");

                                // el codigo del porcentaje es en base a 
                                // 0 = 0%
                                // 2 = 12%
                                // 3 = 14%
                                // 6 = no objeto iva 
                                // 7 = excento de iva
                                $xmlfactura->writeElement("codigoPorcentaje", "0");


                                // el codigo de la tarifa es en base al porcentaje del IVA
                                $xmlfactura->writeElement("tarifa", "0.00");


                                $xmlfactura->writeElement("baseImponible", $itemsfactura[$i]->subtotal0);
                                $xmlfactura->writeElement("valor", "0.00");
                            $xmlfactura->endElement();
                        $xmlfactura->endElement();
                    $xmlfactura->endElement();

                }else if($itemsfactura[$i]->IVA_proser == '12'){

                    $xmlfactura->startElement("detalle");
                        $xmlfactura->writeElement("codigoPrincipal", $itemsfactura[$i]->id_proser);
                        $xmlfactura->writeElement("descripcion", $itemsfactura[$i]->nombre_proser);
                        $xmlfactura->writeElement("cantidad", $itemsfactura[$i]->cantidadvendida);
                        $xmlfactura->writeElement("precioUnitario", $itemsfactura[$i]->precio_proser);
                        $xmlfactura->writeElement("descuento", '0.00');
                        $xmlfactura->writeElement("precioTotalSinImpuesto", $itemsfactura[$i]->subtotal12);
            
                        // inicio de seccion impuestos
                        $xmlfactura->startElement("impuestos");
                            $xmlfactura->startElement("impuesto");
                                // el codigo hace referencia a 
                                // 2 = IVA
                                // 3 = ICE
                                // 5 = IRBPNR
                                $xmlfactura->writeElement("codigo", "2");

                                // el codigo del porcentaje es en base a 
                                // 0 = 0%
                                // 2 = 12%
                                // 3 = 14%
                                // 6 = no objeto iva 
                                // 7 = excento de iva
                                $xmlfactura->writeElement("codigoPorcentaje", "2");


                                // el codigo de la tarifa es en base al porcentaje del IVA
                                $xmlfactura->writeElement("tarifa", "12");

                                $xmlfactura->writeElement("baseImponible", $itemsfactura[$i]->subtotal12);
                                $xmlfactura->writeElement("valor",  $itemsfactura[$i]->iva12);
                            $xmlfactura->endElement();
                        $xmlfactura->endElement();
                    $xmlfactura->endElement();

                }
                
            }
        $xmlfactura->endElement();

        // inicio de seccion para la informacion adicional 
        // usa para poner fondo social 
        // consumo del agua etc etc
        $xmlfactura->startElement("infoAdicional");

            $xmlfactura->startElement("campoAdicional");
                $xmlfactura->writeAttribute("nombre", "Leyenda");
                $xmlfactura->text("Contribuyente Regimen RIMPE");
            $xmlfactura->endElement();

            $xmlfactura->startElement("campoAdicional");
                $xmlfactura->writeAttribute("nombre", "email");
                $xmlfactura->text($emailcliente);
            $xmlfactura->endElement();

        $xmlfactura->endElement();
        

    $xmlfactura->endElement(); // fin de la etiqueta raiz
    $xmlfactura->endDocument(); // fin del documento

    // nombre del archivo primero y luego la ruta
    rename($claveacceso.".xml", "xmlgenerados/$claveacceso".".xml");

    $data_insert=array(
        "data" => array(
            'claveacceso' => $claveacceso,									
            'secuencial' => $number
        ),
        "status" => "success",
        "message" => "Request authorized"
    );  

}catch (Exception $e){

    http_response_code(401);

    $data_insert=array(
        //"data" => $data_from_server,
        "jwt" => $jwt,
        "status" => "error",
        "message" => $e->getMessage()
    );
    
}

echo json_encode($data_insert);