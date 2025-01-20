<?php
//obtener ingenieros
$query =  "SELECT id, name 
            FROM user
            WHERE cargo = '1'";
$inge = mysqli_query($con, $query);
$ingenieros = [];
while ($row = mysqli_fetch_assoc($inge)) {
    $ingenieros[] = $row;
}

//carga las verticales y distribuidores para filtrar--------------------------------------------------------------
$query = "SELECT * FROM verticales ";
$verticalData = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($verticalData)) {
  $verticales[] = $row; 
}

$query = "SELECT * FROM distribuidores ";
$distribuidorData = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($distribuidorData)) {
  $distData[] = $row; 
}
//----------------------------------------------------------------------------------------------------------

$userId = $_SESSION["user_id"];
//Se obtienen los proyectos asociados al usuario
$query = "SELECT pr.id AS projectId, pr.*, es.nombre AS estado, ci.nombre_ciudad AS ciudadN, us.name AS ingeniero, 
                us_com.name AS comercial, tp.nombre AS tipoP, dt.nombre AS distribuidorN
            FROM proyectos pr 
            JOIN estados es ON(pr.estado_id = es.id)
            JOIN ciudades ci ON(pr.ciudad = ci.id)
            LEFT JOIN user us ON(pr.ingeniero_responsable = us.id)
            LEFT JOIN distribuidores dt ON(pr.distribuidor = dt.id)
            JOIN user us_com ON (pr.comercial_responsable = us_com.id)
            JOIN tipo_proyecto tp ON(pr.tipo = tp.id)
            
            WHERE pr.comercial_responsable = $userId";
$stmt = $con->prepare($query);
$stmt->execute();
$rt = $stmt->get_result();



//total de resultados
$num = $rt->num_rows; 
?>