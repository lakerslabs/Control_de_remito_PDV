<?php session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Control de Remitos</title>
<?php include '../../css/header.php'; ?>
</head>
<body>

<button type="button" class="btn btn-primary" onclick="location.href='../index.php'" style="margin:5px">Volver</button>
<button type="button" class="btn btn-primary" onclick="location.href='diferencias.php'" style="margin:5px">Diferencias</button>
<button type="button" class="btn btn-primary" onclick="location.href='pendientes.php'" style="margin:5px">Pendientes / Historico</button>


<div align="center" style="margin-top:10%">
<form action="limpiar.php" id="pedidos" method="get">

<br><br>
<div>
	
	<div style="display: inline-block">
		<label>Remito</label>
		<input type="text" name="rem" placeholder="Numero de Remito" class="form-control form-control-sm col-sm-13"  required autofocus >
	</div>
	
	
	
	<div class="form-group col-sm-3" style="display: inline-block">
      <label for="inputState">Usuario</label>
      <select id="inputState" class="form-control form-control-sm" name="usuario" required>
        <option value="" selected >Usuario</option>
<?php
$dsn = '1 - CENTRAL'; $user = 'sa'; $pass = 'Axoft1988';  $nroSucurs = $_SESSION['numsuc'];
$sql = "
SELECT NOMBRE, APELLIDO, A.BLOQUE, E.DESC_SUCURSAL, E.NRO_SUCURSAL
FROM [TANGO-SUELDOS].LAKERS_CORP_SA.DBO.LEGAJO A
INNER JOIN [TANGO-SUELDOS].LAKERS_CORP_SA.DBO.LEGAJO_SU B
ON A.ID_LEGAJO = B.ID_LEGAJO
INNER JOIN SERVIDOR.LAKER_SA.DBO.GVA23 C
ON A.BLOQUE COLLATE Latin1_General_BIN = C.COD_VENDED
FULL JOIN [192.168.0.226,1433].dbXLSales.DBO.sucursalvendedor D
ON A.BLOQUE COLLATE Latin1_General_BIN = D.CODVENDEDOR
INNER JOIN SERVIDOR.LAKER_SA.DBO.SUCURSAL E
ON D.CODSUCURSAL = E.NRO_SUCURSAL
WHERE B.HABILITADO = 'S' 
AND A.BLOQUE IS NOT NULL
AND A.BLOQUE NOT LIKE ''
AND C.INHABILITA = 0
AND E.NRO_SUCURSAL = $nroSucurs
GROUP BY NOMBRE, APELLIDO, A.BLOQUE, E.DESC_SUCURSAL, E.NRO_SUCURSAL
ORDER BY APELLIDO
";
$cid = odbc_connect($dsn, $user, $pass ); $result = odbc_exec($cid, $sql);
while($v=odbc_fetch_array($result)){
	echo '<option value="'.$v['BLOQUE'].'">'.$v['APELLIDO'].' '.$v['NOMBRE'].'</option>';
}


?>
	       
      </select>
    </div>
	
	

	
	
</div>
<br>
<input type="submit" value="CONSULTAR" class="btn btn-primary btn-sm"></br>
</form>
</div>


<?php
if($_SESSION['conteo'] == 1){
	echo '</br></br><div class="alert alert-danger" role="alert" style="margin-left:15%; margin-right:15%">	El remito no existe en este local!</div>';
	$_SESSION['conteo'] = 0;
}elseif($_SESSION['conteo'] == 2){
	echo '</br></br><div class="alert alert-danger" role="alert" style="margin-left:15%; margin-right:15%">	El remito ya fue controlado</div>';
	$_SESSION['conteo'] = 0;
}


}
?>
</body>
</html>