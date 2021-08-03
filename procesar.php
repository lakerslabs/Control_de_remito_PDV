<?php
session_start();
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{

include '../../css/header.php'; 

$codClient = $_SESSION['codClient'];
$usuarioLocal = $_SESSION['usuario'];

$dsn = "1 - CENTRAL";
$dsnLocal = $_SESSION['dsn'];

$user = "sa";
$pass = "Axoft1988";
$rem = $_SESSION['rem'];
$nroSucurs = $_SESSION['nro_sucurs'];

$cidLocal = odbc_connect($dsnLocal, $user, $pass, SQL_CURSOR_FORWARD_ONLY);


//ASIGNA VALOR DE SUC_ORIG, SUC_DESTIN Y FECHA_REM DEL REMITO EN EL LOCAL DESTINO
$sql="
SELECT FECHA_MOV, SUC_ORIG, SUC_DESTIN FROM CTA115 WHERE N_COMP = '$rem' AND NRO_SUCURS = $nroSucurs
";
$result=odbc_exec($cidLocal,$sql)or die(exit("Error en odbc_exec"));

while($v=odbc_fetch_object($result)){
	$sucOrig = $v->SUC_ORIG;
	$sucDestin = $v->SUC_DESTIN;
	$fechaRem = $v->FECHA_MOV;
}


//MARCAR REMITO COMO REGISTRADO
$sqlActua="
UPDATE CTA115 SET TALONARIO = 1
WHERE N_COMP = '$rem'
";
odbc_exec($cidLocal,$sqlActua)or die(exit("Error en odbc_exec"));




//ACTUALIZAR SUC_ORIG Y SUC_DESTIN EN EL CONTEO
$cid = odbc_connect($dsn, $user, $pass, SQL_CURSOR_FORWARD_ONLY);

$sql="
UPDATE SJ_CONTROL_LOCAL SET SUC_ORIG = $sucOrig, SUC_DESTIN = $sucDestin
WHERE NRO_REMITO = '$rem'
";
odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));



//LIMPIAR TABLA
$sqlBorrar="
DELETE FROM SJ_CONTROL_LOCAL_AUX_REMITO WHERE NRO_REMITO = '$rem'
";
odbc_exec($cid,$sqlBorrar)or die(exit("Error en odbc_exec"));


//DECLARAR FUNCION INSERTAR DATOS DEL REMITO DESDE LOCAL A LA AUDITORIA
function insertarRegistro($cArt, $k, $fechaHora, $codClient,  $fechaRem, $sucOrig, $sucDestin){
	
	$dsn = "1 - CENTRAL";
	$user = "sa";
	$pass = "Axoft1988";
	$rem = $_SESSION['rem'];
	$cid = odbc_connect($dsn, $user, $pass, SQL_CURSOR_FORWARD_ONLY);
	
	$sql4="
	SET DATEFORMAT YMD
	INSERT INTO SJ_CONTROL_LOCAL_AUX_REMITO ( FECHA_CONTROL, COD_CLIENT, FECHA_REM, NRO_REMITO, SUC_ORIG, SUC_DESTIN, COD_ARTICU, CANT_REM )
	VALUES ('$fechaHora', '$codClient', '$fechaRem', '$rem', $sucOrig, $sucDestin, '$cArt', $k)
	";
	odbc_exec($cid,$sql4)or die(exit("Error en odbc_exec"));
}


//INSERTAR DATOS DEL REMITO EN EL LOCAL CON FUNCION	

$sql="
SELECT B.COD_ARTICU, B.CANTIDAD FROM CTA115 A
INNER JOIN CTA96 B
ON A.NCOMP_IN_S = B.NCOMP_IN_S AND A.TCOMP_IN_S = B.TCOMP_IN_S AND A.NRO_SUCURS = B.NRO_SUCURS
WHERE N_COMP = '$rem'
";
$result=odbc_exec($cidLocal,$sql)or die(exit("Error en odbc_exec"));

while($v=odbc_fetch_array($result)){
	
	insertarRegistro($v['COD_ARTICU'], $v['CANTIDAD'], $fechaHora, $codClient, $fechaRem, $sucOrig, $sucDestin);
	
}




