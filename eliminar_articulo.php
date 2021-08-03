<?php
session_start();
if(!isset($_SESSION['username'])){
	header("Location:../login.php");
}else{

$codigo = $_GET['codigo'];

$codClient = $_SESSION['codClient'];


$dsn = "1 - CENTRAL";
$user = "sa";
$pass = "Axoft1988";

$cid = odbc_connect($dsn, $user, $pass);

$sql="
DELETE FROM SJ_CONTROL_LOCAL 
WHERE COD_CLIENT = '$codClient'
AND COD_ARTICU = '$codigo'
";


odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));


?>

<link rel="shortcut icon" href="icono.jpg" />
<script>setTimeout(function () {window.location.href= 'controlRemitos.php';},1);</script>

<?php
}
?>