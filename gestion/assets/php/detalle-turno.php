<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Preparar la consulta SQL con parámetros
    $query = 'SELECT su.nombre AS instalacion,
                     te.fecha_turno AS fechaTurno, 
                     te.horas_cubiertas AS horas, 
                     te.monto AS monto,
                     te.nombre_colaborador AS colaborador, 
                     te.rut AS rut,  
                     bc.nombre_banco AS banco, 
                     CONCAT(dp.rut_cta, "-", dp.digito_verificador) AS RUTcta, 
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
                     CONCAT(TIME_FORMAT(te.hora_inicio, "%H:%i"), " - ", TIME_FORMAT(te.hora_termino, "%H:%i")) AS horario
              FROM turnos_extra te
              LEFT JOIN sucursales su ON te.sucursal_id = su.id
              JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
              JOIN bancos bc ON dp.banco = bc.id
              JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
              JOIN `user` us ON te.autorizado_por = us.id
              WHERE te.id = ?';

    // Preparar la consulta
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error al preparar la consulta: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
    }
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die("Error al obtener resultados: " . mysqli_error($con));
    }
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        die("No se encontró ningún turno con el ID proporcionado");
    }

    $query_historico = "SELECT 
                            h.fecha,
                            u.name AS usuario,
                            h.cambios,
                            h.justificacion
                        FROM historico_turnos h
                        JOIN user u ON h.usuario_id = u.id
                        WHERE h.turno_id = ?
                        ORDER BY h.fecha DESC";

    $stmt_historico = mysqli_prepare($con, $query_historico);
    mysqli_stmt_bind_param($stmt_historico, 'i', $id);
    mysqli_stmt_execute($stmt_historico);
    $result_historico = mysqli_stmt_get_result($stmt_historico);
    $historico = mysqli_fetch_all($result_historico, MYSQLI_ASSOC);
} else {
    die("No se proporcionó ID de turno");
}
//aprobar turno
if(isset($_POST['approved'])){
    $id = $_GET['id'];
    $estado = 'aprobado';
    $query = "UPDATE turnos_extra 
                SET estado = ?,
                    motivo_rechazo = null,
                    justificacion = null
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();
    $_SESSION['aprobarTurno'] = [
        'title' => 'Éxito',
        'html' => 'Turno aprobado correctamente',
        'icon' => 'success',
        'confirmButtonText' => 'Aceptar'
    ];
}
//aprobar pago del turno
if(isset($_POST['pago'])){
    $id = $_GET['id'];
    $estado = 'pago procesado';
    $query = "UPDATE turnos_extra 
                SET estado = ? 
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();
    $_SESSION['aprobarTurno'] = [
        'title' => 'Éxito',
        'html' => 'Pago aprobado correctamente',
        'icon' => 'success',
        'confirmButtonText' => 'Aceptar'
    ];
}

//rechazar turno
if(isset($_POST['denTurno'])){
    $id = $_GET['id'];
    $estado = 'rechazado';
    $motivo = $_POST['motivoR'];
    $query = "UPDATE turnos_extra 
                SET estado = ? ,
                    motivo_rechazo = ?
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssi", $estado, $motivo, $id);
    $stmt->execute();

    $_SESSION['rechazoTurno'] = [
        'title' => 'Éxito',
        'html' => 'Turno rechazado correctamente',
        'icon' => 'success',
        'confirmButtonText' => 'Aceptar'
    ];
}

