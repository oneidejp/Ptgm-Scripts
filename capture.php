<?php require_once("conn.php"); ?>
<?php
include 'funcoes.php';
//include 'banco.php';

$error = '';

//echo "T=" . round(microtime(true) * 1000);

// RECEBE DADOS :::::::::::::::::::::::::::::::::::::::::
$RFID = -1;
if( (isset($_POST['RFID'])) && ($_POST['RFID']) )
	$RFID = $_POST['RFID'];
else
	$error .= 'error: RFID nao infomado!<br>';

if( (isset($_POST['TYPE'])) && ($_POST['TYPE']) )
	$TYPE = $_POST['TYPE'];
else
	$error .= 'error: TYPE nao informado!<br>';

if( (isset($_POST['OUTLET'])) && ($_POST['OUTLET']) )
	$OUTLET = $_POST['OUTLET'];
else
	$error .= 'error: OUTLET nao informado!<br>';

if( (isset($_POST['MV'])) && ($_POST['MV']) )
	$MEAN_VAL = $_POST['MV'];
else
	$MEAN_VAL = 0;

if( (isset($_POST['MV2'])) && ($_POST['MV2']) )
	$MEAN_VAL2 = $_POST['MV2'];
else
	$MEAN_VAL2 = 0;

if( (isset($_POST['OFFSET'])) && ($_POST['OFFSET']) )
	$OFFSET = $_POST['OFFSET'];
else
	$error .= 'error: OFFSET nao informado!<br>';

if( (isset($_POST['GAIN'])) && ($_POST['GAIN']) )
	$GAIN = $_POST['GAIN'];
else
	$error .= 'error: GAIN nao informado!<br>';

if( (isset($_POST['RMS'])) && ($_POST['RMS']) )
	$RMS = $_POST['RMS'];
else
	$error .= 'error: RMS nao informado!<br>';

if( (isset($_POST['UNDER'])) && ($_POST['UNDER']) )
	$UNDER = $_POST['UNDER'];
else
	$error .= 'error: Numero de Underflow (UNDER) nao informado!<br>';

if( (isset($_POST['OVER'])) && ($_POST['OVER']) )
	$OVER = $_POST['OVER'];
else
	$error .= 'error: Numero de Overflow (OVER) nao informado!<br>';

if( (isset($_POST['DURATION'])) && ($_POST['DURATION']) )
	$DURATION = $_POST['DURATION'];
else
	$error .= 'error: Duracao da Fuga informada!<br>';

if( (isset($_POST['SIN'])) && ($_POST['SIN']) )
	$SINE = $_POST['SIN'];

if( (isset($_POST['COS'])) && ($_POST['COS']) )
	$COSINE = $_POST['COS'];

	echo "RFID " . $RFID ;
	echo "Type " . $TYPE;
	echo "Outlet" . $OUTLET;
	echo "VMED " . $MEAN_VAL;
	echo "OFFSET " . $OFFSET;
	echo "GAIN " . $GAIN;
	echo "RMS " . $RMS;
	echo "SENO " . $SINE;
	echo "COSS " . $COSINE;

	$f = fopen("file.txt", "w");
	fwrite($f, print_r("RFID: " . $RFID, true));
	fwrite($f, print_r("\nType: " . $TYPE, true));
	fwrite($f, print_r("\nOutlet: " . $OUTLET, true));
	fwrite($f, print_r("\nVMED: " . $MEAN_VAL, true));
	fwrite($f, print_r("\nOFFSET: " . $OFFSET, true));
	fwrite($f, print_r("\nGAIN: " . hex2float32($GAIN), true));
	fwrite($f, print_r("\nRMS: " . hex2float32($RMS), true));
	fwrite($f, print_r("\nSENO: " . $SINE, true));
	fwrite($f, print_r("\nCOSS: " . $COSINE, true));

//Debug
/*
$RFID = '12345678';
$TYPE = '02';
$OUTLET = '1';
$MEAN_VAL = '00000000';
$OFFSET = '00000000';
$GAIN = '00000000';
$RMS = '00000000';

$SINE = '00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000';
$COSINE = '00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000;00000000';
*/

//if((intval($TYPE) <= 3) && (intval($TYPE) > 0))
//{
	// QUEBRA OS VALORES SEPARADOS POR ';' :::::::::::::::::::
	$SINE = explode(';', $SINE);
	if(count($SINE) != 12)
		$error .= 'error: SENO nao contem 12 valores!<br>';

	$COSINE = explode(';', $COSINE);
	if(count($COSINE) != 12)
		$error .= 'error: COSSENO nao contem 12 valores!<br>';
    //echo "Sen e Cos Quebrados <br>";
//}
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::

$EVT = $TYPE;	//obt�m o valor de codEvento


