<?php

$mensaje = $_POST['mensaje'];


$file = fopen("../facturaFirmada.xml", "w");

$escritoFactura = fwrite($file, $mensaje . PHP_EOL);

fclose($file);

echo (
    json_encode(
        array(
            'mensaje' => $escritoFactura
        )
    )
    );