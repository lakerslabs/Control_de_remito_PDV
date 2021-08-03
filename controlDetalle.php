<?php 
session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{
	
$permiso = $_SESSION['permisos'];
$user = $_SESSION['username'];
$rem = $_SESSION['rem'];
$codClient = $_GET['codClient'];

?>

<!DOCTYPE HTML>

<html>
<head>
<title>Control Remitos</title>	
<?php include '../../css/header_simple.php'; ?>

</head>
<body>	

<a href="javascript:window.print();"><img src="print.png"></a>
<div style="margin: 5px">

<h3 align="center">Remito: <?php echo $rem; ?></h3>

</div>
<script>
function volver() {window.history.back();};
function procesar() {window.location.href= 'procesar.php?pedido=<?php echo $rem ; ?>';};
</script>


<?php






$dsn = '1 - CENTRAL';
$usuario = "sa";
$clave="Axoft1988";

$cid=odbc_connect($dsn, $usuario, $clave);



$sql=
	"
	SET DATEFORMAT YMD
	
	SELECT A.*, B.DESCRIPCIO, A.CANT_CONTROL-A.CANT_REM DIFERENCIA FROM
	(
		SELECT ISNULL(A.COD_CLIENT, B.COD_CLIENT)COD_CLIENT, ISNULL(A.COD_ARTICU, B.COD_ARTICU) COD_ARTICU, ISNULL(A.CANT_REM, 0)CANT_REM, ISNULL(B.CANT_CONTROL, 0)CANT_CONTROL, 
		ISNULL(A.NRO_REMITO, B.NRO_REMITO)NRO_REMITO FROM SJ_CONTROL_LOCAL_AUX_REMITO A
		FULL OUTER JOIN (SELECT COD_CLIENT, NRO_REMITO, COD_ARTICU, SUM(CANT_CONTROL)CANT_CONTROL FROM SJ_CONTROL_LOCAL GROUP BY COD_CLIENT, NRO_REMITO, COD_ARTICU) B
		ON A.COD_CLIENT = B.COD_CLIENT AND A.NRO_REMITO = B.NRO_REMITO AND A.COD_ARTICU = B.COD_ARTICU
	)A
	INNER JOIN STA11 B
	ON A.COD_ARTICU COLLATE Latin1_General_BIN = B.COD_ARTICU COLLATE Latin1_General_BIN
	WHERE A.COD_CLIENT = '$codClient' AND A.NRO_REMITO = '$rem'
	";

ini_set('max_execution_time', 300);

$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

?>
<div class="container">

<table class="table table-striped table-fh table-5c" id="id_tabla" style="width:80%;" align="center">
			
		<thead>
			<tr style="font-size:smaller">

				<td style="width: 10%"><h6>CODIGO</h6></td>
		
				<td style="width: 20%"><h6>DESCRIPCION</h6></td>
		
                <td style="width: 10%"><h6>CANT<br>REMITO</h6></td>
				
				<td style="width: 10%"><h6>CANT<br>CONTROL</h6></td>
				
				<td style="width: 10%"><h6>DIF</h6></td>

        </tr>
		</thead>
		<tbody>
        <?php
		$total1 = 0;
		$total2 = 0;
		$total3 = 0;
		while($v=odbc_fetch_array($result)){
		
		?>
		
			<?php 
			if($v['DIFERENCIA']<>0){
				?>
				<tr style="font-size:smaller;font-weight:bold;color:#FE2E2E" >
				<?php
			}else{
				?>
				<tr style="font-size:smaller" >
				<?php
			}
			?>
				<td style="width: 10%" ><?php echo $v['COD_ARTICU'] ;?></td>
		
                <td style="width: 20%" ><?php echo $v['DESCRIPCIO'] ;?></td>
				
				<td style="width: 10%; padding-left: 4%" ><?php echo $v['CANT_REM'] ;?></td>
				
				<td style="width: 10%; padding-left: 4%" ><?php echo $v['CANT_CONTROL'] ;?></td>
				
				<td style="width: 10%; padding-left: 4%" ><?php echo $v['DIFERENCIA'] ;?></td>

        </tr>
		
        <?php
		$total1+= $v['CANT_REM'];
		$total2+= $v['CANT_CONTROL'];
		$total3+= $v['DIFERENCIA'];
        }

        ?>
		<tr style="font-weight: bold;">
		
				<td style="width: 10%" ></td>
		
                <td style="width: 20%" >TOTALES</td>
				
				<td style="width: 10%; padding-left: 4%" ><?php echo $total1 ;?></td>
				
				<td style="width: 10%; padding-left: 4%" ><?php echo $total2 ;?></td>
				
				<td style="width: 10%; padding-left: 4%" ><?php echo $total3 ;?></td>

        </tr>
		
		</tbody>
     		
</table>
</br></br>



</div>
        <?php
}	

?>

<button onClick="window.location.href= 'index.php'" class="btn btn-primary" style="margin-left:80%">Ir a Inicio</button>
<br><br>
</body>
</html>