<?php 
session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:login.php");
}else{

function strright($rightstring, $length) {
  return(substr($rightstring, -$length));
}

if(isset ($_GET['desde'])){
	$desde = $_GET['desde'];
	$hasta = $_GET['hasta'];	
}else{
	$desde = date('Y-m').'-'.strright(('0'.((date('d'))-15)),2);
	$hasta = date('Y-m').'-'.strright(('0'.((date('d')))),2);
}	

if(isset($_GET['orden'])){
	$orden = $_GET['orden'];
}else{
	$orden = '';
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Pendientes E-Commerce</title>
<link rel="shortcut icon" href="icono.jpg" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</head>
<body>



<form action="" id="pedidos" style="margin:20px">
PEDIDO:
<input type="text" name="orden" placeholder="Numero pedido Tango" id="caja">
DESDE:
<input type="date" name="desde" value="<?php echo $desde ?>">
HASTA
<input type="date" name="hasta" value="<?php echo $hasta ?>">

<input type="submit" value="CONSULTAR" class="btn btn-primary btn-sm"></br>
</form>

</br></br>
<div class="container-fluid">
<?php

$dsn = "1 - CENTRAL";
$user = "Axoft";
$pass = "Axoft";

$cid = odbc_connect($dsn, $user, $pass);

if(!$cid){echo "</br>Imposible conectarse a la base de datos!</br>";}

$sql="
SET DATEFORMAT YMD

SELECT ORIGEN, NRO_ORDEN_ECOMMERCE, NRO_PEDIDO, SELEC, FECHA_PEDIDO, RAZON_SOCIAL, COD_ARTICULO, DESCRIPCION, CANTIDAD_A_FACTURAR, CAST(TOTAL_PEDIDO AS DECIMAL(10,2)) TOTAL_PEDIDO, NOMBRE_MEDIO_PAGO 
FROM SOF_AUDITORIA 
WHERE AUDITORIA = 0 AND ( FECHA_PEDIDO BETWEEN '$desde' AND '$hasta') AND NRO_PEDIDO LIKE '%$orden' AND (NRO_COMP = '' OR NRO_COMP IS NULL )
ORDER BY 1 desc, 3 desc

";

$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

?>





<table class="table table-striped">

        <tr>

				<td align="left" width="20px">ORIGEN</td>
		
                <td align="left" width="150px">NRO ORDEN</td>
				
				<td align="center">NRO PEDIDO</td>
				
				<td align="center">FECHA PEDIDO</td>
				
				<td align="center">RAZON SOCIAL</td>
				
				<td align="center"  width="140px">CODIGO</td>
				
				<td align="center">DESCRIPCION</td>
				
				<td align="center">CANT</td>
				
				<td align="center">TOTAL PEDIDO</td>
				
				<td align="center">MEDIO DE PAGO</td>

        </tr>

		
        <?php

       
		while($v=odbc_fetch_array($result)){

        ?>

		
        <tr style="font-size:smaller">

                <td><?php echo $v['ORIGEN'] ;?></td>
				
				<td><?php echo $v['NRO_ORDEN_ECOMMERCE'] ;?></td>
				
				<td><?php echo $v['NRO_PEDIDO'] ;?></td>
				
				<td><?php echo $v['FECHA_PEDIDO'] ;?></td>
				
				<td><?php echo $v['RAZON_SOCIAL'] ;?></td>
				
				<td><?php echo $v['COD_ARTICULO'] ;?></td>
				
				<td><?php echo $v['DESCRIPCION'] ;?></td>
				
				<td><?php echo $v['CANTIDAD_A_FACTURAR'] ;?></td>
				
				<td><?php echo $v['TOTAL_PEDIDO'] ;?></td>
				
				<td><?php echo $v['NOMBRE_MEDIO_PAGO'] ;?></td>

		</tr>

		
        <?php

        }

        ?>

		
        		
</table>


<?php
//}
?>

</div>
<script>
window.onload = function() {
  var input = document.getElementById("caja").focus();
}
</script>
</body>
</html>

<?php
}
?>
