<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$dataPOST = $_POST['data'];
$id_prefac = $_POST['id_prefac'];

$jsonEncode = json_encode($dataPOST, true);
$dividir = explode(':', $dataPOST);

// $dividir = explode(';', $dataPOST);

$fecha = $dividir[30].$dividir[31].":".$dividir[32].":".$dividir[33];
$estado = $dividir[22];
$num_autorizacion = $dividir[26];


try{
    $bd = "localhost";
    $contrasena = "";
    $usuario = "root";
    $nombre_base_de_datos = "jaapssa_contable";

    // $bd = "localhost";
    // $contrasena = "4956andres";
    // $usuario = "jaapssa_vtAND";
    // $nombre_base_de_datos = "jaapssa_contable";

    // $con = new PDO('mysql:host=localhost;dbname=' . $nombre_base_de_datos, $usuario, $contraseña);
    $con = mysqli_connect($bd, $usuario, $contrasena, $nombre_base_de_datos);

    mysqli_set_charset($con,"utf8");

    if(!$con){
        die("Connection Failed :". mysqli_connect_error());
    }
    $query = "INSERT INTO facturas_aprobadas(
        fecha_acceso,
        num_autorizacion,
        estado,
        id_prefac
    ) VALUES(
        '$fecha',
        '$num_autorizacion',
        '$estado',
        '$id_prefac'
    )";

    $result = mysqli_query($con, $query);
    if($result){
        echo json_encode(
            array(
                'longitud' => strlen($dataPOST),
                'estado' => $estado,
                'fecha' => $fecha,
                'numeroAutorizacion' => $num_autorizacion,
                'data' => $dividir,
                'dataPOST' => $dataPOST,
                'json' => $jsonEncode,
                'id_prefac' => $id_prefac,
                'insertado' => $result
            ),
            true
            );
            
        }else{

            echo json_encode(
                array(
                    'longitud' => strlen($dataPOST),
                    'estado' => $estado,
                    'fecha' => $fecha,
                    'numeroAutorizacion' => $num_autorizacion,
                    'data' => $dividir,
                    'dataPOST' => $dataPOST,
                    'json' => $jsonEncode,
                    'id_prefac' => $id_prefac,
                    'insertado' => $result
                ),
                true
                );
        }
}catch(Exception $e){

}


/* echo json_encode(
    array(
        'data' => ($dataPOST),
        'sri' => $dataPOST['estado']
    ),
    true
); */