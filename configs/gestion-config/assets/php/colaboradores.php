<?php
require __DIR__.'/../../../../vendor/autoload.php';
// Funcion para caclular antiguedad
function calcularAntiguedad($fechaIngreso) {
    try {
        if (empty($fechaIngreso)) {
            throw new Exception("Fecha vacía");
        }
        
        $entryDate = new DateTime($fechaIngreso);
        $currentDate = new DateTime();
        $interval = $currentDate->diff($entryDate);
        
        return $interval->format('%y años, %m meses');
    } catch (Exception $e) {
        return "Error en fecha: " . substr($fechaIngreso, 0, 10);
    }
}

//obtener info 
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
//página actual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 15; // Número de registros por página
$offset = ($page - 1) * $perPage;

$query = "SELECT * FROM colaboradores";
//agregar el filtro
if (!empty($search)) {
    $query .= " WHERE name LIKE ?";
    $searchTerm = "%$search%"; // Búsqueda parcial
}
// Agregar paginación
$query .= " LIMIT ? OFFSET ?";
// Preparar y ejecutar la consulta
$colaboradorData = $con->prepare($query);
if (!empty($search)) {
    $colaboradorData->bind_param('sii', $searchTerm, $perPage, $offset);
} else {
    $colaboradorData->bind_param('ii', $perPage, $offset);
}
$colaboradorData->execute();
$result = $colaboradorData->get_result();

// Construir la consulta para contar el total de registros
$totalQuery = "SELECT COUNT(*) as total FROM colaboradores";

// Si hay un valor de búsqueda, agregar el filtro
if (!empty($search)) {
    $totalQuery .= " WHERE name LIKE ?";
}

// Preparar y ejecutar la consulta para contar
$totalResult = $con->prepare($totalQuery);

if (!empty($search)) {
    $totalResult->bind_param('s', $searchTerm);
}

$totalResult->execute();
$totalRow = $totalResult->get_result()->fetch_assoc();
$totalRecords = $totalRow['total'];
// Calcular el número total de páginas
$totalPages = ceil($totalRecords / $perPage);


//obtener sucursales
$query = "SELECT * FROM sucursales";
$sucursalData = $con->prepare($query);
$sucursalData->execute();
$suData = $sucursalData->get_result();
while ($row = mysqli_fetch_assoc($suData)) {
    $su[] = $row; 
}

//Funcion formatear rut
function formatRut($rut){
    if (strpos($rut, '.' !== false)){
        return $rut;
    }
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    $dv = substr($rut, -1);
    $cuerpo = substr($rut, 0, -1);

    $fRut = number_format($cuerpo, 0, '', '.');
    $format = $fRut.'-'.$dv;
    
    return $format;
}

//nuevo colaborador
if(isset($_POST['newColab'])){ 

}
//actualizar
if(isset($_POST['btnUpdt'])){
    $ids = $_POST['id'];
    $ruts = $_POST['rut'];
    $names = $_POST['name'];
    $fnames = $_POST['fname'];
    $mnames = $_POST['mname'];
    $rsocials = $_POST['rsocial'];
    $nacionality = $_POST['nacionality'];
    $entry_dates = $_POST['entry_date'];
    $phones = $_POST['phone'];
    $emails = $_POST['email'];
    $ctypes = $_POST['ctype'];
    $deptos = $_POST['depto'];
    $estados = $_POST['estado'];
    
    foreach ($ids as $index => $id) {
        $rut = $ruts[$index];
        $name = $names[$index];
        $fname = $fnames[$index];
        $mname =  $mnames[$index];
        $rsocial = $rsocials[$index];
        $nct = $nacionality[$index];
        $entry_date = $entry_dates[$index];
        $phone = $phones[$index];
        $email = $emails[$index];
        $ctype = $ctypes[$index];
        $depto = $deptos[$index];
        $estado = $estados[$index];
        $query = "UPDATE colaboradores 
                    SET rut = ?, 
                        name = ?, 
                        fname = ?, 
                        mname = ?, 
                        rsocial = ?, 
                        nacionality = ?, 
                        entry_date = ?, 
                        phone = ?, 
                        email = ?, 
                        contract_type = ?, 
                        facility = ?, 
                        vigente = ?
                    WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssssssissisi",$rut, $name, $fname, $mname, $rsocial, $nct, $entry_date, $phone, $email, $ctype, $depto, $estado, $id);
        $stmt->execute();
       
    }
    echo "<script>alert('colaboradores actualizados correctamente.'); location.href='colaboradores.php';</script>";

}
//eliminar
if(isset($_POST['delColab'])){
    $id = $_POST['idColab'];
    $query = "DELETE FROM colaboradores WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Colaborador eliminado correctamente.'); location.href='colaboradores.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar al colaborador');</script>";
    }
    $stmt->close();
}

