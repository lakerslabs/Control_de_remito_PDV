<?php 
session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{
	
$permiso = $_SESSION['permisos'];
$user = $_SESSION['codClient'];
$rem = $_SESSION['rem'];

$dsn = '1 - CENTRAL';
$usuario = "sa";
$clave="Axoft1988";

$cid=odbc_connect($dsn, $usuario, $clave);



$sql3=
	"
	SELECT ISNULL(SUM(CANT_CONTROL), 0)VERIFICACION FROM SJ_CONTROL_LOCAL 
	WHERE COD_CLIENT = '$user'
	";

ini_set('max_execution_time', 300);
$result3=odbc_exec($cid,$sql3)or die(exit("Error en odbc_exec"));

while($v=odbc_fetch_array($result3)){
	$verificacion = $v['VERIFICACION'] ;
}

odbc_close($cid);

?>
<!DOCTYPE HTML>

<html>
<head>
<title>Control Remitos</title>	
<?php include '../../css/header_simple.php'; ?>

</head>
<body>	


<div style="margin: 5px">
<button onClick="window.location.href= 'index.php'" class="btn btn-primary">Cancelar</button>
<h3 align="center">Remito: <?php echo $rem; ?></h3>

</div>
<script>
function volver() {window.history.back();};
function procesar() {window.location.href= 'procesar.php?pedido=<?php echo $rem ; ?>';};
</script>

<?php //echo $_SESSION['nro_sucurs'];?>

<form action="" style="margin:20px" align="center" method="POST">
	<label>Leer Codigo</label>
	<input type="text" name="codigo" placeholder="Ingrese codigo" autofocus></input>
	<input type="submit" value="Ingresar" class="btn btn-primary">
</form>

<?php

if(isset ($_POST['codigo']) || $verificacion != 0){

$user_local = $_SESSION['usuario'];

if(isset ($_POST['codigo'])){
    $codigo = str_replace("'", "-", $_POST['codigo']);
    $codigo = str_replace(" ", "", $_POST['codigo']);


$cid=odbc_connect($dsn, $usuario, $clave);


$sqlBuscaSinonimo=
	"
	SET DATEFORMAT YMD
	SELECT COD_ARTICU FROM STA11 WHERE COD_ARTICU = '$codigo' OR SINONIMO = '$codigo'
	";

ini_set('max_execution_time', 300);
$resultBuscaSinonimo=odbc_exec($cid,$sqlBuscaSinonimo)or die(exit("Error en odbc_exec"));


while($v=odbc_fetch_array($resultBuscaSinonimo)){
$codigo = $v['COD_ARTICU'];
}


odbc_close($cid);

//echo $fechaHora.' '.$user.' '.$rem.' '.$codigo.' '.$user_local;
$cid=odbc_connect($dsn, $usuario, $clave);

$sqlInsertar=
	"
	SET DATEFORMAT YMD
	INSERT INTO SJ_CONTROL_LOCAL
	(FECHA_CONTROL, COD_CLIENT, NRO_REMITO, COD_ARTICU, CANT_CONTROL, USUARIO_LOCAL)
	VALUES ('$fechaHora', '$user', '$rem', '$codigo', 1, '$user_local')
	";

ini_set('max_execution_time', 300);
$insert = odbc_exec($cid,$sqlInsertar)or die(exit("Error en odbc_exec"));


odbc_close($cid);


$cid=odbc_connect($dsn, $usuario, $clave);

$sqlValida=
	"
	SET DATEFORMAT YMD
	SELECT COD_ARTICU FROM STA11 WHERE COD_ARTICU LIKE '[XO]%' AND COD_ARTICU = '$codigo'
	";

ini_set('max_execution_time', 300);
$resultValida=odbc_exec($cid,$sqlValida)or die(exit("Error en odbc_exec"));


	//while($v=odbc_fetch_array($resultValida)){
		if(odbc_num_rows($resultValida)==0){
			//$areaEscaneada = 1;
			echo '
			<audio src="Wrong.ogg" autoplay></audio>
			</br></br>
			<div class="alert alert-danger" role="alert" style="margin-left:15%; margin-right:15%">
			ATENCION!! El codigo <strong>'.strtoupper($codigo).'</strong> no existe
			</div>';
			
		}
	//}
	odbc_close($cid);


}

$cid=odbc_connect($dsn, $usuario, $clave);

$sql=
	"
	SET DATEFORMAT YMD

	SELECT A.COD_ARTICU, A.CANT_CONTROL, B.DESCRIPCIO 
	FROM
	(
		SELECT COD_ARTICU, SUM(CANT_CONTROL)CANT_CONTROL FROM SJ_CONTROL_LOCAL 
		WHERE COD_CLIENT = '$user'
		GROUP BY COD_ARTICU
	)A
	INNER JOIN STA11 B
	ON A.COD_ARTICU COLLATE Latin1_General_BIN= B.COD_ARTICU COLLATE Latin1_General_BIN
	
	";

ini_set('max_execution_time', 300);
$result=odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));




