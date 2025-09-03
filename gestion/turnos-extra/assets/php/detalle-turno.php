<?php
// Funciones auxiliares
function puedeEditarTurno($turno, $usuario) {
    $es_supervisor = ($usuario['cargo'] == 11);
    $es_operador = in_array(6, $usuario['deptos']);
    $turno_rechazado = ($turno['estado'] == 'rechazado');
    $es_autor = ($usuario['id'] == $turno['idAuto']);
    return $turno_rechazado && (($es_supervisor && $es_autor) || $es_operador);
}

function registrarHistoricoTurno($con, $idTurno, $usuarioId, $cambios, $justificacion) {
    $stmt = $con->prepare("INSERT INTO historico_turnos 
        (turno_id, usuario_id, accion, cambios, justificacion) 
        VALUES (?, ?, 'editado', ?, ?)");
    $json = json_encode($cambios, JSON_UNESCAPED_UNICODE);
    $stmt->bind_param("iiss", $idTurno, $usuarioId, $json, $justificacion);
    $stmt->execute();
    $stmt->close();
}

function actualizarDatosBancarios($con, $idDatos, $rut, $dv, $numCuenta) {
    $stmt = $con->prepare("UPDATE datos_pago SET rut_cta=?, digito_verificador=?, numero_cuenta=? WHERE id=?");
    $stmt->bind_param("sssi", $rut, $dv, $numCuenta, $idDatos);
    $stmt->execute();
    $stmt->close();
}


$id = (int)$_GET['id'];

$query = "SELECT su.nombre AS instalacion,
                 te.fecha_turno AS fechaTurno,
                 te.horas_cubiertas AS horas,
                 te.monto AS monto,
                 te.nombre_colaborador AS colaborador,
                 te.rut AS rut,
                 bc.nombre_banco AS banco,
                 CONCAT(dp.rut_cta, '-', dp.digito_verificador) AS RUTcta,
                 dp.numero_cuenta AS numCuenta,
                 mg.motivo AS motivo,
                 te.estado AS estado,
                 te.created_at AS fechaCreacion,
                 us.name AS autorizadoPor,
                 te.id AS id,
                 te.motivo_rechazo AS motivoN,
                 te.persona_motivo AS persona_motivo,
                 te.contratado AS contratado,
                 te.justificacion AS justificacion,
                 te.nacionalidad AS nacionalidad,
                 te.autorizado_por AS idAuto,
                 EXISTS (SELECT 1 FROM historico_turnos WHERE turno_id = te.id) AS justificado,
                 CONCAT(TIME_FORMAT(te.hora_inicio, '%H:%i'), ' - ', TIME_FORMAT(te.hora_termino, '%H:%i')) AS horario
          FROM turnos_extra te
          LEFT JOIN sucursales su ON te.sucursal_id = su.id
          JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
          JOIN bancos bc ON dp.banco = bc.id
          JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
          JOIN `user` us ON te.autorizado_por = us.id
          WHERE te.id = ?";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$turno = $result->fetch_assoc();
$stmt->close();

if (!$turno) die("No se encontró ningún turno con el ID proporcionado");

// Histórico de cambios
$query_historico = "SELECT h.fecha, u.name AS usuario, h.cambios, h.justificacion
                    FROM historico_turnos h
                    JOIN user u ON h.usuario_id = u.id
                    WHERE h.turno_id = ? ORDER BY h.fecha DESC";
$stmt_historico = $con->prepare($query_historico);
$stmt_historico->bind_param("i", $id);
$stmt_historico->execute();
$historico = $stmt_historico->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_historico->close();

// Permiso para editar
$puede_editar = puedeEditarTurno($turno, $_SESSION);

// Acciones: aprobar, pago, rechazar, editar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['approved'])) {
        $stmt = $con->prepare("UPDATE turnos_extra SET estado='aprobado', motivo_rechazo=NULL, justificacion=NULL WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['aprobarTurno'] = [
            'title' => 'Éxito',
            'html' => 'Turno aprobado correctamente',
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ];
        header("Location: detalle-turno.php?id=$id"); exit;
    }

    if (isset($_POST['pago'])) {
        $stmt = $con->prepare("UPDATE turnos_extra SET estado='pago procesado' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['aprobarTurno'] = [
            'title' => 'Éxito',
            'html' => 'Pago aprobado correctamente',
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ];
        header("Location: detalle-turno.php?id=$id"); exit;
    }

    if (isset($_POST['denTurno'])) {
        $motivo = $_POST['motivoR'];
        $stmt = $con->prepare("UPDATE turnos_extra SET estado='rechazado', motivo_rechazo=? WHERE id=?");
        $stmt->bind_param("si", $motivo, $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['rechazoTurno'] = [
            'title' => 'Éxito',
            'html' => 'Turno rechazado correctamente',
            'icon' => 'success',
            'confirmButtonText' => 'Aceptar'
        ];
        header("Location: detalle-turno.php?id=$id"); exit;
    }

    if (isset($_POST['guardar_cambios']) && $puede_editar) {
    $justificacion = $_POST['justificacion'];
    $con->begin_transaction();
    try {
        $cambios = [];
        $updates = [];
        $params = [];
        $types = '';

        // Campos del formulario al DB
        $fieldMap = [
            'colab' => 'nombre_colaborador',
            'rutC' => 'rut',
            'fturno' => 'fecha_turno',
            'mt' => 'monto',
            'pMotivo' => 'persona_motivo'
        ];

        foreach ($fieldMap as $form => $dbField) {
            if(isset($_POST[$form])) {
                $valorNuevo = $dbField === 'monto' ? str_replace(['$', '.'], '', $_POST[$form]) : $_POST[$form];
                if($valorNuevo != $turno[$dbField]) {
                    $updates[] = "$dbField=?";
                    $params[] = $valorNuevo;
                    $types .= 's';
                    $cambios[$dbField] = ['antes'=>$turno[$dbField],'despues'=>$valorNuevo];
                }
            }
        }

        // Datos bancarios
        $banco_cambiado = false;
        if(isset($_POST['rutCta'], $_POST['numCta'])) {
            $rutParts = explode('-', $_POST['rutCta']);
            if(count($rutParts)===2){
                [$rut_cta, $dv] = $rutParts;

                // Separar RUT y DV almacenados
                $rutActualParts = explode('-', $turno['RUTcta']);
                $rutActual = $rutActualParts[0] ?? '';
                $dvActual = $rutActualParts[1] ?? '';

                if($rut_cta != $rutActual || $dv != $dvActual || $_POST['numCta'] != $turno['numCuenta']){
                    actualizarDatosBancarios($con, $turno['datos_bancarios_id'], $rut_cta, $dv, $_POST['numCta']);
                    $banco_cambiado = true;

                    // Guardar como array asociativo para el histórico
                    $cambios['datos_bancarios'] = [
                        'antes' => [
                            'rut_cta' => $rutActual,
                            'digito_verificador' => $dvActual,
                            'numero_cuenta' => $turno['numCuenta']
                        ],
                        'despues' => [
                            'rut_cta' => $rut_cta,
                            'digito_verificador' => $dv,
                            'numero_cuenta' => $_POST['numCta']
                        ]
                    ];
                }
            }
        }

        if(!empty($updates) || $banco_cambiado){
            $updates[] = "justificacion=?";
            $params[] = $justificacion;
            $types .= 's';

            $queryUpdate = "UPDATE turnos_extra SET ".implode(',', $updates)." WHERE id=?";
            $params[] = $id;
            $types .= 'i';

            $stmt = $con->prepare($queryUpdate);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();

            registrarHistoricoTurno($con, $id, $_SESSION['id'], $cambios, $justificacion);
            $con->commit();

            // SweetAlert
            $_SESSION['swal'] = [
                'title' => 'Éxito',
                'html' => 'Turno justificado correctamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar',
                'redirect' => "detalle-turno.php?id=$id"
            ];
        } else {
            $con->rollback();
            $_SESSION['swal'] = [
                'title' => 'Advertencia',
                'html' => 'No se realizaron cambios',
                'icon' => 'warning',
                'confirmButtonText' => 'Aceptar',
                'redirect' => "detalle-turno.php?id=$id"
            ];
        }

        header("Location: detalle-turno.php?id=$id"); 
        exit;

    } catch (Exception $e){
        $con->rollback();
        $_SESSION['swal'] = [
            'title'=>'Error',
            'html'=>$e->getMessage(),
            'icon'=>'error',
            'confirmButtonText'=>'Aceptar',
            'redirect' => "detalle-turno.php?id=$id"
        ];
        header("Location: detalle-turno.php?id=$id"); 
        exit;
    }
    }

}
?>
