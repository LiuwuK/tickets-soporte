<?php
require '../../vendor/autoload.php';
//obtener estados 
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

$stmt_s = $con->prepare("SELECT id, name FROM user WHERE cargo = ?");
$cargo_id = 11;
$stmt_s->bind_param("i", $cargo_id);
$stmt_s->execute();
$result_sup = $stmt_s->get_result();

$turnos = [];

$query = 'SELECT su.nombre AS "instalacion",
            te.fecha_turno AS "fechaTurno", 
            te.horas_cubiertas AS "horas", 
            te.monto AS "monto",
            te.nombre_colaborador AS "colaborador", 
            te.rut AS "rut",  dp.banco AS "banco", 
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
            JOIN user us ON (te.autorizado_por = us.id)';
if ($_SESSION['cargo'] == 11){
    $id  = $_SESSION['id'];
    $query .= " WHERE te.autorizado_por = '$id' ";
}
if (isset($_SESSION['deptos']) && is_array($_SESSION['deptos'])) {
    $estados = [
        10 => "aprobado"
    ];

    $estadoEncontrado = false;
    foreach ($estados as $depto => $estado) {
        if (array_intersect([$depto], $_SESSION['deptos'])) {
            $query .= "WHERE (te.created_at >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 1 WEEK)
            AND te.created_at < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)) AND te.estado = '$estado' ";
            $estadoEncontrado = true;
            break;
        }
    }
}

$query .= " ORDER BY te.created_at DESC"; 
$result = mysqli_query($con, $query);
$turnos = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Convertir a JSON para JavaScript
echo "<script>var turnosData = " . json_encode($turnos) . ";</script>";

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