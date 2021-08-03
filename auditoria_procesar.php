<?php 
session_start(); 
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{
	
$permiso = $_SESSION['permisos'];
$user = $_SESSION['username'];
	
$dsn = '1 - CENTRAL';
$usuario = "sa";
$clave="Axoft1988";

$codClient = $_SESSION['codClient'];

$cid=odbc_connect($dsn, $usuario, $clave);


for($i=0;$i<count($_POST['codArticu']);$i++){
	
	$rem = $_POST['rem'][$i];
	$id = $_POST['id'][$i];
	$codArticu = $_POST['codArticu'][$i];
	$observ = $_POST['observ_auditoria'][$i];
	
	//echo $rem.' '.$codArticu.' '.$observ.'<br>';
	
	$sql= 
	"
	UPDATE SJ_CONTROL_AUDITORIA SET OBSERVAC_AUDITORIA = '$observ'
	WHERE NRO_REMITO = '$rem' AND COD_ARTICU = '$codArticu'
	AND ID = $id 
	";
	
	odbc_exec($cid, $sql) or die(exit("Error en odbc_exec"));
}

}
?>
<script>setTimeout(function () {window.location.href= 'control_auditoria.php';},1);</script>