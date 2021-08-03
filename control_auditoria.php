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


	SELECT ID, FECHA_CONTROL, COD_CLIENT, USUARIO_LOCAL, FECHA_REM, NRO_REMITO, SUC_ORIG, SUC_DESTIN, COD_ARTICU, DESCRIPCIO, CANT_REM, CANT_CONTROL, OBSERVAC_LOCAL, OBSERVAC_AUDITORIA, OBSERVAC_LOGISTICA, DIF
	FROM
	(
	SELECT A.ID, CAST(FECHA_CONTROL AS DATE)FECHA_CONTROL, COD_CLIENT, USUARIO_LOCAL, CAST(FECHA_REM AS DATE)FECHA_REM, NRO_REMITO, C.DESC_SUCURSAL SUC_ORIG, D.DESC_SUCURSAL SUC_DESTIN, 
	A.COD_ARTICU, CASE WHEN A.COD_ARTICU = 'SIN DIFERENCIAS' THEN 'REMITO SIN DIFERENCIAS' ELSE B.DESCRIPCIO END DESCRIPCIO, CANT_REM, CANT_CONTROL, ISNULL(A.OBSERVAC_LOCAL, '')OBSERVAC_LOCAL, ISNULL(A.OBSERVAC_AUDITORIA, '')OBSERVAC_AUDITORIA,
	ISNULL(A.OBSERVAC_LOGISTICA, '')OBSERVAC_LOGISTICA, CANT_REM-CANT_CONTROL DIF
	FROM SJ_CONTROL_AUDITORIA A
	LEFT JOIN STA11 B
	ON A.COD_ARTICU COLLATE Latin1_General_BIN = B.COD_ARTICU COLLATE Latin1_General_BIN
	INNER JOIN SUCURSAL C
	ON A.SUC_ORIG = C.NRO_SUCURSAL
	INNER JOIN SUCURSAL D
	ON A.SUC_DESTIN = D.NRO_SUCURSAL	
	)A
	WHERE DESCRIPCIO IS NOT NULL OR COD_ARTICU = 'SIN DIFERENCIAS'
	ORDER BY FECHA_CONTROL DESC
	";

ini_set('max_execution_time', 300);
$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

?>

<form method="post" action="auditoria_procesar.php">

	

		<table class="table table-striped table-fh table-15c" id="id_tabla">
		
			<thead>
				<tr style="font-size:smaller">
					<th style="width: 7%">FECHA<br>CONTROL</th>
					<th style="width: 7%">ID</th>
					<th style="width: 7%">NRO<br>REMITO</th>

					<th style="width: 7%">SUC<br>ORIGEN</th>
					<th style="width: 7%">SUC<br>DESTINO</th>
					<th style="width: 7%">USER</th>
					<th style="width: 7%">CODIGO</th>

					<th style="width: 7%">DESCRIPCION</th>
					<th style="width: 7%">REM</th>
					<th style="width: 7%">CONTRL</th>
					<th style="width: 7%">DIF</th>
					<th style="width: 7%">OBSERVAC<br>LOCAL</th>
					<th style="width: 7%">OBSERVAC<br>AUDITORIA</th>
					<th style="width: 7%"><input type="text" class="form-control form-control-sm" onkeyup="myFunction()" id="textBox" name="factura" placeholder="Sobre cualquier campo.." autofocus></th>
					<th style="width: 7%"><input type="submit" value="Grabar" class="btn btn-primary btn-sm"></th>
					
				</tr>
			</thead>
			
			<tbody id="table">

				<?php
				while($v=odbc_fetch_array($result)){
				?>

				<tr style="font-size:smaller">
					<td style="width: 7%"><?php echo $v['FECHA_CONTROL'] ;?></td>
					<td style="width: 7%"><input class="form-control-plaintext" type="text" name="id[]" value="<?php echo $v['ID'] ;?>" ></td>
					
					<td style="width: 7%; color:
						<?php 
						if($v['OBSERVAC_LOGISTICA']=='SI'){
							echo 'green';
						}else{
							echo 'red';
						}
						?>
					; font-weight: bold"><input type="text" class="form-control-plaintext" name="rem[]" value="<?php echo $v['NRO_REMITO'] ;?>" ></td>
					
					
					<td style="width: 7%"><small><?php echo $v['SUC_ORIG'] ;?></small></td>
					<td style="width: 7%"><small><?php echo $v['SUC_DESTIN'] ;?></small></td>
					<td style="width: 7%"><?php echo $v['USUARIO_LOCAL'] ;?></td>
					
					<td style="width: 7%"><input class="form-control-plaintext" type="text" name="codArticu[]" value="<?php echo $v['COD_ARTICU'] ;?>" ></td>
					<td style="width: 7%"><small><?php echo $v['DESCRIPCIO'] ;?></small></td>
					<td style="width: 7%"><?php echo $v['CANT_REM'] ;?></td>
					<td style="width: 7%"><?php echo $v['CANT_CONTROL'] ;?></td>
					<td style="width: 7%"><?php echo $v['DIF'] ;?></td>
					<td style="width: 7%"><input type="text" value="<?php echo $v['OBSERVAC_LOCAL'] ;?>" class="form-control form-control-sm col-md-12" readonly></td>
					<td style="width: 7%"><input type="text" name="observ_auditoria[]" value="<?php echo $v['OBSERVAC_AUDITORIA'] ;?>" class="form-control form-control-sm col-md-12" ></td>
					<td style="width: 7%"><input type="date" value="<?php echo date("Y-m-d");?>" class="form-control form-control-sm col-md-11" ></td>
				</tr>

				<?php
				}
				?>
				
			</tbody>
		</table>
		
	
	
</form>

<button onClick="window.location.href= '../conteos/index.php'" class="btn btn-primary btn-sm" style="margin-left:80%">Volver</button>

<script type="text/javascript" src="main.js"></script>


</body>
<script>
function volver() {window.history.back();};
function procesar() {window.location.href= 'procesar.php?pedido=<?php echo $rem ; ?>';};
</script>
</html>


<?php
}
?>