// Pesquisa o tipo de evento na tabela tipos_eventos para o evento da mensagem
mysql_select_db($database_conn, $conn);
$query_rsTipoEvento = "SELECT * FROM eventos WHERE codEvento = '$EVT'";
$rsTipoEvento = mysql_query($query_rsTipoEvento, $conn) or die(mysql_error());
$row_rsTipoEvento = mysql_fetch_assoc($rsTipoEvento);
$totalRows_rsTipoEvento = mysql_num_rows($rsTipoEvento);

//echo "Acessou MYSQL <br>";
if($totalRows_rsTipoEvento)
{
	$IDEVT = $row_rsTipoEvento['codEvento'];
	if($IDEVT==1 || $IDEVT==3 || $IDEVT==6 || $IDEVT==9)
		$TIPOEVT = 2;
	else
		$TIPOEVT = 1;
}
else
	$error .= 'error: Tipo de evento nao localizado!<br>';



// Pesquisa pelo RFID o ID do equipamento informado

mysql_select_db($database_conn, $conn);
$query_rsEquipamento = "SELECT * FROM equipamento WHERE rfid= '$RFID'";
$rsEquipamento = mysql_query($query_rsEquipamento, $conn) or die(mysql_error());
$row_rsEquipamento = mysql_fetch_assoc($rsEquipamento);
$totalRows_rsEquipamento = mysql_num_rows($rsEquipamento);

if($totalRows_rsEquipamento){
	$IDEQ = $row_rsEquipamento['codEquip'];
}
	else
		$error .= 'error: RFID nao encontrado na tabela EQUIPAMENTO!<br>';

//echo $IDEQ;

/*
// Pesquisa pela TOMADA, sem uso do RFID
$IDEQ = $OUTLET;

mysql_select_db($database_conn, $conn);
$query_rsTomada = "SELECT * FROM tomada WHERE codTomada = $OUTLET";
$rsTomada = mysql_query($query_rsTomada, $conn) or die(mysql_error());
$row_rsTomada = mysql_fetch_assoc($rsTomada);
$totalRows_rsTomada = mysql_num_rows($rsTomada);

if($totalRows_rsTomada)
	$IDTOMADA = $row_rsTomada['codTomada'];
else
	$error .= 'error: tomada nao encontrada na tabela TOMADAS!<br>';

//echo $IDTOMADA;
*/
echo $error;
fwrite($f, print_r("\Erro: " . $error, true));
fclose($f);
if($error == '') {

	//CADASTRA ONDA ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::


	// Mexi aqui para incluir MV2, UNDER, OVER, DURATION
	// 2 - inserir registro na tabela captura
	$captureSQL = sprintf("INSERT INTO capturaatual (codTomada,codTipoOnda,codEquip,codEvento,valorMedio,VM2,offset,gain,eficaz,dataAtual, under,over,duration) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,NOW(),%s,%s,%s)",
			GetSQLValueString($OUTLET,'int'),GetSQLValueString($TIPOEVT,'int'),GetSQLValueString($IDEQ,'int'),GetSQLValueString($IDEVT,'int'),GetSQLValueString(hex2float32($MEAN_VAL),'double'),GetSQLValueString(hex2float32($MEAN_VAL2),'double'),
			GetSQLValueString($OFFSET,'int'),GetSQLValueString(hex2float32($GAIN),'double'),GetSQLValueString(hex2float32($RMS),'double'), GetSQLValueString($UNDER,'int'), GetSQLValueString($OVER,'int'),GetSQLValueString($DURATION,'int'));
	//execute sql
	mysql_select_db($database_conn,$conn) or die(mysql_error());
	$result1 = mysql_query($captureSQL,$conn) or die(mysql_error());
	$lastid = mysql_insert_id();

    //if((intval($TYPE) <= 3) && (intval($TYPE) > 0))
	{
		// 3 - inserir dados na tabela onda
		for($i=0; $i<12; $i++)
		{
			$ondaSQL = sprintf("INSERT INTO harmatual (codCaptura,codHarmonica,sen,cos) VALUES (%s,%s,%s,%s)",
				GetSQLValueString($lastid,'int'),GetSQLValueString($i+1,'int'),GetSQLValueString(hex2float32( $SINE[$i] ),'double'),
				GetSQLValueString(hex2float32( $COSINE[$i] ),'double'));
			//Execute SQL
			mysql_select_db($database_conn,$conn) or die(mysql_error());
			$result1 = mysql_query($ondaSQL,$conn) or die(mysql_error());
		}
	}

	//echo "T=" .round(microtime(true) * 1000);
	echo "Success!!";


}
else
    echo "Bosta " . $error;

mysql_free_result($rsEquipamento);

//echo "Ok";

?>