//justificacion del turno
if(isset($_POST['guardar_cambios'])) {
    $id_turno = $_GET['id'];
    $justificacion = $_POST['justificacion'];
    
    // datos actuales del turno
    $stmt = $con->prepare("SELECT 
        estado, autorizado_por, datos_bancarios_id,
        nombre_colaborador, rut, fecha_turno, monto, 
        persona_motivo, nacionalidad
        FROM turnos_extra WHERE id = ?");
    $stmt->bind_param("i", $id_turno);
    $stmt->execute();
    $turno_actual = $stmt->get_result()->fetch_assoc();
    
    if(!$turno_actual) {
        die("Turno no encontrado");
    }

    
    // Validar permisos de forma más segura
    $es_supervisor = ($_SESSION['cargo'] == 11);
    $es_operador = array_intersect([6], $_SESSION['deptos']);
    $turno_rechazado = ($turno_actual['estado'] == 'rechazado');
    $es_autor = ($_SESSION['id'] == $turno_actual['autorizado_por']);
    
    $puede_editar = ($turno_rechazado && (
        ($es_supervisor && $es_autor) || 
        $es_operador
    ));
    
    if(!$puede_editar) {
        die("No tienes permisos para editar este turno");
    }

    $con->begin_transaction();
    try {
        // Mapeo de campos (nombre en formulario => nombre en BD)
        $fieldMap = [
            'colab' => 'nombre_colaborador',
            'rutC' => 'rut',
            'fturno' => 'fecha_turno',
            'mt' => 'monto',
            'pMotivo' => 'persona_motivo'
        ];
        
        $cambios = [];
        $updates = [];
        $params = [];
        $types = '';
        
        foreach($fieldMap as $formField => $dbField) {
            if(isset($_POST[$formField])) {
                $nuevo_valor = $_POST[$formField];
                if($dbField === 'monto') {
                    $nuevo_valor = str_replace(['$', '.'], '', $nuevo_valor);
                    if(!is_numeric($nuevo_valor)) {
                        throw new Exception("El monto debe ser un valor numérico válido");
                    }
                }
                $valor_actual = $turno_actual[$dbField] ?? null;

                if($nuevo_valor != $valor_actual) {
                    $updates[] = "$dbField = ?";
                    $params[] = $nuevo_valor;
                    $types .= 's';
                    $cambios[$dbField] = [
                        'antes' => $valor_actual,
                        'despues' => $dbField === 'monto' ? '$'.number_format($nuevo_valor, 0, '', '.') : $nuevo_valor
                    ];
                }
            }
        }
        // Procesar datos bancarios 
        $banco_cambiado = false;
        
        if(isset($_POST['rutCta']) && isset($_POST['numCta'])) {
            // Obtener datos actuales
            $stmt = $con->prepare("SELECT * FROM datos_pago WHERE id = ?");
            $stmt->bind_param("i", $turno_actual['datos_bancarios_id']);
            $stmt->execute();
            $datos_actuales = $stmt->get_result()->fetch_assoc();

            if(!$datos_actuales) {
                throw new Exception("Datos bancarios no encontrados");
            }

            // Procesar RUT (formato: 12345678-9)
            $rutParts = explode('-', $_POST['rutCta']);
            if(count($rutParts) != 2) {
                throw new Exception("Formato de RUT inválido. Debe ser 12345678-9");
            }
            
            $rut_cta = $rutParts[0];
            $digito_verificador = $rutParts[1];
            
            // Comparar cambios
            if($datos_actuales['rut_cta'] != $rut_cta || 
               $datos_actuales['digito_verificador'] != $digito_verificador ||
               $datos_actuales['numero_cuenta'] != $_POST['numCta']) {
                
                $banco_cambiado = true;
                
                // Registrar cambios
                $cambios['datos_bancarios'] = [
                    'antes' => [
                        'rut_cta' => $datos_actuales['rut_cta'],
                        'digito_verificador' => $datos_actuales['digito_verificador'],
                        'numero_cuenta' => $datos_actuales['numero_cuenta']
                    ],
                    'despues' => [
                        'rut_cta' => $rut_cta,
                        'digito_verificador' => $digito_verificador,
                        'numero_cuenta' => $_POST['numCta']
                    ]
                ];
                
                // Actualizar datos_pago
                $query_dp = "UPDATE datos_pago SET 
                            rut_cta = ?, 
                            digito_verificador = ?, 
                            numero_cuenta = ? 
                            WHERE id = ?";
                
                $stmt = $con->prepare($query_dp);
                $stmt->bind_param("sssi", $rut_cta, $digito_verificador, $_POST['numCta'], $turno_actual['datos_bancarios_id']);
                if(!$stmt->execute()) {
                    throw new Exception("Error al actualizar datos bancarios: " . $stmt->error);
                }
            }
        }
        
        // Actualizar turno (solo si hay cambios)
        if(!empty($updates) || $banco_cambiado) {
            // Agregar campos fijos
            $updates[] = "justificacion = ?";
            $updates[] = "estado = 'pendiente en operaciones'";
            $params[] = $justificacion;
            $types .= 's';
            
            $query = "UPDATE turnos_extra SET ".implode(', ', $updates)." WHERE id = ?";
            $params[] = $id_turno;
            $types .= 'i';
            
            $stmt = $con->prepare($query);
            if(!$stmt->bind_param($types, ...$params)) {
                throw new Exception("Error al vincular parámetros: " . $stmt->error);
            }
            
            if(!$stmt->execute()) {
                throw new Exception("Error al actualizar turno: " . $stmt->error);
            }
            
            // Registrar histórico
            $stmt_historico = $con->prepare("INSERT INTO historico_turnos 
                (turno_id, usuario_id, accion, cambios, justificacion) 
                VALUES (?, ?, 'editado', ?, ?)");
            $json_cambios = json_encode($cambios, JSON_UNESCAPED_UNICODE);
            
            if(!$stmt_historico->bind_param("iiss", $id_turno, $_SESSION['id'], $json_cambios, $justificacion)) {
                throw new Exception("Error al vincular histórico: " . $stmt_historico->error);
            }
            
            if(!$stmt_historico->execute()) {
                throw new Exception("Error al guardar histórico: " . $stmt_historico->error);
            }
            
            $con->commit();
            $_SESSION['swal'] = [
                'title' => 'Éxito',
                'html' => 'Turno justificado correctamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar'
            ];
            echo '<script>window.location.href = "detalle-turno.php?id='.$id_turno.'";</script>';  
        } else {
            $con->rollback();
            $_SESSION['swal'] = [
                'title' => 'Advertencia',
                'html' => 'No se realizaron cambios',
                'icon' => 'warning',
                'confirmButtonText' => 'Aceptar'
            ];
            echo '<script>window.location.href = "detalle-turno.php?id='.$id_turno.'";</script>';   
        }
        exit;
    
    } catch (Exception $e) {
       $con->rollback();
        $_SESSION['swal'] = [
            'title' => 'Error',
            'html' => 'Error: ' . $e->getMessage(),
            'icon' => 'error',
            'confirmButtonText' => 'Aceptar'
        ];
        echo '<script>window.location.href = "detalle-turno.php?id='.$id_turno.'";</script>';
        exit;
    }
}
?>
