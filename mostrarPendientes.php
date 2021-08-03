<?php 
session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{
	
$permiso = $_SESSION['permisos'];
$user = $_SESSION['username'];

?>
<!DOCTYPE HTML>

<html>
<head>
<title>Control Remitos</title>	
<?php include '../../css/header_simple.php'; 

if(!isset($_GET['fecha_remito'])){
	$fecha_remito = date("Y-m-d");
	$fecha_control = date("Y-m-d");
}else{
	$fecha_remito = $_GET['fecha_remito'];
	$fecha_control = $_GET['fecha_control'];
}

?>

</head>
<body>	


<div style="margin: 5px">



</div>
<script>
function volver() {window.history.back();};
function procesar() {window.location.href= 'procesar.php?pedido=<?php echo $rem ; ?>';};
</script>

<div >
<form action="" method="GET" >

	<div class="form-group row">
		
		<div class="col-sm-1">
			<button type="button" class="btn btn-primary btn-sm" onClick="window.location.href= 'index.php'">Inicio</button>
		</div>
		
		<label class="col-sm-1 col-form-label">Desde</label>
		<div class="col-sm-2">
			<input type="date" class="form-control" name="fecha_remito" value="<?php echo $fecha_remito;?>">
		</div>
		
		<label class="col-sm-1 col-form-label">Hasta</label>
		<div class="col-sm-2">
			<input type="date" class="form-control" name="fecha_control" value="<?php echo $fecha_control;?>">
		</div>
				
		<div class="col-sm-2">
			<input type="submit" class="btn btn-primary btn-sm" value="Consultar">
		</div>
		
	</div>

</form>
</div>

<?php


$dsn = '1 - CENTRAL';
$usuario = "sa";
$clave="Axoft1988";

$codClient = $_SESSION['codClient'];

$cid=odbc_connect($dsn, $usuario, $clave);





$sql=
	"
	SET DATEFORMAT YMD
	
	SELECT A.*, 
	CASE 
	WHEN A.NOMBRE_VEN IS NULL AND B.CANT_DIF IS NULL THEN '' 
	WHEN A.NOMBRE_VEN IS NOT NULL AND B.CANT_DIF IS NULL THEN 0 
	ELSE B.CANT_DIF 
	END CANT_DIF 
	FROM
	(
	SELECT A.COD_CLIENT, FECHA_MOV, N_COMP, A.SUC_ORIG, C.DESC_SUCURSAL, 
	CASE ESTADO
	WHEN 'P' THEN 'Pendiente'
	WHEN 'I' THEN 'Registrado'
	WHEN 'X' THEN 'Rechazado'
	ELSE 'Consultar'
	END ESTADO
	, FECHA_CONTROL, SUC_DESTIN, USUARIO_LOCAL, D.NOMBRE_VEN FROM SJ_DIFERENCIAS A
	LEFT JOIN (SELECT FECHA_CONTROL, COD_CLIENT, NRO_REMITO, SUC_ORIG, SUC_DESTIN, USUARIO_LOCAL FROM SJ_CONTROL_AUDITORIA 
	GROUP BY FECHA_CONTROL, COD_CLIENT, NRO_REMITO, SUC_ORIG, SUC_DESTIN, USUARIO_LOCAL) B
	ON A.COD_CLIENT = B.COD_CLIENT COLLATE Latin1_General_BIN AND A.N_COMP = B.NRO_REMITO COLLATE Latin1_General_BIN
	INNER JOIN SUCURSAL C
	ON A.SUC_ORIG = C.NRO_SUCURSAL
	LEFT JOIN GVA23 D
	ON B.USUARIO_LOCAL COLLATE Latin1_General_BIN = D.COD_VENDED
	
	WHERE A.COD_CLIENT = '$user'	
	AND A.FECHA_MOV BETWEEN '$fecha_remito' AND '$fecha_control'
	)A
	LEFT JOIN 
	(
	SELECT COD_CLIENT, NRO_REMITO, SUM(DIF) CANT_DIF
	FROM
	(
	SELECT A.ID, CAST(FECHA_CONTROL AS DATE)FECHA_CONTROL, COD_CLIENT, USUARIO_LOCAL, CAST(FECHA_REM AS DATE)FECHA_REM, NRO_REMITO, C.DESC_SUCURSAL SUC_ORIG, D.DESC_SUCURSAL SUC_DESTIN, 
	A.COD_ARTICU, B.DESCRIPCIO, CANT_REM, CANT_CONTROL, ISNULL(A.OBSERVAC_LOCAL, '')OBSERVAC_LOCAL, ISNULL(A.OBSERVAC_AUDITORIA, '')OBSERVAC_AUDITORIA,
	(CANT_CONTROL - CANT_REM) DIF
	FROM SJ_CONTROL_AUDITORIA A
	INNER JOIN STA11 B
	ON A.COD_ARTICU COLLATE Latin1_General_BIN = B.COD_ARTICU COLLATE Latin1_General_BIN
	INNER JOIN SUCURSAL C
	ON A.SUC_ORIG = C.NRO_SUCURSAL
	INNER JOIN SUCURSAL D
	ON A.SUC_DESTIN = D.NRO_SUCURSAL
	WHERE COD_CLIENT = '$user'
	--AND A.FECHA_CONTROL >= GETDATE()-30
	AND (CAST(A.FECHA_CONTROL AS DATE) BETWEEN '$fecha_remito' AND '$fecha_control')
	)A
	GROUP BY COD_CLIENT, NRO_REMITO
	)B
	ON A.COD_CLIENT COLLATE Latin1_General_BIN = B.COD_CLIENT COLLATE Latin1_General_BIN  
	AND A.N_COMP COLLATE Latin1_General_BIN  = B.NRO_REMITO COLLATE Latin1_General_BIN 
	ORDER BY 4, 2, 3
	";

ini_set('max_execution_time', 300);
$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

?>
<div >

<form method="get" action="diferencias_procesar.php">

<table class="table table-striped"  id="tabla">

        
		<thead>
			<tr style="font-size:smaller">
				<th style="width: 8%">FECHA<br>REMITO</th>
				<th style="width: 8%">NRO<br>REMITO</th>
				<th style="width: 3%">ESTADO</th>
				<th style="width: 8%">SUC ORIGEN</th>
				<th style="width: 8%">FECHA<br>CONTROL</th>
				<th style="width: 10%">USUARIO</th>
				<th style="width: 10%">CANT DIF</th>
			</tr>
		</thead>
        <?php

		while($v=odbc_fetch_array($result)){
		
		?>
		
        <tr class="fila-base" style="font-size:smaller">

				<td style="width: 8%"><?php echo $v['FECHA_MOV'] ;?></td>
				<td style="width: 3%"><?php echo $v['N_COMP'] ;?></td>
				<td style="width: 8%"><?php echo $v['ESTADO'] ;?></td>
				<td style="width: 8%"><?php echo $v['DESC_SUCURSAL'] ;?></td>
				<td style="width: 8%"><?php echo $v['FECHA_CONTROL'] ;?></td>
				<td style="width: 10%"><?php echo $v['NOMBRE_VEN'] ;?></td>
				<td style="width: 10%">
					<?php if($v['NOMBRE_VEN'] != '') echo $v['CANT_DIF'] ;?>
				</td>
				
        </tr>
		
        <?php

        }

        ?>
     		
</table>


</form>

</div>
        <?php

}


?>


</body>
</html>