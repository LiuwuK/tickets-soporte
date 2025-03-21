<?php
require '../../vendor/autoload.php';
$query = "SELECT * FROM sucursales";
$sucursalesData = $con->prepare($query);
$sucursalesData->execute();
$sucursalData = $sucursalesData->get_result();
while ($row = mysqli_fetch_assoc($sucursalData)) {
    $inst[] = $row; 
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
    $stmtCheck->bind_param("ssss", $banco, $rutNum, $dv, $numCta);
    $stmtCheck->execute();
    $stmtCheck->bind_result($bancoID);
    $stmtCheck->fetch();
    $stmtCheck->close();
    if (!$bancoID) {
        //Insertar datos bancarios
        $queryInsert = "INSERT INTO datos_pago (banco, rut_cta, digito_verificador, numero_cuenta) VALUES (?, ?, ?, ?)";
        $stmtInsert = $con->prepare($queryInsert);
        $stmtInsert->bind_param("ssss", $banco, $rutNum, $dv, $numCta);
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

    $query = "INSERT INTO turnos_extra (sucursal_id, fecha_turno, horas_cubiertas, monto, nombre_colaborador, rut, datos_bancarios_id,
                                        motivo_turno_id, autorizado_por)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("isiissiii", $instalacion, $fecha_turno, $horas, $monto, $colaborador, $rut, $bancoID, $motivo, $autorizado);
    $stmt->execute();
    $bancoID = $stmt->insert_id;
    $stmt->close();

    echo "<script>alert('Turno Agregado Correctamente.'); location.href='nuevo-turno.php';</script>";
}

use PhpOffice\PhpSpreadsheet\IOFactory;
function is_empty($value) {
    return $value === null || trim($value) === '';
}
// Habilitar reporting de errores
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

        // Preparar queries
        $queryCheck = "SELECT id FROM datos_pago WHERE banco = ? AND rut_cta = ? AND digito_verificador = ? AND numero_cuenta = ?";
        $stmtCheck = $con->prepare($queryCheck);

        if (!$stmtCheck) {
            die("Error al preparar la consulta de verificación: " . $con->error);
        }

        $queryBanco = "INSERT INTO datos_pago (banco, rut_cta, digito_verificador, numero_cuenta) VALUES (?, ?, ?, ?)";
        $stmtBanco = $con->prepare($queryBanco);

        if (!$stmtBanco) {
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
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            die("Error al preparar la consulta de inserción de turnos: " . $con->error);
        }

        foreach ($data as $index => $row) {
            if ($index < 2) continue; // Saltar las dos primeras filas
            echo "<pre>";
            print_r($row);
            echo "</pre>";

            // Datos bancarios
            $banco = $row[13] ?? null;
            $rutNum = $row[14] ?? null;
            $dv = $row[15] ?? null;
            $numCta = $row[16] ?? null;
            // columnas vacías, saltar la fila
            if (is_empty($banco) || is_empty($rutNum) || is_empty($dv) || is_empty($numCta)) {
                continue;
            }
            //datos instalacion
            $instalacion = $row[2];
            $fecha_turno = $row[5];        
            // Convertir fecha al formato correcto
            $fecha_obj = DateTime::createFromFormat('j/n/Y', $fecha_turno);
            $fecha = $fecha_obj ? $fecha_obj->format('Y-m-d') : null;
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

            // Verificar si los datos bancarios ya existen
            $stmtCheck->bind_param("ssss", $banco, $rutNum, $dv, $numCta);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            $stmtCheck->bind_result($bancoID);
            $stmtCheck->fetch();

            if (!$stmtCheck->num_rows) {
                // Insertar datos bancarios si no existen
                $stmtBanco->bind_param("ssss", $banco, $rutNum, $dv, $numCta);
                $stmtBanco->execute();
                $bancoID = $stmtBanco->insert_id;
            }
            $stmtCheck->free_result(); 

            echo "<pre>";
            var_dump($instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $bancoID, $motivo_id, $autorizado, $persona_motivo, $contratado, $nacionalidad);
            echo "</pre>";

            // Insertar en turnos_extra
            $stmt->bind_param("isiissiiisis", $instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $bancoID, $motivo_id, $autorizado, $persona_motivo, $contratado, $nacionalidad);
            if (!$stmt->execute()) {
                die("Error al ejecutar la consulta de inserción de turnos: " . $stmt->error);
            }
        }
        echo "<script>alert('Turnos insertados correctamente'); location.href='nuevo-turno.php';</script>";
    } else {
        echo "Error al subir el archivo.";
    }
}



?>