
<body>


<select name="select" id="obj_personas">
<option value=""></option>

<?php
include ('conn.php');
$rs = $mysqli->query("SELECT * FROM tb_personas");
while ($row = $rs->fetch_assoc()){
    echo "<option value='" .$row['id_personas'] ."'>" .$row['nombre_personas'] ."</option>";
}
 
?>
</select>


</body>