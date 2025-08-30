<?php
require '../../vendor/autoload.php';

// Obtener estados
$query = "SHOW COLUMNS FROM turnos_extra LIKE 'estado'";
$result = $con->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $type = $row['Type']; 
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $valoresEnum = explode("','", $matches[1]);
} else {
    die("Error al obtener los valores del enum.");
}

// Obtener supervisores
$stmt_s = $con->prepare("SELECT id, name FROM user WHERE cargo = ?");
$cargo_id = 11;
$stmt_s->bind_param("i", $cargo_id);
$stmt_s->execute();
$result_sup = $stmt_s->get_result();

// Configuración de paginación
$limite = 20; 
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $limite;

// Obtener filtros
$filtros = [
    'texto' => isset($_GET['texto']) ? trim($_GET['texto']) : '',
    'estado' => isset($_GET['estado']) ? trim($_GET['estado']) : '',
    'supervisor' => isset($_GET['supervisor']) ? (int)$_GET['supervisor'] : 0,
    'fecha_inicio' => isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '',
    'fecha_fin' => isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '',
    'semana_actual' => isset($_GET['semana_actual']) ? true : false
];

// onsulta base
$query = 'SELECT su.nombre AS "instalacion",
            te.fecha_turno AS "fechaTurno", 
            te.horas_cubiertas AS "horas", 
            te.monto AS "monto",
            te.nombre_colaborador AS "colaborador", 
            te.rut AS "rut",  
            dp.banco AS "banco", 
            dp.rut_cta || "-" || dp.digito_verificador AS "RUTcta", 
            dp.numero_cuenta "numCuenta",
            mg.motivo AS "motivo", 
            te.estado AS "estado", 
            te.created_at AS "fechaCreacion",
            us.name AS "autorizadoPor",
            te.id  AS "id",
            te.autorizado_por AS "supID",
            EXISTS (
                SELECT 1 FROM historico_turnos 
                WHERE turno_id = te.id
            ) AS "tiene_historico"
          FROM turnos_extra te
          LEFT JOIN sucursales su ON (te.sucursal_id = su.id)
          JOIN datos_pago dp ON (te.datos_bancarios_id = dp.id)
          JOIN motivos_gestion mg ON (te.motivo_turno_id = mg.id)
          JOIN user us ON (te.autorizado_por = us.id)
          WHERE 1=1';

// Consulta para contar total 
$count_query = 'SELECT COUNT(*) as total
                FROM turnos_extra te
                LEFT JOIN sucursales su ON (te.sucursal_id = su.id)
                JOIN datos_pago dp ON (te.datos_bancarios_id = dp.id)
                JOIN motivos_gestion mg ON (te.motivo_turno_id = mg.id)
                JOIN user us ON (te.autorizado_por = us.id)
                WHERE 1=1';

// Aplicar filtros
$where_conditions = [];
$params = [];
$types = '';

if ($_SESSION['cargo'] == 11) {
    $where_conditions[] = " te.autorizado_por = ? ";
    $params[] = $_SESSION['id'];
    $types .= 'i';
}

if (!empty($filtros['texto'])) {
    $where_conditions[] = " (te.nombre_colaborador LIKE ? OR te.rut LIKE ? OR us.name LIKE ? OR su.nombre LIKE ? OR mg.motivo LIKE ?) ";
    $search_term = '%' . $filtros['texto'] . '%';
    $params = array_merge($params, array_fill(0, 5, $search_term));
    $types .= str_repeat('s', 5);
}

if (!empty($filtros['estado'])) {
    $where_conditions[] = " te.estado = ? ";
    $params[] = $filtros['estado'];
    $types .= 's';
}

if (!empty($filtros['supervisor'])) {
    $where_conditions[] = " te.autorizado_por = ? ";
    $params[] = $filtros['supervisor'];
    $types .= 'i';
}

