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
<script type="text/javascript" language="javascript" src="ajax.js"></script>
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

SELECT ID, CAST(FECHA AS DATE)FECHA, USUARIO, AREA, COD_ARTICU, CANT FROM SOF_INVENTARIO

";

$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));

?>





<table class="table table-striped">

        <tr>

				<td align="left" width="20px">ID</td>
		
				<td align="left" width="20px">FECHA</td>
		
                <td align="left" width="150px">USUARIO</td>
				
				<td align="center">AREA</td>
				
				<td align="center">COD_ARTICU</td>
				
				<td align="center">CANT</td>
				
        </tr>

		
        <?php

       
		while($v=odbc_fetch_array($result)){

        ?>

		
        <tr style="font-size:smaller">

                <div><td><input type="text" value="<?php echo $v['ID'] ;?>"  id="obj_text2" name="obj_text2"></td></div>
				
				<td><input type="text" value="<?php echo $v['FECHA'] ;?>"></td>
				
				<div  id="resultado_<?php echo $v['ID'] ;?>"><td><input type="text" value="<?php echo $v['USUARIO'] ;?>" onChange="enviar_personas(this, <?php echo $v['ID'] ;?>)" id="obj_text" name="obj_text"></td></div>
				
				<td><input type="text" value="<?php echo $v['AREA'] ;?>"></td>
				
				<td><input type="text" value="<?php echo $v['COD_ARTICU'] ;?>"></td>
				
				<td><input type="text" value="<?php echo $v['CANT'] ;?>"></td>

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
