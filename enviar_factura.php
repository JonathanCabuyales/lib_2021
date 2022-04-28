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
// $numeroautorizacion = $jsonFactura->claveacceso;



try {
    JWT::$leeway = 10;
    $decoded = JWT::decode($jwt, SECRET_KEY, array(ALGORITMO));

    // Access is granted. Add code of the operation here 

    include ('ejecutar.php');

    $firmaFactura='';
    $validarContraseña='';
    $validarVigencia='';

    if(!empty($_POST['respuestaFirmarFactura'])){
        
        
    $firmaFactura = $_POST['respuestaFirmarFactura'];
    // echo "firmaFactura: ".$firmaFactura[0]."";
    // echo "<br>";

    // echo "firmaFactura: ".$firmaFactura[1]."";
    // echo "<br>";

    }


    if(!empty($_POST['respuestaValidarContraseña'])){
    $validarContraseña = $_POST['respuestaValidarContraseña'];
    /*
    echo "validarContraseña: ".$validarContraseña[0]."";
    echo "<br>";
    */
    }


    if(!empty($_POST['respuestaValidarVigencia'])){

    $validarVigencia = $_POST['respuestaValidarVigencia'];

    /*
    echo "validarVigencia: ".$validarVigencia[0]."";
    echo "<br>";
    */

    }


    if(!empty($validarContraseña)){
        //Put Code
        
        $file = fopen("recibe.txt", "a+");
        fwrite($file, $validarContraseña .PHP_EOL);
        var_dump($validarContraseña);
        
        
    }
    if(!empty($validarVigencia)){

    //Put Code
        
        $file = fopen("recibe.txt", "w+");
        fwrite($file,$validarVigencia[0] .PHP_EOL);
        fwrite($file, $validarVigencia[1] .PHP_EOL);
        fwrite($file, $validarVigencia[2].PHP_EOL);
        var_dump($validarVigencia);
    
    //Put Code  
    }

    if (!empty($firmaFactura)) {
        
        $validarComprobante = $firmaFactura[0];
        $autorizacionComprobante = $firmaFactura[1];
        
        var_dump($validarComprobante);
        var_dump($autorizacionComprobante);
        
        //Put Code
        
    }


    /*$ruta_factura= 'http://localhost/libreria/'.$comp.'.xml';
    $ruta_certificado= 'http://localhost/libreria/DANNY JOSE MERO CEDENO 210820133524.p12';
    $contraseña= 'danny1985';
    $ruta_respuesta= 'http://localhost/libreria/example.php';*/


    /*$ruta_factura= 'http://localhost/libreria_2021/guiaRemision.xml';
    $ruta_certificado= 'http://localhost/libreria_2021/angel_martin_pisculla_coque.p12';
    $contraseña= 'Alejandro2014';
    $ruta_respuesta= 'http://localhost/libreria_2021/example.php';*/

    $ruta_factura= 'http://localhost/VT/APIVTPROYECTOS/libreria_2021/xmlgenerados/0302202201172798762800110010010000000101234567811.xml';
    // $ruta_certificado= 'http://localhost/libreria_2021/nelly_maria_cuenca_macas.p12';
    $ruta_certificado = 'http://localhost/VT/APIVTPROYECTOS/libreria_2021/DAISY FERNANDA CAIZA TIPAN 290921160443.p12';
    $contraseña= 'Caizad2021';
    $ruta_respuesta= 'http://localhost/VT/APIVTPROYECTOS/libreria_2021/example.php';


    $ejecutar = new ejecutar();
    $domain_dir = $_SERVER['SERVER_NAME'];

    //Validar Contraseña del certificado
        // $ejecutar->validarContraseña($ruta_certificado,$contraseña,$ruta_respuesta);


    //Validar Vigencia del certificado
        // $ejecutar->validarVigencia($ruta_certificado,$contraseña,$ruta_respuesta);


    //Firmar Factura y enviar a SRI
    $ejecutar->firmarFactura($ruta_factura,$ruta_certificado,$contraseña,$ruta_respuesta);

    

    $data_insert=array(
        "data" => $ejecutar,
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