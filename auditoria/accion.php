<?php
//var_dump($_POST);

/*
$id =  $_POST['id'];
$usuario =  $_POST['nombre'];

echo $id.' '.$usuario;
*/


$dsn = "1 - CENTRAL";
$user = "Axoft";
$pass = "Axoft";

$cid = odbc_connect($dsn, $user, $pass);

if(!$cid){echo "</br>Imposible conectarse a la base de datos!</br>";}

$id =  $_POST['id'];
$usuario =  $_POST['usuario'];

$sql="
SET DATEFORMAT YMD

UPDATE SOF_INVENTARIO SET USUARIO = '$usuario' WHERE ID = $id

";

odbc_exec($cid,$sql)or die(exit("Error en odbc_exec"));




?>