use PhpOffice\PhpSpreadsheet\IOFactory;
if(isset($_POST['carga'])) {
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Consultas
        $query_insert = "INSERT INTO `colaboradores` 
                      (`rut`, `name`, `fname`, `mname`, `rsocial`, `birth_date`, 
                       `nacionality`, `gender`, `role`, `entry_date`, `phone`, 
                       `email`, `contract_type`, `leaving_reason`, `facility`, `vigente`) 
                      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $query_update = "UPDATE `colaboradores` SET 
                          `fname` = ?, `mname` = ?, `rsocial` = ?, `birth_date` = ?, 
                          `nacionality` = ?, `gender` = ?, `role` = ?, `entry_date` = ?, 
                          `phone` = ?, `email` = ?, `contract_type` = ?, `leaving_reason` = ?, 
                          `facility` = ?, `vigente` = ? 
                          WHERE rut = ? AND name = ?";

        $query_check = "SELECT id FROM colaboradores WHERE rut = ? AND name = ?";
        $query_sucursal = "
            SELECT id, nombre FROM sucursales
            WHERE REPLACE(REPLACE(REPLACE(LOWER(nombre), ' ', ''), '.', ''), ',', '') = ?
            LIMIT 1
        ";

        $stmt_insert = $con->prepare($query_insert);
        $stmt_update = $con->prepare($query_update);
        $stmt_check = $con->prepare($query_check);
        $stmt_sucursal = $con->prepare($query_sucursal);

        $con->autocommit(false);

        try {
            $registros_procesados = 0;
            $actualizados = 0;
            $errores = [];

            
            foreach ($data as $index => $row) {
                if ($index == 0) continue;
                
                if(empty($row[0]) || empty($row[2])) {
                    $errores[] = "Fila $index: Datos básicos faltantes";
                    continue;
                }
                $inst = $row[63];
                $inst = strtolower($inst);
                $inst = preg_replace('/[\s\.,]+/', '', $inst);

                $stmt_sucursal->bind_param("s", $inst);
                $stmt_sucursal->execute();
                $result = $stmt_sucursal->get_result();
                $sucursal = $result->fetch_assoc();

                if(!$sucursal) {
                    $errores[] = "Fila $index: Sucursal no encontrada - ".$row[63];
                    continue;
                }
                $bday = DateTime::createFromFormat('d-m-Y', $row[6])->format('Y-m-d');
                $entrydate = DateTime::createFromFormat('d-m-Y', $row[14])->format('Y-m-d');
                $vigente = ($row[68] == 'Si') ? 1 : 0;
                $email = !empty($row[36]) ? $row[36] : null;
                $phone = !empty($row[33]) ? $row[33] : null;
                $lReason = !empty($row[52]) ? $row[52] : null;

                $stmt_check->bind_param("ss", $row[0], $row[2]); // rut, name
                $stmt_check->execute();
                $stmt_check->store_result();

                if($stmt_check->num_rows > 0) {
                    // Actualizar
                    $stmt_update->bind_param(
                        "sssssssssssissss",
                        $row[3],  // fname
                        $row[4],  // mname
                        $row[5],  // rsocial
                        $bday,
                        $row[7],  // nacionality
                        $row[8],  // gender
                        $row[11], // role
                        $entrydate,
                        $phone,
                        $email,
                        $row[50], // contract_type
                        $lReason,
                        $sucursal['id'],
                        $vigente,
                        $row[0],  // rut
                        $row[2]   // name
                    );

                    if(!$stmt_update->execute()) {
                        $errores[] = "Fila $index: Error al actualizar - " . $stmt_update->error;
                    } else {
                        $actualizados++;
                    }

                } else {
                    // Insertar
                    $stmt_insert->bind_param(
                        "ssssssssssisssii",
                        $row[0],  // rut
                        $row[2],  // name
                        $row[3],  // fname
                        $row[4],  // mname
                        $row[5],  // rsocial
                        $bday,
                        $row[7],  // nacionality
                        $row[8],  // gender
                        $row[11], // role
                        $entrydate,
                        $phone,
                        $email,
                        $row[50], // contract_type
                        $lReason,
                        $sucursal['id'],
                        $vigente
                    );

                    if(!$stmt_insert->execute()) {
                        $errores[] = "Fila $index: Error al insertar - " . $stmt_insert->error;
                    } else {
                        $registros_procesados++;
                    }
                }
            }

            $con->commit();
            $mensaje = "$registros_procesados colaboradores insertados.\n$actualizados colaboradores actualizados.";
            if(!empty($errores)) {
                $mensaje .= "\nErrores: " . count($errores);
                file_put_contents('errores_carga.log', implode("\n", $errores), FILE_APPEND);
            }

            $detallesErrores = implode("\n", $errores); 

            $_SESSION['swal'] = [
                'title' => 'Resultado de la carga',
                'html' => nl2br(htmlspecialchars($mensaje)), 
                'icon' => (!empty($errores) ? 'warning' : 'success'),
                'confirmButtonText' => 'Aceptar',
                'showCancelButton' => true,
                'cancelButtonText' => 'Ver detalles',
                'footer' => '<a href="colaboradores.php">Volver</a>',
                'details' => nl2br(htmlspecialchars($detallesErrores))
            ];

            header("Location: colaboradores.php");
            exit;
        } catch (Exception $e) {
            $con->rollback();
            $_SESSION['swal'] = [
                'title' => 'Resultado de la carga',
                'html' => nl2br(htmlspecialchars(string: $mensaje)),
                'icon' => (!empty($errores) ? 'warning' : 'success'),
                'confirmButtonText' => 'Aceptar',
                'footer' => '<a href="colaboradores.php">Volver</a>'
            ];
        }

        $con->autocommit(true);
        $stmt_insert->close();
        $stmt_update->close();
        $stmt_check->close();
        $stmt_sucursal->close();
    } else {
        echo "<script>alert('Error al subir el archivo: ".$_FILES['file']['error']."');</script>";
    }
}

?>