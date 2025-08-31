<?php
session_start();
require_once '../../../../dbconnection.php'; 

$sucursalId = isset($_GET['sucursal_id']) ? intval($_GET['sucursal_id']) : null;

$primer_dia = date('Y-m-01');
$primer_dia_sig = date('Y-m-01', strtotime('+1 month'));

$query = "
    SELECT r.fecha, s.id AS sucursal_id, s.nombre AS sucursal, r.monto, r.id
    FROM recuperaciones r
    JOIN sucursales s ON r.sucursal_id = s.id
    WHERE r.fecha >= '$primer_dia'
      AND r.fecha < '$primer_dia_sig'
      " . ($sucursalId ? " AND s.id = $sucursalId" : "") . "
    ORDER BY r.fecha
";

$result = $con->query($query);

$events = [];
$totales = [];

while ($row = $result->fetch_assoc()) {
    // Para FullCalendar
    $events[] = [
        'title' => $row['sucursal'].' - $'.number_format($row['monto']),
        'start' => $row['fecha'],
        'color' => '#378006',
        'extendedProps' => [
            'monto' => $row['monto'],
            'sucursal' => $row['sucursal'],
            'id' => $row['id']
        ]
    ];

    // Totales por sucursal
    if (!isset($totales[$row['sucursal']])) $totales[$row['sucursal']] = 0;
    $totales[$row['sucursal']] += $row['monto'];
}

$_SESSION['recuperaciones_export'] = ['events' => $events, 'totales' => $totales];

echo json_encode([
    'events' => $events,
    'totales' => $totales
]);

$con->close();
?>
