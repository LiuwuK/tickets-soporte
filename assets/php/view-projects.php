<?php
$userId = $_SESSION["user_id"];
//Se obtienen los proyectos asociados al usuario
$query = "SELECT pr.id AS projectId, pr.*, es.nombre AS estado, ci.nombre_ciudad AS ciudadN, us.name AS ingeniero, tp.nombre AS tipoP
            FROM proyectos pr 
            JOIN estados es ON(pr.estado_id = es.id)
            JOIN ciudades ci ON(pr.ciudad = ci.id)
            LEFT JOIN user us ON(pr.ingeniero_responsable = us.id)
            JOIN tipo_proyecto tp ON(pr.tipo = tp.id)
            WHERE pr.comercial_responsable = $userId";
$stmt = $con->prepare($query);
$stmt->execute();
$rt = $stmt->get_result();



//total de resultados
$num = $rt->num_rows; 
?>