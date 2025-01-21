<?php
//obtener datos del distribuidor
$query = "SELECT * 
            FROM distribuidores
            WHERE id = '".$_GET['id']."'";
$distribuidorData = mysqli_query($con, $query);
$distribuidor = mysqli_fetch_assoc($distribuidorData);
//obtener proyectos asociados
$query = "SELECT *
            FROM proyectos
            WHERE distribuidor = '".$_GET['id']."' AND estado_id = '20'";
$proyectos = mysqli_query($con, $query);

?>
