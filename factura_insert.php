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

$id_usuario = $jsonFactura->id_usuario;
$nombre_empresa = $jsonFactura->nombre_empresa;
$ciruc_empresa = $jsonFactura->ciruc_empresa;
$direccion_empresa = $jsonFactura->direccion_empresa;
$nombre_cliente = $jsonFactura->nombre_cliente;
$direccion_cliente = $jsonFactura->direccion_cliente;
$ciruc_cliente = $jsonFactura->ciruc_cliente;
$email_cliente = $jsonFactura->email_cliente;
$totalFactura = $jsonFactura->totalFactura;
$totalsinimpu = $jsonFactura->totalsinimpu;
$tipoidentificacion = $jsonFactura->tipoidentificacion;
$formapago = $jsonFactura->formapago;
$subtotal0 = $jsonFactura->subtotal0;
$subtotal12 = $jsonFactura->subtotal12;
$ivatotal = $jsonFactura->ivatotal;
$items = $jsonFactura->items;
$numeroautorizacion = $jsonFactura->numeroautorizacion;
$secuencial = $jsonFactura->secuencial;


try {
    JWT::$leeway = 10;
    $decoded = JWT::decode($jwt, SECRET_KEY, array(ALGORITMO));

    // Access is granted. Add code of the operation here 

    include ("../conexion/bd.php");

    $data=array();
    
    $query = "INSERT INTO facturas (
        id_usuario,
    	numeroautorizacion,
    	secuencial,
    	items,
    	subtotal0,
    	subtotal12,
    	ivatotal,
    	totalsinimpu,
    	totalFactura,
    	formapago,
    	tipoidentificacion,
    	nombre_cliente,
    	direccion_cliente,
    	ciruc_cliente,
    	email_cliente,
    	nombre_empresa,
    	ciruc_empresa,
    	direccion_empresa) 
    VALUES ('$id_usuario',
        '$numeroautorizacion',
        '$secuencial',
        '$items',
        '$subtotal0',
        '$subtotal12',
        '$ivatotal',
        '$totalsinimpu',
        '$totalFactura',
        '$formapago',
        '$tipoidentificacion',
        '$nombre_cliente',
        '$direccion_cliente',
        '$ciruc_cliente',
        '$email_cliente',
        '$nombre_empresa',
        '$ciruc_empresa',
        '$direccion_empresa')";

    $insert = mysqli_query($con, $query);

    $data_insert=array(
        "data" => $insert,
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