$sqlAuditoria =
"
SET DATEFORMAT YMD
SELECT COD_CLIENT, COD_ARTICU, CANT_REM, CANT_CONTROL, USUARIO_LOCAL
FROM
(
	SELECT COD_CLIENT, ISNULL(COD1,COD2) COD_ARTICU, 
	CASE WHEN CANT_REM IS NULL THEN 0 ELSE CANT_REM END CANT_REM, 
	CASE WHEN CANT_CONTROL IS NULL THEN 0 ELSE CANT_CONTROL END CANT_CONTROL, ISNULL(USUARIO_LOCAL, '$usuarioLocal')USUARIO_LOCAL
	FROM
	(
		SELECT A.COD_CLIENT, A.COD_ARTICU COD1, B.COD_ARTICU COD2, A.CANT_REM, B.CANT_CONTROL, B.USUARIO_LOCAL FROM SJ_CONTROL_LOCAL_AUX_REMITO A
		FULL JOIN 
		(
			SELECT COD_CLIENT, NRO_REMITO, COD_ARTICU, SUM(CANT_CONTROL)CANT_CONTROL, USUARIO_LOCAL FROM SJ_CONTROL_LOCAL 
			GROUP BY COD_CLIENT, NRO_REMITO, COD_ARTICU, USUARIO_LOCAL
		) B
		ON A.COD_CLIENT = B.COD_CLIENT AND A.NRO_REMITO = B.NRO_REMITO AND A.COD_ARTICU = B.COD_ARTICU
		WHERE ISNULL(A.COD_CLIENT, B.COD_CLIENT) = '$codClient' AND ISNULL(A.NRO_REMITO, B.NRO_REMITO) = '$rem'
	)A
	WHERE ISNULL(COD1,COD2) != '' 
)A
WHERE CANT_REM <> CANT_CONTROL
AND COD_ARTICU NOT IN (SELECT COD_ARTICU COLLATE Latin1_General_BIN FROM STA03)
";

if(odbc_num_rows ( odbc_exec($cid,$sqlAuditoria) ) == 0 ){
	$sqlInsertarAuditoria = "INSERT INTO SJ_CONTROL_AUDITORIA 
	([FECHA_CONTROL], [COD_CLIENT], [FECHA_REM], [NRO_REMITO], [SUC_ORIG], [SUC_DESTIN], [COD_ARTICU], [CANT_REM], [CANT_CONTROL], [USUARIO_LOCAL]) 
	VALUES ('$fechaHora', '$codClient', '$fechaRem', '$rem', $sucOrig, $sucDestin, 'SIN DIFERENCIAS', $cantRem, $cantControl, '$usuarioLocal')";
	
	odbc_exec($cid,$sqlInsertarAuditoria)or die("<p>".odbc_errormsg());
}else{
	$resultAuditoria=odbc_exec($cid,$sqlAuditoria)or die(exit("Error en odbc_exec"));

	while($v=odbc_fetch_object($resultAuditoria)){
		
		$codArticu = $v-> COD_ARTICU;
		$cantRem = $v -> CANT_REM;
		$cantControl = $v -> CANT_CONTROL;
		$usuarioLocal = $v -> USUARIO_LOCAL;
		
		$sqlInsertarAuditoria = "INSERT INTO SJ_CONTROL_AUDITORIA 
		([FECHA_CONTROL], [COD_CLIENT], [FECHA_REM], [NRO_REMITO], [SUC_ORIG], [SUC_DESTIN], [COD_ARTICU], [CANT_REM], [CANT_CONTROL], [USUARIO_LOCAL]) 
		VALUES ('$fechaHora', '$codClient', '$fechaRem', '$rem', $sucOrig, $sucDestin, '$codArticu', $cantRem, $cantControl, '$usuarioLocal')";
		
		odbc_exec($cid,$sqlInsertarAuditoria)or die("<p>".odbc_errormsg());
	}
}




//echo $rem.' '.$user;

}

echo '<h3>Remito '.$rem.' procesado</h3>';
?>
<title>Procesando..</title>
<link rel="shortcut icon" href="icono.jpg" />
<!--<script>setTimeout(function () {window.location.href= 'controlDetalle.php?rem=<?php echo $rem;?>&codClient=<?php echo $codClient;?>';},1000);</script>-->