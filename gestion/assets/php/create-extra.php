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
    $colaborador = $_POST['nombre_colaborador'];
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

//Carga masiva
use PhpOffice\PhpSpreadsheet\IOFactory;
if (isset($_POST['carga'])) {
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Preparar queries
        $queryCheck = "SELECT id FROM datos_pago WHERE banco = ? AND rut_cta = ? AND digito_verificador = ? AND numero_cuenta = ?";
        $stmtCheck = $con->prepare($queryCheck);

        $queryBanco = "INSERT INTO datos_pago (banco, rut_cta, digito_verificador, numero_cuenta) VALUES (?, ?, ?, ?)";
        $stmtBanco = $con->prepare($queryBanco);

        $query_s = "SELECT id FROM sucursales WHERE nombre = ?";
        $stmt_s = $con->prepare($query_s);

        $query_m = "SELECT id FROM motivos_gestion WHERE motivo = ?";
        $stmt_m = $con->prepare($query_m);

        $query = "INSERT INTO turnos_extra (sucursal_id, fecha_turno, horas_cubiertas, monto, nombre_colaborador, rut, datos_bancarios_id, motivo_turno_id, autorizado_por) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);

        foreach ($data as $index => $row) {
            if ($index < 2) continue; // Saltar las dos primeras filas

            $instalacion = $row[2];
            $fecha_turno = $row[5];

            // Convertir fecha al formato correcto
            $fecha_obj = DateTime::createFromFormat('j/n/Y', $fecha_turno);
            $fecha = $fecha_obj ? $fecha_obj->format('Y-m-d') : null;
            $horas = $row[7];
            // Limpiar el monto
            $monto = floatval(str_replace(['$', ','], '', $row[8]));
            $rut = $row[9] . '-' . $row[10];
            $colaborador = $row[11];
            $motivo = $row[16];
            $autorizado = $_SESSION['id'];
            // Datos bancarios
            $banco = $row[12] ?? null;
            $rutNum = $row[13] ?? null;
            $dv = $row[14] ?? null;
            $numCta = $row[15] ?? null;
            // Si alguna de estas columnas esenciales está vacía, saltar la fila
            if (empty(trim($banco)) || empty(trim($rutNum)) || empty(trim($dv)) || empty(trim($numCta))) {
                continue;
            }
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

            // Insertar en turnos_extra
            $stmt->bind_param("isiissiii", $instalacion_id, $fecha, $horas, $monto, $colaborador, $rut, $bancoID, $motivo_id, $autorizado);
            $stmt->execute();
        }

        echo "<script>alert('Turnos insertados correctamente'); location.href='nuevo-turno.php';</script>";
    } else {
        echo "Error al subir el archivo.";
    }
}



?>