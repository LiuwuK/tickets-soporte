<?php
require '../../vendor/autoload.php';
date_default_timezone_set('America/Santiago'); 
//obtener sucursales
$query = "SELECT * 
            FROM sucursales
            ORDER BY nombre ASC";
$sucursalesData = $con->prepare($query);
$sucursalesData->execute();
$sucursalData = $sucursalesData->get_result();
while ($row = mysqli_fetch_assoc($sucursalData)) {
    $inst[] = $row; 
}

//obtener bancos
$query = "SELECT * FROM bancos";
$bancosData = $con->prepare($query);
$bancosData->execute();
$bancodata = $bancosData->get_result();
while ($row = mysqli_fetch_assoc($bancodata)) {
    $bancos[] = $row; 
}

//motivos turnos extra
$query = "SELECT * 
            FROM motivos_gestion
            WHERE tipo_motivo = 'turnos'";
$motivosData = $con->prepare($query);
$motivosData->execute();
$motivoData = $motivosData->get_result();
while ($row = mysqli_fetch_assoc($motivoData)) {
    $motivo[] = $row; 
}


if (isset($_POST['newExtra'])) {
    $turnos = $_POST['nuevos_turnos'];
    $errores = [];
    $exitos = 0;
    $usuario_id = $_SESSION['id']; 
    /*
    echo '<pre>Datos recibidos:';
    print_r($_POST);
    echo '</pre>';
    */

    $turnosValidos = array_filter($turnos, function($t) {
    return !empty($t['motivo']) || !empty($t['rut']) || !empty($t['fecha']);
    });

    if (empty($turnosValidos)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Error',
            'message' => 'No se recibieron datos de turnos válidos'
        ];
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
    }

    // Iniciar transacción
    $con->begin_transaction();

    try {
        $queryCheckBanco = "SELECT id FROM datos_pago WHERE banco = ? AND rut_cta = ? AND numero_cuenta = ? LIMIT 1";
        $stmtCheckBanco = $con->prepare($queryCheckBanco);
        
        $queryInsertBanco = "INSERT INTO datos_pago (banco, rut_cta, digito_verificador, numero_cuenta) 
                           VALUES (?, ?, ?, ?)";
        $stmtInsertBanco = $con->prepare($queryInsertBanco);
        
        $queryInsertTurno = "INSERT INTO turnos_extra 
                           (sucursal_id, fecha_turno, horas_cubiertas, monto, nombre_colaborador, 
                            rut, datos_bancarios_id, motivo_turno_id, autorizado_por, persona_motivo, 
                            contratado, nacionalidad, hora_inicio, hora_termino) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertTurno = $con->prepare($queryInsertTurno);

        foreach ($turnosValidos as $index => $turno) {
          
            if (empty($turno['motivo'])) {
                $errores[] = "Debe seleccionar un motivo para el turno";
                continue;
            }

            if (empty($turno['fecha'])) {
                $errores[] = "La fecha del turno es requerida";
                continue;
            }
            if($_SESSION['id'] != 38){
                // validar fecha turno (SOLO DIA ACTUAL HASTA LAS 12:00 DEL DIA SIGUIENTE)
                $fechaTurno = $turno['fecha'];
                $horaActual = (int)date('H');
                $horaMinuto = date('H:i');
                $fechaHoy = date('Y-m-d');
                $fechaAyer = date('Y-m-d', strtotime('-1 day'));
                
                // Convertir fecha recibida a formato Y-m-d para comparación segura
                $fechaTurnoFormateada = date('Y-m-d', strtotime($fechaTurno));

                if ($horaActual < 12) {
                    if ($fechaTurnoFormateada != $fechaAyer && $fechaTurnoFormateada != $fechaHoy) {
                        $errores[] = "Fila " . ($index + 1) . ": La fecha '$fechaTurno' está fuera del plazo permitido";
                        continue;
                    }
                } else {
                    if ($fechaTurnoFormateada != $fechaHoy) {
                        $errores[] = "Fila " . ($index + 1) . ": La fecha '$fechaTurno' está fuera del plazo permitido ";
                        continue;
                    }
                }
            }

            if (empty($turno['hora_entrada']) || empty($turno['hora_salida'])) {
                $errores[] = "Las horas de entrada y salida son requeridas";
                continue;
            }

            if (empty($turno['rut'])) {
                $errores[] = "El RUT del colaborador es requerido";
                continue;
            }

            // Validar formato de horas
            $horaInicio = DateTime::createFromFormat('H:i', $turno['hora_entrada']);
            $horaTermino = DateTime::createFromFormat('H:i', $turno['hora_salida']);

            if (!$horaInicio || !$horaTermino) {
                $errores[] = "Formato de hora inválido.";
                continue;
            }

            // Si la hora de término es menor que la de inicio, asumimos que es del día siguiente
            if ($horaTermino < $horaInicio) {
                $horaTermino->add(new DateInterval('P1D')); // Añade 1 día
            }

            // Calcular diferencia en segundos y convertir a horas
            $diferenciaSegundos = $horaTermino->getTimestamp() - $horaInicio->getTimestamp();
            $horasCubiertas = $diferenciaSegundos / 3600; // 3600 segundos = 1 hora
            
            // Procesar RUT (limpieza básica)
            $rut = strtoupper(preg_replace('/[^0-9kK]/', '', $turno['rut']));

            // Manejar datos bancarios (si existen)
            $bancoId = null;
            if (!empty($turno['banco']) && !empty($turno['rut_cuenta']) && !empty($turno['numero_cuenta'])) {

                $rutCuenta = strtoupper(preg_replace('/[^0-9kK]/', '', $turno['rut_cuenta']));
                $rutCuentaNum = substr($rutCuenta, 0, -1);
                $rutCuentaDv = substr($rutCuenta, -1);


                // Verificar si ya existe
                $stmtCheckBanco->bind_param("iis", $turno['banco'], $rutCuentaNum, $turno['numero_cuenta']);
                $stmtCheckBanco->execute();
                $stmtCheckBanco->store_result();

                if ($stmtCheckBanco->num_rows > 0) {
                    $stmtCheckBanco->bind_result($bancoId);
                    $stmtCheckBanco->fetch();
                } else {
                    // Insertar nuevos datos bancarios
                    $stmtInsertBanco->bind_param("iiss", 
                        $turno['banco'], 
                        $rutCuentaNum, 
                        $rutCuentaDv, 
                        $turno['numero_cuenta']
                    );
                    
                    if ($stmtInsertBanco->execute()) {
                        $bancoId = $stmtInsertBanco->insert_id;
                    } else {
                        if ($con->errno == 1062) {
                            $stmtCheckBanco->execute();
                            $stmtCheckBanco->bind_result($bancoId);
                            $stmtCheckBanco->fetch();
                        } else {
                            throw new Exception("Error al insertar datos bancarios: ".$stmtInsertBanco->error);
                        }
                    }
                }
                $stmtCheckBanco->free_result();
            }
            $instalacion = !empty($turno['instalacion']) ? $turno['instalacion'] : null;
            
            // Insertar turno
            $stmtInsertTurno->bind_param(
                "isdissiiisisss",
                $instalacion,
                $turno['fecha'],
                $horasCubiertas,
                $turno['monto'],
                $turno['nombre'],
                $rut,
                $bancoId,
                $turno['motivo'],
                $usuario_id,
                $turno['persona_motivo'],
                $turno['contratado'],
                $turno['nacionalidad'],
                $turno['hora_entrada'],
                $turno['hora_salida']
            );
            
            if ($stmtInsertTurno->execute()) {
                $exitos++;
            } else {
                $errores[] = "Error al insertar turno: ".$stmtInsertTurno->error;
            }
        }

        if (empty($errores)) {
            $con->commit();
            $_SESSION['alert'] = [
                'type' => 'success',
                'title' => 'Éxito',
                'message' => $exitos > 1 ? 
                    "Se registraron $exitos turnos correctamente" :
                    "Turno registrado correctamente",
                'footer' => 'Fecha: '.date('d/m/Y H:i')
            ];
        } else {
            $con->rollback();
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Error al procesar',
                'message' => implode('<br>', array_unique($errores)),
                'footer' => 'Corrija los errores e intente nuevamente'
            ];
        }

    } catch (Exception $e) {
        $con->rollback();
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Error del sistema',
            'message' => "Ocurrió un error inesperado: ".$e->getMessage(),
            'footer' => 'Contacte al administrador'
        ];
    }

    // Redireccionar
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}


