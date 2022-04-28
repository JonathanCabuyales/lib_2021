<?php

class Modulo {
    
    function getmodulo11($num){

        $digitos = str_replace( array('.',','), array(''.''), strrev($num));
        if(!ctype_digit($digitos)){
            return false;
        }
        $sum = 0;
        $factor = 2;

        for ($i=0; $i < strlen($digitos); $i++) { 
            $sum += substr($digitos, $i, 1) * $factor;

            if($factor == 7){
                $factor = 2;
            }else{
                $factor++;
            }
        }

        $dv = 11 - ($sum % 11);
        if($dv == 11) {
            return 0;
        }
        if($dv == 10){
            return 1;
        }
        return $dv;
    }

}

// $fechaactual = date("dmY");
// // 01 factura, 04 nota de credito, 05 nota de debito, 06 guia remision, comprobante de retencion, 07
// $tipocomprobante = '01';
// $ruc = '1792919320001';
// // 1 pruebas, 2 produccion
// $tipoambiente = '1';
// // sacar de la base de datos
// $serie = '001001';
// // sacar de la base de datos
// $secuencial = '000000746';
// // codigo cualquiera de 8 numeros
// $codigonumerico = '12345678';
// $tipoemision = '1';

// $mod = new Modulo();

// $codigo = $fechaactual.$tipocomprobante.$ruc.$tipoambiente.$serie.$secuencial.$codigonumerico.$tipoemision;
// echo "el digito verificador es: ".$mod->getmodulo11($codigo);
// echo "<br>";
// echo "codigo: ".$codigo;
// echo "<br>";
// echo "Unido:. ".$codigo.$mod->getmodulo11($codigo);

?>