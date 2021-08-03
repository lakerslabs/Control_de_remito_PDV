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
<?php include '../../css/header.php'; ?>

</head>
<body>	

<?php


$dsn = '1 - CENTRAL';
$usuario = "sa";
$clave="Axoft1988";

$codClient = $_SESSION['codClient'];

$cid=odbc_connect($dsn, $usuario, $clave);


$sql=
	"
	SET DATEFORMAT YMD

	SELECT A.ID, CAST(FECHA_CONTROL AS DATE)FECHA_CONTROL, COD_CLIENT, USUARIO_LOCAL, CAST(FECHA_REM AS DATE)FECHA_REM, NRO_REMITO, C.DESC_SUCURSAL SUC_ORIG, D.DESC_SUCURSAL SUC_DESTIN, 
	A.COD_ARTICU, B.DESCRIPCIO, CANT_REM, CANT_CONTROL, ISNULL(A.OBSERVAC_LOCAL, '')OBSERVAC_LOCAL, ISNULL(A.OBSERVAC_AUDITORIA, '')OBSERVAC_AUDITORIA,
	ISNULL(A.OBSERVAC_LOGISTICA, '')OBSERVAC_LOGISTICA, CANT_CONTROL-CANT_REM DIF
	FROM SJ_CONTROL_AUDITORIA A
	INNER JOIN STA11 B
	ON A.COD_ARTICU COLLATE Latin1_General_BIN = B.COD_ARTICU COLLATE Latin1_General_BIN
	INNER JOIN SUCURSAL C
	ON A.SUC_ORIG = C.NRO_SUCURSAL
	INNER JOIN SUCURSAL D
	ON A.SUC_DESTIN = D.NRO_SUCURSAL
	ORDER BY OBSERVAC_LOGISTICA, FECHA_CONTROL
	";

ini_set('max_execution_time', 300);
$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

?>
<div style="width:100%; height:80%" >
<form method="get" action="logistica_procesar.php">

	

		<table class="table table-striped table-fh table-15c" id="id_tabla">
		
			<thead>
				<tr style="font-size:smaller">
					<th style="width: 8%">FECHA<br>CONTROL</th>
					
					<th style="width: 10%">NRO REMITO</th>
					
					<th style="width: 8%">SUC ORIGEN</th>
					<th style="width: 8%">SUC DESTINO</th>
					
					<th style="width: 10%">CODIGO</th>
					
					<th style="width: 13%">DESCRIPCION</th>
					<th style="width: 5%">CANT<br>REM</th>
					<th style="width: 5%">CANT<br>CONTRL</th>
					<th style="width: 5%">DIF</th>
					<th style="width: 10%">OBSERVAC<br>LOCAL</th>
					<th style="width: 10%">OBSERVAC<br>AUDITORIA</th>
					<th style="width: 5%"><input type="submit" value="Grabar" class="btn btn-primary btn-sm"></th>
					
				</tr>
			</thead>
			
			<tbody>

				<?php
				while($v=odbc_fetch_array($result)){
				?>

				<tr style="font-size:smaller">
					<td style="width: 8%"><?php echo $v['FECHA_CONTROL'] ;?></td>
					<td style="width: 0%"><input type="text" name="id[]" value="<?php echo $v['ID'] ;?>" hidden></td>
					<td style="width: 10%"><?php echo $v['NRO_REMITO'] ;?></td>
					<td style="width: 0%"><input type="text" name="rem[]" value="<?php echo $v['NRO_REMITO'] ;?>" hidden></td>
					<td style="width: 8%"><small><?php echo $v['SUC_ORIG'] ;?></small></td>
					<td style="width: 8%"><small><?php echo $v['SUC_DESTIN'] ;?></small></td>
					
					<td style="width: 10%"><?php echo $v['COD_ARTICU'] ;?></td>
					<td style="width: 0%"><input type="text" name="codArticu[]" value="<?php echo $v['COD_ARTICU'] ;?>" hidden></td>
					<td style="width: 13%"><?php echo $v['DESCRIPCIO'] ;?></td>
					<td style="width: 5%"><?php echo $v['CANT_REM'] ;?></td>
					<td style="width: 5%"><?php echo $v['CANT_CONTROL'] ;?></td>
					<td style="width: 5%"><?php echo $v['DIF'] ;?></td>
					<td style="width: 10%"><input type="text" value="<?php echo $v['OBSERVAC_LOCAL'] ;?>" class="form-control form-control-sm col-md-12" readonly></td>
					<td style="width: 10%"><input type="text" value="<?php echo $v['OBSERVAC_AUDITORIA'] ;?>" class="form-control form-control-sm col-md-12" readonly></td>

					<?php if($v['OBSERVAC_LOGISTICA']=='SI'){
					?>	
					<td style="width: 10%"><input type="text" name="observ_logistica[]" value="<?php echo $v['OBSERVAC_LOGISTICA'] ;?>" class="form-control form-control-sm col-md-12" readonly></td>
					<?php
					}else{
					?>
					<td style="width: 10%"><select name="observ_logistica[]" class="form-control form-control-sm col-md-13" ><option value="NO">No</option><option value="SI">Si</option></select > </td>
					<?php
					}
					?>

				</tr>

				<?php
				}
				?>
				
			</tbody>
		</table>
		
	
	
</form>
</div>


</body>
<script>
function volver() {window.history.back();};
function procesar() {window.location.href= 'procesar.php?pedido=<?php echo $rem ; ?>';};
</script>
</html>


<?php
}
?>


