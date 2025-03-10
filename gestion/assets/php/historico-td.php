<?php
$historico = [];

$query = "
    SELECT 
        tr.id, 
        tr.fecha_registro AS fecha, 
        'traslado' AS tipo, 
        tr.nombre_colaborador AS colaborador, 
        tr.rut, 
        tr.estado, 
        us.name AS solicitante,
        tr.observacion,
        tr.obs_rrhh
    FROM traslados tr
    JOIN user us ON tr.solicitante = us.id
";
$query .= "  
    UNION ALL  
    SELECT 
        de.id, 
        de.fecha_registro AS fecha, 
        'desvinculación' AS tipo, 
        de.colaborador, 
        de.rut, 
        de.estado, 
        us.name AS solicitante,
        de.observacion,
        de.obs_rrhh
    FROM desvinculaciones de
    JOIN user us ON de.solicitante = us.id
";
if($_SESSION['cargo'] == 13){
    $query = "
        SELECT * FROM ($query) AS combined
        WHERE estado = 'realizado'
    ";
}

$query .= " ORDER BY fecha DESC";
$result = mysqli_query($con, $query);

// Obtener todos los resultados directamente en un array asociativo
$historico = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Convertir a JSON para JavaScript
echo "<script>var historicoData = " . json_encode($historico) . ";</script>";
?>