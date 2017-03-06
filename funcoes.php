<?php

if (!function_exists('logTXT')) {

    function logTXT($data, $filename = "log.txt") {
        $f = @fopen($filename, 'a+');
        $fcontents = fread($f, filesize($filename));
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, "\n");
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }

}

// Função para inserção dos valores no banco de dados
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
    if (PHP_VERSION < 6) {
        $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }
    $conn = mysqli_connect("localhost", "root", "senha.123", "protegemed");
    $value = mysqli_real_escape_string($conn, $theValue);

    switch ($theType) {
        case "text":
            $value = ($value != "") ? "'" . $value . "'" : "NULL";
            break;
        case "long":
        case "int":
            $value = ($value != "") ? intval($value) : "NULL";
            break;
        case "double":
            $value = ($value != "") ? doubleval($value) : "NULL";
            break;
        case "date":
            $value = ($value != "") ? "'" . $value . "'" : "NULL";
            break;
        case "defined":
            $value = ($value != "") ? $theDefinedValue : $theNotDefinedValue;
            break;
    }
    mysqli_close($conn);
    return $value;
}

// Fun��o que transforma os valores float 32 para valores hexadecimais (IEEE-754)
function ieee_float($f) {
    $value = (float) $f;
    $b = pack("f", $value);
    //$hexa = array();
    for ($i = 0; $i < strlen($b); $i++) {
        $c = ord($b{$i});
        $hexa[] = sprintf("%02X", $c);
    }
    $hex = '';
    for ($i = strlen($hexa); $i >= 0; $i--) {
        $hex.=$hexa[$i];
    }

    return $hex;
}

// Fun��o que transforma os valores hexadecimais para valores float 32 (IEEE-754)
function hex2float32($hex) {
    // Gera sequencia bin�ria. OBS: concatena '1' no in�cio para n�o perder ZEROS, mas logo ap�s retira-o com SUBSTR
    $binario = substr(base_convert('1' . $hex, 16, 2), 1);

    $sinal = substr($binario, 0, 1); // 1 bit 0 ou 1
    $exp = substr($binario, 1, 8); // 8 bits para o expoente
    $valor = substr($binario, 9); // Inicia do bit 9 para a mantissa

    $fracional = 0;
    for ($i = 0; $i < strlen($valor); $i++) {
        // Aplica a formula:  2**-1  +  2**-2  +  2**-3  +  ...  +  2**-n  ::  IEEE-754
        $fracional += pow(2, ($i + 1) * -1) * substr($valor, $i, 1);
    }
    $mant = 1;
    if (bindec($exp) == 0) {
        $mant = 0;
    }
    // Aplica a formula:  -1**sign  *  1 + fractional  *  2**exp-127  ::  IEEE-754
    //FIXME: Eliminar 1 + do $fracional quando o expoente for -127
    return pow(-1, $sinal) * ( $mant + $fracional ) * pow(2, bindec($exp) - 127);
}
