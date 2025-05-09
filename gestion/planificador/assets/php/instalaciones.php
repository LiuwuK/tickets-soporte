<?php
$historico = [];

$query = "  SELECT su.*, dt.nombre_departamento AS cost_center, sup.nombre_supervisor AS 'nSup', ci.nombre_ciudad AS 'nCiudad'
            FROM sucursales su JOIN departamentos dt ON(su.departamento_id = dt.id)
            JOIN supervisores sup ON(su.supervisor_id = sup.id )
            JOIN ciudades ci ON(su.ciudad_id = ci.id)
            ORDER BY su.nombre ASC";
$result = mysqli_query($con, $query);

// Obtener todos los resultados directamente en un array asociativo
$sucursal = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Convertir a JSON para JavaScript
echo "<script>var sucursalData = " . json_encode($sucursal) . ";</script>";
?>