if (!empty($filtros['fecha_inicio'])) {
    $where_conditions[] = " te.fecha_turno >= ? ";
    $params[] = $filtros['fecha_inicio'];
    $types .= 's';
}

if (!empty($filtros['fecha_fin'])) {
    $where_conditions[] = " te.fecha_turno <= ? ";
    $params[] = $filtros['fecha_fin'];
    $types .= 's';
}

if ($filtros['semana_actual']) {
    $where_conditions[] = " (te.created_at >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 1 WEEK)
                          AND te.created_at < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)) ";
}

// Aplicar condiciones WHERE
if (!empty($where_conditions)) {
    $query .= " AND " . implode(" AND ", $where_conditions);
    $count_query .= " AND " . implode(" AND ", $where_conditions);
}

// Consulta para contar total
$stmt_count = $con->prepare($count_query);

if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}

$stmt_count->execute();
$total_result = $stmt_count->get_result();
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limite);

// Consulta principal 
$query .= " ORDER BY te.created_at DESC LIMIT ? OFFSET ?";

// Agregar parámetros de paginación
$params_pagination = $params;
$types_pagination = $types . 'ii';
$params_pagination[] = $limite;
$params_pagination[] = $offset;

$stmt = $con->prepare($query);
$stmt->bind_param($types_pagination, ...$params_pagination);
$stmt->execute();
$result = $stmt->get_result();
$turnos = $result->fetch_all(MYSQLI_ASSOC);

echo "<script>
    var turnosData = " . json_encode($turnos) . ";
    var paginacionData = {
        pagina_actual: $pagina,
        total_paginas: $total_pages,
        total_registros: $total_rows,
        limite: $limite
    };
</script>";

echo "<script>
    var filtrosActuales = " . json_encode($filtros) . ";
</script>";

use PhpOffice\PhpSpreadsheet\IOFactory;
//Actualizar turnos masivamente 
if (isset($_POST['carga'])) {
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Preparar consultas
        $queryEstado = "UPDATE turnos_extra 
                        SET estado = ? 
                        WHERE id = ?";
        $stmtEstado = $con->prepare($queryEstado);

        $queryEstadoYRechazo = "UPDATE turnos_extra 
                                SET estado = ?,
                                    motivo_rechazo = ? 
                                WHERE id = ?";
        $stmtEstadoYRechazo = $con->prepare($queryEstadoYRechazo);

        if (!$stmtEstado || !$stmtEstadoYRechazo) {
            die("Error al preparar las consultas: " . $con->error);
        }

        foreach ($data as $index => $row) {
            if ($index < 1) continue; // Saltar la primera fila (encabezados)

            // Mostrar datos de la fila (para depuración)
            /* 
            echo "<pre>";
            print_r($row);
            echo "</pre>";
            */
            // Obtener datos de la fila
            $id = $row[0] ?? null;
            $estado = ucwords(strtolower($row[2] ?? ''));
            $motivo_rechazo = $row[23] ?? null;

            if (empty($id)) {
                echo "Error: ID vacío en la fila $index.<br>";
                continue;
            }
            if (empty($estado)) {
                echo "Error: Estado vacío en la fila $index.<br>";
                continue;
            }

            if ($motivo_rechazo && strtolower($estado) == 'rechazado') {
                $stmtEstadoYRechazo->bind_param("ssi", $estado, $motivo_rechazo, $id);
                if (!$stmtEstadoYRechazo->execute()) {
                    echo "Error al actualizar fila $index: ".$stmtEstadoYRechazo->error."<br>";
                }
            } else {
                $stmtEstado->bind_param("si", $estado, $id);
                if (!$stmtEstado->execute()) {
                    echo "Error al actualizar fila $index: ".$stmtUpdateEstado->error."<br>";
                }
            }
        }
        echo "<script>alert('Turnos actualizados correctamente'); location.href='ver-turnos.php';</script>";
    } else {
        echo "Error al subir el archivo.";
    }
}
?>