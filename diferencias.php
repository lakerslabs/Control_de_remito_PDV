<?php 
session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{
	
$permiso = $_SESSION['permisos'];
$user = $_SESSION['username'];

if(!isset($_GET['fecha_desde'])){
	$fecha_desde = date("Y-m-d");
	$fecha_hasta = date("Y-m-d");
	$observ = '%';
}else{
	$fecha_desde = $_GET['fecha_desde'];
	$fecha_hasta = $_GET['fecha_hasta'];
	
	if($_GET['observ']!=''){
		$observ = '%'.$_GET['observ'];
	}else{
		$observ = '%';
	}
	
	if(!isset($_GET['pendientes'])){
		$pendientes = 'off';
	}else{
		$observ = '';
	}

}


?>
<!DOCTYPE HTML>

<html>
<head>
<title>Control Remitos</title>	
<?php include '../../css/header_simple.php'; ?>

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
			<input type="date" class="form-control" name="fecha_desde" value="<?php if(!isset($_GET['fecha_desde'])){ echo date("Y-m-d"); } else { echo $fecha_desde; };?>">
		</div>
		
		<label class="col-sm-1 col-form-label">Desde</label>
		<div class="col-sm-2">
			<input type="date" class="form-control" name="fecha_hasta" value="<?php if(!isset($_GET['fecha_hasta'])){ echo date("Y-m-d"); } else { echo $fecha_hasta; };?>">
		</div>
		
		<label class="col-sm-1 col-form-label">Observac</label>
		<div class="col-sm-1">
			<input type="text" class="form-control" name="observ">
		</div>
		
		<label class="col-sm-1 col-form-label">Pendientes</label>
		<div class="col-sm-1">
			<input type="checkbox" class="form-control col-2" name="pendientes">
		</div>
		
		<div class="col-sm-1">
			<input type="submit" class="btn btn-primary btn-sm" value="Consultar">
		</div>
		
	</div>

</form>
</div>







<?php


$dsn = '1 - CENTRAL';
$usuario = "sa";
$clave="Axoft1988";




//echo $pendientes.' - '.$observ;


$codClient = $_SESSION['codClient'];


$cid=odbc_connect($dsn, $usuario, $clave);


$sql=
	"
	SET DATEFORMAT YMD

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
	WHERE COD_CLIENT = '$codClient'
	--AND A.FECHA_CONTROL >= GETDATE()-30
	AND (CAST(A.FECHA_CONTROL AS DATE) BETWEEN '$fecha_desde' AND '$fecha_hasta')
	AND A.OBSERVAC_AUDITORIA LIKE '$observ'
	ORDER BY FECHA_CONTROL DESC
	";

ini_set('max_execution_time', 300);
$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

?>
<div >

<form method="get" action="diferencias_procesar.php">

<table class="table table-striped"  id="tabla">

        
		<thead>
			<tr style="font-size:smaller">
				<th style="width: 8%">FECHA<br>CONTROL</th>
				<th style="width: 3%">USER</th>
				<th style="width: 0%"></th>
				<th style="width: 8%">NRO<br>REMITO</th>
				<th style="width: 0%"></th>
				<th style="width: 8%">SUC ORIGEN</th>
				<th style="width: 10%">CODIGO</th>
				<th style="width: 0%"></th>
				<th style="width: 18%">DESCRIPCION</th>
		        <th style="width: 3%">CANT<br>REM</th>
				<th style="width: 3%">CANT<br>CONTRL</th>
				<th style="width: 3%">DIF</th>
				<th style="width: 10%">OBSERVAC LOCAL</th>
				<th style="width: 10%">OBSERVAC AUDITORIA</th>
				<th style="width: 4%; padding:2px"><input type="submit" value="Grabar" class="btn btn-primary btn-sm"></th>

			</tr>
		</thead>
        <?php

		while($v=odbc_fetch_array($result)){
		
		?>
		
        <tr class="fila-base" style="font-size:smaller">

				<td style="width: 8%"><?php echo $v['FECHA_CONTROL'] ;?></td>
				<td style="width: 3%"><?php echo $v['USUARIO_LOCAL'] ;?></td>
				<td style="width: 0%"><input type="text" name="id[]" value="<?php echo $v['ID'] ;?>" hidden></td>
				<td style="width: 8%"><?php echo $v['NRO_REMITO'] ;?></td>
				<td style="width: 0%"><input type="text" name="rem[]" value="<?php echo $v['NRO_REMITO'] ;?>" hidden></td>
				<td style="width: 8%"><?php echo $v['SUC_ORIG'] ;?></td>
				<td style="width: 10%"><?php echo $v['COD_ARTICU'] ;?></td>
		        <td style="width: 0%"><input type="text" name="codArticu[]" value="<?php echo $v['COD_ARTICU'] ;?>" hidden></td>
				<td style="width: 18%"><?php echo $v['DESCRIPCIO'] ;?></td>
				<td style="width: 3%"><?php echo $v['CANT_REM'] ;?></td>
				<td style="width: 3%"><?php echo $v['CANT_CONTROL'] ;?></td>
				<td style="width: 3%"><?php echo $v['DIF'] ;?></td>
				<td style="width: 10%"><input type="text" size="15" name="observ_local[]" value="<?php echo $v['OBSERVAC_LOCAL'] ;?>" class="form-control form-control-sm"></td>
				<td style="width: 10%"><input type="text" size="15" value="<?php echo $v['OBSERVAC_AUDITORIA'] ;?>" class="form-control form-control-sm" readonly></td>
				<td style="width: 4%; padding:2px"></td>
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