<?php
require '../../vendor/autoload.php';
date_default_timezone_set('America/Santiago'); 
$query = "SELECT * FROM sucursales";
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
if(isset($_POST['newExtra'])){
    //Insertar datos bancarios
    $banco = $_POST['banco'];
    $rutBanco = $_POST['rut'];
    $partes = explode('-', $rutBanco);
    $rutNum = $partes[0];
    $dv = end($partes); 
    $numCta = $_POST['numCta'];

    // Verificar si ya existe
    $queryCheck = "SELECT id FROM datos_pago WHERE banco = ? AND rut_cta = ? AND digito_verificador = ? AND numero_cuenta = ?";
    $stmtCheck = $con->prepare($queryCheck);
    $stmtCheck->bind_param("iisi", $banco, $rutNum, $dv, $numCta);
    $stmtCheck->execute();
    $stmtCheck->bind_result($bancoID);
    $stmtCheck->fetch();
    $stmtCheck->close();
    if (!$bancoID) {
        //Insertar datos bancarios
        $queryInsert = "INSERT INTO datos_pago (banco, rut_cta, digito_verificador, numero_cuenta) VALUES (?, ?, ?, ?)";
        $stmtInsert = $con->prepare($queryInsert);
        $stmtInsert->bind_param("iisi", $banco, $rutNum, $dv, $numCta);
        $stmtInsert->execute();
        $bancoID = $stmtInsert->insert_id; // Obtener el ID recién insertado
        $stmtInsert->close();
    }
    //Insertar Turno
    $instalacion = $_POST['instalacion'];
    $fecha_turno = $_POST['fecha_turno'];
    $horas = $_POST['horas_cubiertas'];
    $monto = $_POST['monto'];
    $colaborador = ucwords(strtolower($_POST['nombre_colaborador']));
    $rut = $_POST['rutCta'];
    $motivo = $_POST['motivo_turno'];
    $autorizado = $_SESSION['id'];
    $persona_motivo = $_POST['persona_motivo'];
    $contratado = $_POST['contratado'];
    $nacionalidad = $_POST['nacionalidad'];

    $query = "INSERT INTO turnos_extra (sucursal_id, fecha_turno, horas_cubiertas, monto, nombre_colaborador, rut, datos_bancarios_id,
                                        motivo_turno_id, autorizado_por, persona_motivo, contratado, nacionalidad)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("isiissiiisis", $instalacion, $fecha_turno, $horas, $monto, $colaborador, $rut, $bancoID, $motivo, 
                        $autorizado, $persona_motivo, $contratado, $nacionalidad);
    $stmt->execute();
    $bancoID = $stmt->insert_id;
    $stmt->close();

    echo "<script>alert('Turno Agregado Correctamente.'); location.href='nuevo-turno.php';</script>";
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
                                            rut, datos_bancarios_id, motivo_turno_id, autorizado_por, persona_motivo, contratado, nacionalidad) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);

        $checkTurnos = "SELECT dp.id
                        FROM turnos_extra
                        WHERE sucursal_id = ? 
                        AND fecha_turno = ? 
                        AND horas_cubiertas = ? 
                        AND monto = ? 
                        AND nombre_colaborador = ? 
                        AND rut = ? 
                        AND motivo_turno_id = ? 
                        AND autorizado_por = ? 
                        AND persona_motivo = ? 
                        AND contratado = ? 
                        AND nacionalidad = ?";
        $stmtTurnos = $con->prepare($checkTurnos);

        if (!$stmtTurnos) {
            die("Error al preparar la consulta de verificación: " . $con->error);
        }


        if (!$stmt) {
            die("Error al preparar la consulta de inserción de turnos: " . $con->error);
        }
        //info fechas no validas
        $count = 0;
        $turnos = 0;
        $fechasInvalidas = [];
        $colaboradorTurno = [];
        foreach ($data as $index => $row) {
            if ($index < 2) continue; // Saltar las dos primeras filas
            /*
            echo "<pre>";
            print_r($row);
            echo "</pre>";
            */
            //validar si ya existen los datos


            // Datos bancarios
            $banco = $row[13] ?? null;
            $rutNum = $row[14] ?? null;
            if ($rutNum !== null) {
                $rutNum = preg_replace('/[^0-9]/', '', $rutNum);
            }
            $dv = $row[15] ?? null;
            $numCta = $row[16] ?? null;
            if ($numCta !== null) {
                $numCta = preg_replace('/[^0-9]/', '', $numCta);
            }
            // columnas vacías, saltar la fila
            if (is_empty($banco) || is_empty($rutNum) || is_empty($dv) || is_empty($numCta)) {
                continue;
            }
            //datos instalacion
            $instalacion = $row[2];
            $fecha_turno = $row[5];  // Asumiendo que la fecha está en la columna 5

            // Convertir fecha a Y-m-d 
            $fecha_obj = DateTime::createFromFormat('m/d/Y', $fecha_turno) ?: 
                        DateTime::createFromFormat('Y-m-d', $fecha_turno) ?: 
                        new DateTime($fecha_turno);

            if (!$fecha_obj) {
                continue; 
            }

            $fechaTurnoFormateada = $fecha_obj->format('Y-m-d');
            // validar fecha turno (SOLO DIA ACTUAL HASTA LAS 10:00 DEL DIA SIGUIENTE)
            $horaActual = (int)date('H');
            $fechaHoy = date('Y-m-d');
            $fechaAyer = date('Y-m-d', strtotime('-1 day'));
            
            /*
            if ($horaActual < 10) {
                if ($fechaTurnoFormateada != $fechaAyer && $fechaTurnoFormateada != $fechaHoy) {
                    $count = $count + 1;
                    $fechasInvalidas[] = $fechaTurnoFormateada;
                    continue;
                }
            } else {
                if ($fechaTurnoFormateada != $fechaHoy) {
                    $count = $count + 1;
                    $fechasInvalidas[] = $fechaTurnoFormateada;
                    continue;
                }
            }
            */
            $fecha = $fechaTurnoFormateada;  
            $horas = $row[7];

            // Limpiar el monto
            $monto = floatval(str_replace(['$', ','], '', $row[8]));
            $rut = $row[9] . '-' . $row[10];
            $colaborador = ucwords(strtolower($row[11]));
            $nacionalidad = ucwords(strtolower($row[12]));
            $motivo = $row[17];
            $persona_motivo = ucwords(strtolower($row[18]));
            $contratado = ($row[19] == "SI") ? 1 : 0;
            $autorizado = $_SESSION['id'];

            
            // Obtener instalación
            $stmt_s->bind_param("s", $instalacion);
            $stmt_s->execute();
            $stmt_s->store_result();
            $stmt_s->bind_result($instalacion_id);
            $stmt_s->fetch();
            if (!$stmt_s->num_rows) $instalacion_id = null;
            $stmt_s->free_result(); 

            // Obtener Motivo
            $stmt_m->bind_param("s", $motivo);
            $stmt_m->execute();
            $stmt_m->store_result();
            $stmt_m->bind_result($motivo_id);
            $stmt_m->fetch();
            if (!$stmt_m->num_rows) $motivo_id = null;
            $stmt_m->free_result();
            
            if($motivo_id == null){
                echo "<script>alert('Error al Insertar el turno de ".$colaborador.", El motivo ".$motivo." no existe en el sistema');</script>";
                continue; 
            } 

            //Verificar si existe los datos 
            $stmtTurnos->bind_param("isiissiisis", $instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $motivo_id, $autorizado, $persona_motivo, $contratado, $nacionalidad);
            $stmtTurnos->execute();
            $stmtTurnos->fetch();
            if ($stmtCheck->num_rows) {
                $turnos = $turnos + 1;
                $colaboradorTurno[] = $colaborador;
                continue;
            }
            // Verificar si los datos bancarios ya existen
            $stmtCheck->bind_param("sisi", $banco, $rutNum, $dv, $numCta);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            $stmtCheck->bind_result($bancoId, $bancoNombre);
            $stmtCheck->fetch();
            if (!$stmtCheck->num_rows) {
                // Insertar datos bancarios si no existen
                //obtengo el id del banco
                $stmtBanco->bind_param("s", $banco);
                $stmtBanco->execute();
                $stmtBanco->bind_result($idBanco);
                $stmtBanco->fetch();
                $stmtBanco->free_result();
                
                if (!$idBanco) {
                    // Banco no encontrado, manejar error
                    continue;
                }
                $stmtDatosPago->bind_param("iisi", $idBanco, $rutNum, $dv, $numCta);
                $stmtDatosPago->execute();
                $bancoId = $stmtDatosPago->insert_id;
            }
            $stmtCheck->free_result(); 
            /*Ver info por pantalla
            echo "<pre>";
            var_dump($instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $bancoId, $motivo_id, $autorizado, $persona_motivo, $contratado, $nacionalidad);
            echo "</pre>";
            */
            // Insertar en turnos_extra
            $stmt->bind_param("isiissiiisis", $instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $bancoId, $motivo_id, $autorizado, $persona_motivo, $contratado, $nacionalidad);
            if (!$stmt->execute()) {
                die("Error al ejecutar la consulta de inserción de turnos: " . $stmt->error);
            }
        }
       if($count > 0 ){
        foreach($fechasInvalidas AS $fechas){
            echo "El turno con Fecha ".$fechas." no corresponde al dia de hoy <br>";    
        }
        echo "<script>alert('Error al Insertar turnos, Algunos no corresponden al dia de hoy'); location.href='nuevo-turno.php';</script>";
       }else if ($turnos > 0){
        foreach($colaboradorTurno AS $colaboradores){
            echo "El turno con Fecha ".$colaboradores." no corresponde al dia de hoy <br>";    
        }
        echo "<script>alert('Error al Insertar turnos, Algunos turnos estan duplicados'); location.href='nuevo-turno.php';</script>";
       }else{
        echo "<script>alert('Turnos insertados correctamente'); location.href='nuevo-turno.php';</script>";
       }
    } else {
        echo "Error al subir el archivo.";
    }
}

?>