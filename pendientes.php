<?php session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{




$dsnLocal = $_SESSION['dsn'];
$dsn = '1 - CENTRAL';
$user = 'sa';
$pass = 'Axoft1988';
$cod_client = $_SESSION['codClient'];
$cidCentral = odbc_connect($dsn, $user, $pass);

$sqlLimpia = "DELETE FROM SJ_DIFERENCIAS WHERE COD_CLIENT = '$cod_client'";

odbc_exec($cidCentral, $sqlLimpia);


function insertar($a, $b, $c, $d, $e) {
	$dsn = '1 - CENTRAL';
	$user = 'sa';
	$pass = 'Axoft1988';
	
	$cidCentral = odbc_connect($dsn, $user, $pass);
	
	$sqlCentral = 
	"
	INSERT INTO SJ_DIFERENCIAS (FECHA_MOV, COD_CLIENT, N_COMP, SUC_ORIG, ESTADO) VALUES ('$a', '$b', '$c', $d, '$e')
	"
	;
	
	odbc_exec($cidCentral, $sqlCentral) or die(exit("Error en odbc_exec"));
}


$sqlLocal = 
" 
SET DATEFORMAT YMD 
SELECT FECHA_MOV, N_COMP, ESTADO, SUC_ORIG
FROM CTA115 
WHERE FECHA_MOV >= GETDATE()-90 
AND N_COMP LIKE 'R%'
";

$cidLocal = odbc_connect($dsnLocal, $user, $pass);

$result = odbc_exec($cidLocal, $sqlLocal);

while($v=odbc_fetch_array($result)){
	insertar ($v['FECHA_MOV'], $cod_client, $v['N_COMP'], $v['SUC_ORIG'], $v['ESTADO']);
}


}
?>
<script>setTimeout(function () {window.location.href= 'mostrarPendientes.php';},1);</script>