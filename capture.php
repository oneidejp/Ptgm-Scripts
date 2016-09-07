<?php

//carrega funcoes
require_once("funcoes.php");

//define variaveis de teste
$error_test = true;
$error = "";

//conecta no banco
$conn = mysqli_connect("localhost", "root", "senha.123", "protegemed");
if (!$conn) {
    logTXT("Error: Unable to connect to MySQL." . PHP_EOL);
    logTXT("Debugging errno: " . mysqli_connect_errno() . PHP_EOL);
    logTXT("Debugging error: " . mysqli_connect_error() . PHP_EOL);
}

//recebe dados do POST
$RFID = -1;
if ((isset($_POST['RFID'])) && ($_POST['RFID'])) {
    $RFID = $_POST['RFID'];
} else {
    $error .= 'error: RFID nao infomado!<br>';
}
if ((isset($_POST['TYPE'])) && ($_POST['TYPE']))
    $TYPE = $_POST['TYPE'];
else
    $error .= 'error: TYPE nao informado!<br>';

if ((isset($_POST['OUTLET'])) && ($_POST['OUTLET']))
    $OUTLET = $_POST['OUTLET'];
else
    $error .= 'error: OUTLET nao informado!<br>';

if ((isset($_POST['MV'])) && ($_POST['MV']))
    $MEAN_VAL = $_POST['MV'];
else
    $MEAN_VAL = 0;

if ((isset($_POST['MV2'])) && ($_POST['MV2']))
    $MEAN_VAL2 = $_POST['MV2'];
else
    $MEAN_VAL2 = 0;

if ((isset($_POST['OFFSET'])) && ($_POST['OFFSET']))
    $OFFSET = $_POST['OFFSET'];
else
    $error .= 'error: OFFSET nao informado!<br>';

if ((isset($_POST['GAIN'])) && ($_POST['GAIN']))
    $GAIN = $_POST['GAIN'];
else
    $error .= 'error: GAIN nao informado!<br>';

if ((isset($_POST['RMS'])) && ($_POST['RMS']))
    $RMS = $_POST['RMS'];
else
    $error .= 'error: RMS nao informado!<br>';

if ((isset($_POST['UNDER'])) && ($_POST['UNDER']))
    $UNDER = $_POST['UNDER'];
else
    $error .= 'error: Numero de Underflow (UNDER) nao informado!<br>';

if ((isset($_POST['OVER'])) && ($_POST['OVER']))
    $OVER = $_POST['OVER'];
else
    $error .= 'error: Numero de Overflow (OVER) nao informado!<br>';

if ((isset($_POST['DURATION'])) && ($_POST['DURATION']))
    $DURATION = $_POST['DURATION'];
else
    $error .= 'error: Duracao da Fuga informada!<br>';

if ((isset($_POST['SIN'])) && ($_POST['SIN']))
    $SINE = $_POST['SIN'];

if ((isset($_POST['COS'])) && ($_POST['COS']))
    $COSINE = $_POST['COS'];

// QUEBRA OS VALORES SEPARADOS POR ';' :::::::::::::::::::
$SINE = explode(';', $SINE);
if (count($SINE) != 12) {
    $error .= 'error: SENO nao contem 12 valores!<br>';
}
$COSINE = explode(';', $COSINE);
if (count($COSINE) != 12) {
    $error .= 'error: COSSENO nao contem 12 valores!<br>';
}

// :::::::::::::::::::::::::::::::::::::::::::::::::::::::
if ($error != '') {
    logTXT("Erro em obter dados do POST.\n" . $error);
    $error_test = false;
}

// Pesquisa o tipo de evento na tabela tipos_eventos para o evento da mensagem
$query_rsTipoEvento = "SELECT * FROM eventos WHERE codEvento = '$TYPE'";
$result = mysqli_query($conn, $query_rsTipoEvento);
if (!$result) {
    logTXT("Erro selecionando eventos.\n" . $conn->error);
    $error_test = false;
} else {
    $row_rsTipoEvento = mysqli_fetch_assoc($result);
    $totalRows_rsTipoEvento = mysqli_num_rows($result);
    mysqli_free_result($result);
}
//echo "Acessou MYSQL <br>";
if ($totalRows_rsTipoEvento) {
    $IDEVT = $row_rsTipoEvento['codEvento'];
    if ($IDEVT == 1 || $IDEVT == 3 || $IDEVT == 6 || $IDEVT == 9 || $IDEVT == 10)
        $TIPOEVT = 2;
    else
        $TIPOEVT = 1;
}
else {
    logTXT("Tipo de evento nao localizado.");
    $error_test = false;
}


// Pesquisa pelo RFID o ID do equipamento informado
$rsEquipamento = "SELECT * FROM equipamento WHERE rfid= '$RFID'";
$result = mysqli_query($conn, $rsEquipamento);
if (!$result) {
    logTXT("Erro selecionando equipamento.\n" . $conn->error);
    $error_test = false;
} else {
    $row_rsEquipamento = mysqli_fetch_assoc($result);
    $totalRows_rsEquipamento = mysqli_num_rows($result);
    mysqli_free_result($result);
}
if ($totalRows_rsEquipamento) {
    $IDEQ = $row_rsEquipamento['codEquip'];
} else {
    logTXT("RFID nao encontrado na tabela EQUIPAMENTO.");
    $error_test = false;
}

if ($error == '') {
    //inserir no capturaatual
    $captureSQL = sprintf("INSERT INTO capturaatual (codTomada,codTipoOnda,codEquip,codEvento,valorMedio,VM2,offset,gain,eficaz,dataAtual, under,over,duration) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,NOW(),%s,%s,%s)", GetSQLValueString($OUTLET, 'int'), GetSQLValueString($TIPOEVT, 'int'), GetSQLValueString($IDEQ, 'int'), GetSQLValueString($IDEVT, 'int'), GetSQLValueString(hex2float32($MEAN_VAL), 'double'), GetSQLValueString(hex2float32($MEAN_VAL2), 'double'), GetSQLValueString($OFFSET, 'int'), GetSQLValueString(hex2float32($GAIN), 'double'), GetSQLValueString(hex2float32($RMS), 'double'), GetSQLValueString($UNDER, 'int'), GetSQLValueString($OVER, 'int'), GetSQLValueString($DURATION, 'int'));
    //execute sql
    //logTXT("Capture SQL: .\n" . $captureSQL);
    $result = mysqli_query($conn, $captureSQL);
    if (!$result) {
        logTXT("Erro inserindo capturaatual.\n" . $conn->error);
        $error_test = false;
    } else {
        $lastid = mysqli_insert_id($conn);
    }

    for ($i = 0; $i < 12; $i++) {
        $ondaSQL = sprintf("INSERT INTO harmatual (codCaptura,codHarmonica,sen,cos) VALUES (%s,%s,%s,%s)", GetSQLValueString($lastid, 'int'), GetSQLValueString($i + 1, 'int'), GetSQLValueString(hex2float32($SINE[$i]), 'double'), GetSQLValueString(hex2float32($COSINE[$i]), 'double'));
        //Execute SQL
        $result = mysqli_query($conn, $ondaSQL);

        if (!$result) {
            logTXT("Erro inserindo harmatual[{$i}].\n" . $conn->error);
            logTXT("CodCaptura: {$lastid}].");
            $error_test = false;
        }
    }
}

mysqli_close($conn);
if ($error_test) {
    $log = "CodCaptura {$lastid} inserido corretamente no banco as ";
    $log .= date("H:i:s") . " do dia " . date("d-m-Y");
    logTXT($log, "LogOK.txt");
}
