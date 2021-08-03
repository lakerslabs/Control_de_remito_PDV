<?php
session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{
	
include '../../css/header.php'; 
$dsn = '1 - CENTRAL';
$usuario = "sa";
$clave= "Axoft1988";
$user = $_SESSION['codClient'];
$_SESSION['rem'] = $_GET['rem'];
$_SESSION['usuario'] = $_GET['usuario'];
$rem = $_SESSION['rem'];
$dsn_local = $_SESSION['dsn'];




//BUSCA EL REMITO EN EL LOCAL
$sqlBuscarRemito = "SELECT * FROM CTA115 WHERE N_COMP = '$rem'";
$cid_2 = odbc_connect($dsn_local, $usuario, $clave);
$result2 = odbc_exec($cid_2, $sqlBuscarRemito);
if( odbc_num_rows( $result2 ) ) { 

	//BUSCA QUE NO ESTE CONTROLADO EL REMITO
	$sqlRemitoPasado = "SELECT * FROM CTA115 WHERE N_COMP = '$rem' AND TALONARIO = 1";
	
	$result3 = odbc_exec($cid_2, $sqlRemitoPasado);
	//SI YA FUE CONTROLADO, TE ENVIA AL INDEX CON CARTEL DE AVISO
	if( odbc_num_rows( $result3 ) ) { 
		$_SESSION['conteo'] = 2;
		header("Location:index.php");

	}


	echo $_SESSION['rem'];

    while($v=odbc_fetch_array($result2)){
        $_SESSION['nro_sucurs'] = $v['NRO_SUCURS'];
    }

	$sqlLimpiar = "DELETE FROM SJ_CONTROL_LOCAL WHERE COD_CLIENT = '$user';";
	$cid=odbc_connect($dsn, $usuario, $clave);
	odbc_exec($cid, $sqlLimpiar);


	$sqlLimpiar2 = "DELETE FROM SJ_CONTROL_LOCAL_AUX_REMITO WHERE COD_CLIENT = '$user';";
	$cid=odbc_connect($dsn, $usuario, $clave);
	odbc_exec($cid, $sqlLimpiar2);

	
	?>
	<script>setTimeout(function () {window.location.href= 'controlRemitos.php';},1);</script>
	<?php
}else{
	$_SESSION['conteo'] = 1;
	header("Location:index.php");
}


}
?>