use PhpOffice\PhpSpreadsheet\IOFactory;
function is_empty($value) {
    return $value === null || trim($value) === '';
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['carga'])) {
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Verificar la conexión a la base de datos
        if (!$con) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        // Preparar consultas
        $queryBanco = "SELECT id FROM bancos WHERE nombre_banco = ?";
        $stmtBanco = $con->prepare($queryBanco);
        if (!$stmtBanco) {
            die("Error al preparar la consulta de bancos: " . $con->error);
        }

        $queryCheck = "SELECT dp.id, bc.nombre_banco  
                        FROM datos_pago dp
                        JOIN bancos bc ON(bc.id = dp.banco)
                        WHERE bc.nombre_banco = ? 
                            AND dp.rut_cta = ? 
                            AND dp.digito_verificador = ? 
                            AND dp.numero_cuenta = ?";
        $stmtCheck = $con->prepare($queryCheck);
        if (!$stmtCheck) {
            die("Error al preparar la consulta de verificación: " . $con->error);
        }

        $queryDatosPago = "INSERT INTO datos_pago (banco, rut_cta, digito_verificador, numero_cuenta) VALUES (?, ?, ?, ?)";
        $stmtDatosPago = $con->prepare($queryDatosPago);
        if (!$stmtDatosPago) {
            die("Error al preparar la consulta de inserción de banco: " . $con->error);
        }

        $query_s = "SELECT id FROM sucursales WHERE nombre = ?";
        $stmt_s = $con->prepare($query_s);
        if (!$stmt_s) {
            die("Error al preparar la consulta de sucursales: " . $con->error);
        }

        $query_m = "SELECT id FROM motivos_gestion WHERE motivo = ?";
        $stmt_m = $con->prepare($query_m);
        if (!$stmt_m) {
            die("Error al preparar la consulta de motivos: " . $con->error);
        }

        $query = "INSERT INTO turnos_extra (sucursal_id, fecha_turno, horas_cubiertas, monto, nombre_colaborador, 
                                            rut, datos_bancarios_id, motivo_turno_id, autorizado_por, persona_motivo, 
                                            contratado, nacionalidad, hora_inicio, hora_termino) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);

        $checkTurnos = "SELECT id
                        FROM turnos_extra
                        WHERE (sucursal_id IS NULL OR sucursal_id  = ? )
                        AND fecha_turno = ?
                        AND horas_cubiertas = ? 
                        AND monto = ? 
                        AND nombre_colaborador = ? 
                        AND rut = ? 
                        AND motivo_turno_id = ? 
                        AND (persona_motivo IS NULL OR persona_motivo = ?)
                        AND contratado = ? 
                        AND nacionalidad = ?
                        AND hora_inicio = ?
                        AND hora_termino = ?";
        $stmtTurnos = $con->prepare($checkTurnos);

        if (!$stmtTurnos) {
            die("Error al preparar la consulta de verificación: " . $con->error);
        }
        if (!$stmt) {
            die("Error al preparar la consulta de inserción de turnos: " . $con->error);
        }

        $totalRegistros = 0; 
        $registrosInsertados = 0;
        $errores = [
            'fechasInvalidas' => [],
            'turnosDuplicados' => [],
            'bancosInvalidos' => [],
            'motivosInvalidos' => [],
            'instalacionesInvalidas' => []
        ];
        foreach ($data as $index => $row) {
            if ($index < 2) continue; // Saltar las dos primeras filas
            $nErrores = 0;
            /*
            echo "<pre>";
            print_r($row);
            echo "</pre>";
            */
            //validar si ya existen los datos

            // Datos bancarios
            $banco = $row[14] ?? null;
            $rutNum = $row[15] ?? null;
            if ($rutNum !== null) {
                $rutNum = preg_replace('/[^0-9]/', '', $rutNum);
            }
            //echo $rutNum;
            $dv = $row[16] ?? null;
            $numCta = $row[17] ?? null;
            if ($numCta !== null) {
                $numCta = preg_replace('/[^0-9]/', '', $numCta);
            }
            //datos instalacion y turno
            $instalacion = $row[2] ?? null;
            $fecha_turno = $row[5];
            //Obtener hora inicio y termino 
            $hora_inicio = $row[6];
            $hora_termino = $row[7];

            // columnas vacías, saltar la fila
            if (is_empty($banco) || is_empty($rutNum) || is_empty($dv) || is_empty($numCta) || is_empty($fecha_turno) || is_empty($hora_inicio) || is_empty($hora_termino) ) {
                continue;
            }
            $totalRegistros++;
 


            $hora_inicio_obj = DateTime::createFromFormat('H:i', $hora_inicio);
            $hora_termino_obj = DateTime::createFromFormat('H:i', $hora_termino);

            $hora_inicio_str = $hora_inicio_obj->format('H:i:s');
            $hora_termino_str = $hora_termino_obj->format('H:i:s');
            // Convertir fecha a Y-m-d 
            
            $fechaTurnoFormateada = procesarFechaTurno($fecha_turno, $index, $errores);
    
            if (empty($fechaTurnoFormateada)) {
                $nErrores++;
                continue;
            }
            if($_SESSION['id'] != 38){
                // validar fecha turno (SOLO DIA ACTUAL HASTA LAS 12:00 DEL DIA SIGUIENTE)
                $horaActual = (int)date('H');
                $horaMinuto = date('H:i');
                $fechaHoy = date('Y-m-d');
                $fechaAyer = date('Y-m-d', strtotime('-1 day'));
                /*
                echo 'FECHATURNOFORMATEADA'.$fechaTurnoFormateada;
                echo 'hora'.$horaActual;
                echo 'fecha hoy'.$fechaHoy;
                echo 'fecha ayet'.$fechaAyer;
                */

                if ($horaActual < 12) {
                    if ($fechaTurnoFormateada != $fechaAyer && $fechaTurnoFormateada != $fechaHoy) {
                        $fechasInvalidas = $fechaTurnoFormateada;
                        $errores['fechasInvalidas'][] = "Fila $index: La fecha/hora de carga: '$fechasInvalidas $horaMinuto' Estan fuera de lo permitido";
                        $nErrores++;
                    }
                } else {
                    if ($fechaTurnoFormateada != $fechaHoy) {
                        $fechasInvalidas = $fechaTurnoFormateada;
                        $errores['fechasInvalidas'][] = "Fila $index: La fecha/hora de carga: '$fechasInvalidas $horaMinuto' Estan fuera de lo permitido";
                        $nErrores++;
                    }
                }
            };
            $fecha = $fechaTurnoFormateada;  
            $horas = $row[8];

            // Limpiar el monto
            $monto = floatval(str_replace(['$', ',', '.'], '', $row[9]));
            $rut = $row[10] . '-' . $row[11];
            $colaborador = ucwords(strtolower($row[12]));
            $nacionalidad = ucwords(strtolower($row[13]));
            $motivo = $row[18];
            $persona_motivo = $row[19] ? ucwords(strtolower($row[19])) : null;
            $contratado = ($row[20] == "SI") ? 1 : 0;
            $autorizado = $_SESSION['id'];

            // Obtener instalación
            if (empty($instalacion) || strtolower(trim($instalacion)) === 'spot') {
                $instalacion_id = null;
            } else {
                $instalacion_normalizada = preg_replace('/[^\w]/', '', strtolower($instalacion));

                $query = "SELECT id FROM sucursales 
                            WHERE REGEXP_REPLACE(LOWER(nombre), '[^a-z0-9_]', '') = ?";
                $stmt_s = $con->prepare($query);
                $stmt_s->bind_param("s", $instalacion_normalizada);
                $stmt_s->execute();
                $stmt_s->store_result();
                $stmt_s->bind_result($instalacion_id);
                $stmt_s->fetch();

                if (!$stmt_s->num_rows) {
                    $errores['instalacionesInvalidas'][] = "Fila $index: Instalación '$instalacion' no existe";
                    $nErrores++;
                }

                $stmt_s->free_result();
            }
            // Obtener Motivo
            $stmt_m->bind_param("s", $motivo);
            $stmt_m->execute();
            $stmt_m->store_result();
            $stmt_m->bind_result($motivo_id);
            $stmt_m->fetch();
            if (!$stmt_m->num_rows) $motivo_id = null;
            $stmt_m->free_result();

            if($motivo_id == null) {
                $errores['motivosInvalidos'][] = "Fila $index: $colaborador - Motivo '$motivo' no existe";
                $nErrores++;
            }
            /*
            echo   'INSTALACION ID '.$instalacion_id;
            echo   '<br>fecha turno'.$fecha;
            echo   '<br>horas '.$horas;
            echo   '<br>monto '.$monto;
            echo   '<br>colaborador '.$colaborador;
            echo   '<br>rut '.$rut;
            echo   '<br>motivo '.$motivo_id;
            echo   '<br>persona motivo '.$persona_motivo;
            echo   '<br>autorizadopor '.$autorizado;
            echo   '<br>contratado '.$contratado;
            echo   '<br>nacionalidad '.$nacionalidad;
            */
            //Verificar si existe los datos 
            $stmtTurnos->bind_param("isiissisisss",$instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $motivo_id, $persona_motivo, $contratado, $nacionalidad, $hora_inicio, $hora_termino);
            $stmtTurnos->execute();
            $stmtTurnos->store_result();    
            if ($stmtTurnos->num_rows > 0) {
                $errores['turnosDuplicados'][] = "Fila $index: $colaborador (RUT: $rut)";
                $nErrores++;
            }
            $stmtTurnos->free_result();
        
           // Verificar si los datos bancarios ya existen
            $stmtCheck->bind_param("siss", $banco, $rutNum, $dv, $numCta);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows > 0) {
                // Recuperar el ID existente
                $stmtCheck->bind_result($existingId, $nombreBanco); 
                $stmtCheck->fetch();
                $bancoId = $existingId; 
                $stmtCheck->free_result();
            }else {
                // Obtener ID del banco
                $stmtBanco->bind_param("s", $banco);
                $stmtBanco->execute();
                $stmtBanco->bind_result($idBanco);
                $stmtBanco->fetch();
                $stmtBanco->free_result();

                if (!$idBanco) {
                    $errores['bancosInvalidos'][] = "Fila $index: Banco '$banco' no existe";
                    continue;
                }

                try {
                    $stmtDatosPago->bind_param("iiss", $idBanco, $rutNum, $dv, $numCta);
                    if ($stmtDatosPago->execute()) {
                        $bancoId = $stmtDatosPago->insert_id;
                    } else {
                        if ($con->errno == 1062) {
                            $queryGetExisting = "SELECT id FROM datos_pago WHERE rut_cta = ? AND numero_cuenta = ?";
                            $stmtGet = $con->prepare($queryGetExisting);
                            $stmtGet->bind_param("ss", $rutNum, $numCta);
                            $stmtGet->execute();
                            $stmtGet->bind_result($bancoId);
                            $stmtGet->fetch();
                            $stmtGet->close();
                        } else {
                            throw new Exception("Error bancario: " . $stmtDatosPago->error);
                        }
                    }
                } catch (mysqli_sql_exception $e) {
                    if ($e->getCode() == 1062) {
                        $queryGetExisting = "SELECT id FROM datos_pago WHERE rut_cta = ? AND numero_cuenta = ? LIMIT 1";
                        $stmtGet = $con->prepare($queryGetExisting);
                        $stmtGet->bind_param("ss", $rutNum, $numCta);
                        $stmtGet->execute();
                        $stmtGet->bind_result($bancoId);
                        $stmtGet->fetch();
                        $stmtGet->close();
                    } else {
                        throw $e;
                    }
                }
            }
    
            if($nErrores > 0){
                continue;
            }else{
                $stmt->bind_param("isiissiiisisss", $instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $bancoId, $motivo_id, 
                                $autorizado, $persona_motivo, $contratado, $nacionalidad, $hora_inicio_str, $hora_termino_str);
                if (!$stmt->execute()) {
                    die("ERROR AL INSERTAR". $stmt->error);
                }
                $registrosInsertados++;
            }
            
        }
        //mensajes para sweetalert
        $mensajeExito = "<b>$registrosInsertados de $totalRegistros turnos procesados correctamente.</b>";
        
        $mensajeErrores = "";
        $detallesErrores = "";

        if (!empty($errores['fechasInvalidas'])) {
            $count = count($errores['fechasInvalidas']);
            $mensajeErrores .= "<br><b>Fechas inválidas:</b> $count";
            $detallesErrores .= "<b>Fechas inválidas:</b><ul>";
            foreach($errores['fechasInvalidas'] as $error) {
                $detallesErrores .= "<li>$error</li>";
            }
            $detallesErrores .= "</ul>";
        }

        if (!empty($errores['turnosDuplicados'])) {
            $count = count($errores['turnosDuplicados']);
            $mensajeErrores .= "<br><b>Turnos Duplicados:</b> $count";
            $detallesErrores .= "<b>Turnos Duplicados:</b><ul>";
            foreach($errores['turnosDuplicados'] as $error) {
                $detallesErrores .= "<li>$error</li>";
            }
            $detallesErrores .= "</ul>";
        }
        if (!empty($errores['bancosInvalidos'])) {
            $count = count($errores['bancosInvalidos']);
            $mensajeErrores .= "<br><b>Bancos no registrados:</b> $count";
            $detallesErrores .= "<b>Bancos no registrados:</b><ul>";
            foreach($errores['bancosInvalidos'] as $error) {
                $detallesErrores .= "<li>$error</li>";
            }
            $detallesErrores .= "</ul>";
        }
        if (!empty($errores['motivosInvalidos'])) {
            $count = count($errores['motivosInvalidos']);
            $mensajeErrores .= "<br><b>Motivos no existentes:</b> $count";
            $detallesErrores .= "<b>Motivos no existentes:</b><ul>";
            foreach($errores['motivosInvalidos'] as $error) {
                $detallesErrores .= "<li>$error</li>";
            }
            $detallesErrores .= "</ul>";
        }

        if (!empty($errores['instalacionesInvalidas'])) {
            $count = count($errores['instalacionesInvalidas']);
            $mensajeErrores .= "<br><b>Instalaciones no existentes:</b> $count";
            $detallesErrores .= "<b>Instalaciones no existentes:</b><ul>";
            foreach($errores['instalacionesInvalidas'] as $error) {
                $detallesErrores .= "<li>$error</li>";
            }
            $detallesErrores .= "</ul>";
        }
        
        // Determinar el tipo de alerta
        $alertType = ($registrosInsertados == $totalRegistros) ? 'success' : 'warning';
        if ($registrosInsertados == 0) {
            $alertType = 'error';
        }
        
        // Mostrar SweetAlert
        $_SESSION['swal'] = [
            'title' => 'Resultado de la carga',
            'html' => $mensajeExito . $mensajeErrores,
            'icon' => $alertType,
            'showCancelButton' => !empty($detallesErrores),
            'confirmButtonText' => 'Aceptar',
            'cancelButtonText' => 'Ver detalles',
            'footer' => '<a href="nuevo-turno.php">Volver al formulario</a>',
            'details' => $detallesErrores
        ];

    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Error del sistema',
            'message' => "Ocurrió un error inesperado",
            'footer' => 'Contacte al administrador'
        ];
    }
}