?>
<div class="container">

<table class="table table-striped" style="margin-left:50px; margin-right:50px" id="tabla">

        <tr >

				<td class="col-" style="width:20%"><h4>CODIGO</h4></td>
		
				<td class="col-" style="width:50%"><h4>DESCRIPCION</h4></td>
		
                <td class="col-" style="width:2%"><h4>CANTIDAD</h4></td>
				
				<td class="col-"></td>

        </tr>
		
        <?php
		
		$total = 0;
		
		while($v=odbc_fetch_array($result)){
		
		?>
		
        <tr class="fila-base">

				<td class="col-" style="width:20%"><?php echo $v['COD_ARTICU'] ;?></td>
		
                <td class="col-" style="width:50%"><?php echo $v['DESCRIPCIO'] ;?></td>
				
				<td class="col-" style="width:2%" align="center"><?php echo $v['CANT_CONTROL'] ;?></td>
				
				<td class="col-" ><img src="eliminar.png" width="23px" height="23px" align="left" onClick="window.location.href='eliminar_articulo.php?codigo=<?php echo $v['COD_ARTICU'] ;?>'"></img></td>

        </tr>
		
        <?php
		$total+= $v['CANT_CONTROL'];
		
        }
		
		odbc_close($cid);

        ?>
     		

</table>
</br></br>


<script>
function historial(){


<?php
$cid=odbc_connect($dsn, $usuario, $clave);
$sql5=
	"
	SET DATEFORMAT YMD
	SELECT COD_ARTICU FROM SJ_CONTROL_LOCAL 
	WHERE COD_CLIENT = '$user'
	AND COD_ARTICU IN (SELECT COD_ARTICU COLLATE Latin1_General_BIN FROM STA11 WHERE COD_ARTICU LIKE '[XO]%')
	ORDER BY ID DESC
	";

ini_set('max_execution_time', 300);
$cod_ultimo = '';
$result5=odbc_exec($cid,$sql5)or die(exit("Error en odbc_exec"));
?>
alert("Ultimos codigos ingresados :\n<?php while($v=odbc_fetch_array($result5)){ echo $v['COD_ARTICU'].'\n'; if($cod_ultimo==''){$cod_ultimo = $v['COD_ARTICU'] ;} } odbc_close($cid);?>");

};

</script>


<div class="mt-2 text-center fixed fixed-bottom bg-white" style="height: 30px!important; background-color: white; margin-bottom: 5px" >
	<a align="left" style="margin-right:150px"> <strong>Ultimo controlado:</strong>  <?php echo $cod_ultimo;?> <button type="button" class="btn btn-info btn-sm" onClick="historial()">Ver</button></a>
	<a style="margin-right:15px"> <strong>Total de articulos:</strong>  <?php echo $total;?></a>
	<button onClick="window.location.href= 'procesar.php'" class="btn btn-primary btn-sm" >Procesar</button>
</div>

</div>

<?php
}	
}
?>



</body>



</html>
