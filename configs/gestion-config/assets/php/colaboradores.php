<?php
require __DIR__.'/../../../../vendor/autoload.php';
// Funcion para caclular antiguedad
function calcularAntiguedad($fechaIngreso) {
    $entryDate = new DateTime($fechaIngreso);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($entryDate);
    
    return $interval->format('%y años, %m meses');
}

//obtener info 
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
//página actual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20; // Número de registros por página
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
    $name = $_POST['nombre'];
    $city = $_POST['ciudad'];
    $direccion = $_POST['direccion'];
    $comuna = $_POST['comuna'];
    $supervisor = $_POST['supervisor'];
    $dept = $_POST['departamento'];


    $query  = "INSERT INTO sucursales(nombre, direccion_calle, comuna, ciudad_id, departamento_id, supervisor_id)
                VALUES (?,?,?,?,?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssiii",$name,$direccion, $comuna, $city, $dept, $supervisor);
    if ($stmt->execute()) {
        echo "<script>alert('Sucursal registrada correctamente'); location.href='instalaciones.php';</script>";
    } else {
        echo "<script>alert('Error en la consulta: ".$stmt->error."');</script>";
    }
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
        $fnames = $fnames[$index];
        $mnames =  $mnames[$index];
        $rsocial = $rsocial[$index];
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
    echo "<script>alert('colaboradores actualizados correctamente.'); location.href='instalaciones.php';</script>";

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
        // Aumentar límites temporales para archivos grandes
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();

        // Preparar consultas
        $query_main = "INSERT INTO `colaboradores` 
                      (`rut`, `name`, `fname`, `mname`, `rsocial`, `birth_date`, 
                       `nacionality`, `gender`, `role`, `entry_date`, `phone`, 
                       `email`, `contract_type`, `leaving_reason`, `facility`, `vigente`) 
                      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        
        $query_sucursal = "SELECT id FROM sucursales WHERE nombre = ? LIMIT 1";
        
        $stmt = $con->prepare($query_main);
        $stmt_sucursal = $con->prepare($query_sucursal);
        /*
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        */
        // Desactivar autocommit para transacción
        $con->autocommit(false);
        
        try {
            $registros_procesados = 0;
            $errores = [];
            
            foreach ($data as $index => $row) {
                if ($index == 0) continue; // Saltar encabezado

                // Validar datos básicos
                if(empty($row[0]) || empty($row[2]) ) {
                    $errores[] = "Fila $index: Datos básicos faltantes";
                    continue;
                }

                // Obtener ID de sucursal
                $stmt_sucursal->bind_param("s", $row[60]);
                $stmt_sucursal->execute();
                $result = $stmt_sucursal->get_result();
                $sucursal = $result->fetch_assoc();
                
                if(!$sucursal) {
                    $errores[] = "Fila $index: Sucursal no encontrada - ".$row[60];
                    continue;
                }
                
                // Formatear fechas
                $bday = DateTime::createFromFormat('d-m-Y', $row[6])->format('Y-m-d');
                $entrydate = DateTime::createFromFormat('d-m-Y', $row[14])->format('Y-m-d');
                
                // Bind parameters
                $vigente = ($row[65] == 'Si') ? 1 : 0;
                $email = !empty($row[33]) ? $row[33] : null;
                $phone = !empty($row[30]) ? $row[30] : null;
                $lReason = !empty($row[49]) ? $row[49] : null;
                
                $stmt->bind_param(
                    "ssssssssssisssii",
                    $row[0],    // rut
                    $row[2],    // name
                    $row[3],    // fname
                    $row[4],    // mname
                    $row[5],    // rsocial
                    $bday,      // birth_date
                    $row[7],    // nacionality
                    $row[8],    // gender
                    $row[11],   // role
                    $entrydate, // entry_date
                    $phone,     // phone
                    $email,   // email
                    $row[47],   // contract_type
                    $lReason,   // leaving_reason
                    $sucursal['id'], // facility
                    $vigente    // vigente
                );
                
                if(!$stmt->execute()) {
                    $errores[] = "Fila $index: Error al insertar - " . $stmt->error;
                } else {
                    $registros_procesados++;
                }
            }
            
            // Commit si todo está bien
            $con->commit();
            // Mostrar resultados
            $mensaje = "$registros_procesados colaboradores registrados.";
            if(!empty($errores)) {
                $mensaje .= "\nErrores: " . count($errores);
                // Guardar errores en log
                file_put_contents('errores_carga.log', implode("\n", $errores), FILE_APPEND);
                echo "<script>alert('".addslashes($mensaje)."'); location.href='colaboradores.php';</script>";
            }
            echo "<script>alert('".addslashes($mensaje)."'); location.href='colaboradores.php';</script>";
        } catch (Exception $e) {
            $con->rollback();
            echo "<script>alert('Error en la transacción: ".addslashes($e->getMessage())."');</script>";
        }
        
        // Restaurar configuración
        $con->autocommit(true);
        $stmt->close();
        $stmt_sucursal->close();
         echo "<script>alert('".addslashes($mensaje)."'); location.href='colaboradores.php';</script>";
    } else {
        echo "<script>alert('Error al subir el archivo: ".$_FILES['file']['error']."');</script>";
    }
}
?>