// Función para limpiar y validar fechas
function procesarFechaTurno($fecha_str, $index, &$errores) {
    $ch = false;
    if (preg_match('/(\d{4})年(\d{1,2})月(\d{1,2})日/', $fecha_str, $matches)){
        $ch = true; 
    }

    $fecha_str = preg_replace('/[^\x20-\x7E]/u', '', $fecha_str);
    $fecha_str = str_replace(['　', '⁄', '／', '\\', '|'], '/', $fecha_str);
    $fecha_str = trim($fecha_str);
    $mes_actual = (int)date('m');
    $anio_actual = (int)date('Y');


    try {


        if (empty($fecha_str)) {
            throw new Exception("La fecha está vacía");
        }

        // Patrón japonés (si es necesario)
        if ($ch) {
            $a = (int)$matches[3];
            $b = (int)$matches[2];
            $anio = (int)$matches[1];

            if($a === $mes_actual){
                $mes = $a;
                $dia = $b;
            }else{
                $mes = $b;
                $dia = $a;
            }

            if (!checkdate($mes, $dia, $anio)) {
                throw new Exception("Fecha inválida en formato especial");
            }

            return sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
        }

        if (!preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $fecha_str, $matches)) {
            throw new Exception("Formato debe ser DD/MM/AAAA");
        }

        $a = (int)$matches[1];
        $b = (int)$matches[2];
        $anio = (int)$matches[3];

        if($a === $mes_actual){
            $mes = $a;
            $dia = $b;
        }else{
            $mes = $b;
            $dia = $a;
        }

        if ($mes !== $mes_actual || $anio !== $anio_actual) {
            throw new Exception("La fecha debe ser del mes actual");
        }
        
        if (!checkdate($mes, $dia, $anio)) {
            throw new Exception("Fecha inválida");
        }
        
        return sprintf('%04d-%02d-%02d', $anio, $mes, $dia);

    } catch (Exception $e) {
        $errores['fechasInvalidas'][] = "Fila $index: '{$fecha_str}' - " . $e->getMessage();
        return false;
    }
}

?>