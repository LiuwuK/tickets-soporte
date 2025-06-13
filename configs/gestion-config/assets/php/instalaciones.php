<?php
require __DIR__.'/../../../../vendor/autoload.php';
//obtener info 
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
//página actual
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Número de registros por página
$offset = ($page - 1) * $perPage;

$query = "SELECT * FROM sucursales";
//agregar el filtro
if (!empty($search)) {
    $query .= " WHERE nombre LIKE ?";
    $searchTerm = "%$search%"; // Búsqueda parcial
}
// Agregar paginación
$query .= " LIMIT ? OFFSET ?";
// Preparar y ejecutar la consulta
$supervisorData = $con->prepare($query);
if (!empty($search)) {
    $supervisorData->bind_param('sii', $searchTerm, $perPage, $offset);
} else {
    $supervisorData->bind_param('ii', $perPage, $offset);
}
$supervisorData->execute();
$result = $supervisorData->get_result();

// Construir la consulta para contar el total de registros
$totalQuery = "SELECT COUNT(*) as total FROM sucursales";

// Si hay un valor de búsqueda, agregar el filtro
if (!empty($search)) {
    $totalQuery .= " WHERE nombre LIKE ?";
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


//obtener supervisores/departamentos/roles/ciudades
$query = "SELECT * FROM supervisores";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$supData = $supervisorData->get_result();
while ($row = mysqli_fetch_assoc($supData)) {
    $sup[] = $row; 
}

$query = "SELECT * FROM departamentos";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$deptoData = $supervisorData->get_result();
while ($row = mysqli_fetch_assoc($deptoData)) {
    $depto[] = $row; 
}

$query = "SELECT * FROM ciudades";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$cityData = $supervisorData->get_result();
while ($row = mysqli_fetch_assoc($cityData)) {
    $city[] = $row; 
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

//nueva sucursal
if(isset($_POST['newSup'])){ 
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
    $names = $_POST['name'];
    $ciudades = $_POST['ciudad'];
    $calles = $_POST['calle'];
    $comunas = $_POST['comuna'];
    $supervisores = $_POST['supervisor'];
    $depts = $_POST['depto'];
    $estados = $_POST['estado'];


    foreach ($ids as $index => $id) {
        $nombre = $names[$index];
        $ciudad = $ciudades[$index];
        $calle = $calles[$index];
        $comuna = $comunas[$index];
        $supervisor = $supervisores[$index];
        $dept = $depts[$index];
        $estado = $estados[$index];
        $query = "UPDATE sucursales 
                    SET nombre = ?,
                        direccion_calle = ?,
                        comuna = ?, 
                        ciudad_id = ?,
                        departamento_id = ?,
                        supervisor_id = ?, 
                        estado = ?
                    WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssiiisi", $nombre,$calle, $comuna, $ciudad, $dept, $supervisor,$estado, $id);
        $stmt->execute();
    }
    echo "<script>alert('sucursales actualizados correctamente.'); location.href='instalaciones.php';</script>";

}
//eliminar
if(isset($_POST['delSup'])){
    $id = $_POST['idSup'];
    $query = "DELETE FROM sucursales WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Sucursal eliminada correctamente'); location.href='instalaciones.php';</script>";

    } else {
        echo "<script>alert('Error al eliminar la sucursal');</script>";
    }
    $stmt->close();
}

use PhpOffice\PhpSpreadsheet\IOFactory;
if(isset($_POST['carga'])){
     if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        // Preparamos ambas consultas (INSERT y UPDATE)
        $query_insert = "INSERT INTO sucursales(nombre, direccion_calle, comuna, ciudad_id, departamento_id, supervisor_id, estado)
                VALUES (?,?,?,?,?,?,?)";
        
        $query_update = "UPDATE sucursales SET 
                        direccion_calle = ?, 
                        comuna = ?, 
                        ciudad_id = ?, 
                        departamento_id = ?, 
                        supervisor_id = ?, 
                        estado = ?
                        WHERE nombre = ?";
        
        $stmt_insert = $con->prepare($query_insert);
        $stmt_update = $con->prepare($query_update);
        
        $successCount = 0;
        $updateCount = 0;
        $errorCount = 0;
        $errorsDetails = []; // Para almacenar detalles de errores
        
        foreach ($data as $index => $row) {
            if ($index == 0) continue; // Saltar encabezados
            
            // Normalizar nombre
            $nombre = preg_replace('/\s+/', ' ', trim($row[0]));
            $nombre = str_replace([' .', '. '], '.', $nombre);
            
            $ciudad = strtolower($row[3]);
            $comuna = $row[2];
            $calle = $row[5];
            $supervisor = $row[4];
            $depto = $row[6];
            $estado = strtolower($row[7]);

            // Obtener departamento
            $query_d = "SELECT id FROM departamentos WHERE depto_id = ?";
            $stmt_d = $con->prepare($query_d);
            $stmt_d->bind_param("s", $depto); 
            $stmt_d->execute();
            $stmt_d->bind_result($depto_id);
            $stmt_d->fetch();
            $stmt_d->close();
            
            $supervisor = formatRut($supervisor);
            
            // Obtener supervisor 
            $query_s = "SELECT id FROM supervisores WHERE rut = ?";
            $stmt_s = $con->prepare($query_s);
            $stmt_s->bind_param("s", $supervisor); 
            $stmt_s->execute();
            $stmt_s->bind_result($supervisor_id);
            $stmt_s->fetch();
            $stmt_s->close();
            
            // Obtener ciudad
            $query_c = "SELECT id FROM ciudades WHERE LOWER(nombre_ciudad) = ?";
            $stmt_c = $con->prepare($query_c);
            $stmt_c->bind_param("s", $ciudad); 
            $stmt_c->execute();
            $stmt_c->bind_result($ciudad_id);
            $stmt_c->fetch();
            $stmt_c->close();
            // Verificar si la sucursal ya existe con comparación flexible
            $query_check = "SELECT id, nombre FROM sucursales WHERE 
                           REPLACE(REPLACE(nombre, ' ', ''), '.', '') = 
                           REPLACE(REPLACE(?, ' ', ''), '.', '')";
            
            $stmt_check = $con->prepare($query_check);
            $originalForComparison = str_replace([' ', '.'], '', $nombre);
            $stmt_check->bind_param("s", $originalForComparison);
            $stmt_check->execute();
            $stmt_check->store_result();
            $stmt_check->bind_result($existing_id, $existing_name);
            $stmt_check->fetch();
            
            if ($stmt_check->num_rows > 0) {
                // Registrar discrepancias en los nombres
                if ($existing_name !== $nombre) {
                    $errorsDetails[] = "Nombre normalizado: '$nombre' difiere del existente: '$existing_name'";
                    $nombre = $existing_name; // Usar el nombre exacto que está en la base de datos
                }
                
                // Proceder con el update usando el nombre exacto
                $stmt_update->bind_param("ssiiiss", $calle, $comuna, $ciudad_id, $depto_id, $supervisor_id, $estado, $existing_name);
            
                if ($stmt_update->execute()) {
                    $updateCount++;
                } else {
                    $errorCount++;
                    $errorsDetails[] = "Error actualizando $nombre: " . $stmt_update->error;
                }
            } else {
                // Sucursal no existe - insertar
                $stmt_insert->bind_param("sssiiis", $nombre, $calle, $comuna, $ciudad_id, $depto_id, $supervisor_id, $estado);
                if ($stmt_insert->execute()) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errorsDetails[] = "Error insertando $nombre: " . $stmt_insert->error;
                }
            }
            $stmt_check->close();
        }
        
        // Preparar el mensaje para SweetAlert
        $swalTitle = "Proceso completado";
        $swalHtml = "<div style='text-align: left;'>";
        $swalHtml .= "<p><b>Sucursales nuevas:</b> $successCount</p>";
        $swalHtml .= "<p><b>Sucursales actualizadas:</b> $updateCount</p>";
        $swalHtml .= "<p><b>Errores:</b> $errorCount</p>";
        
        if ($errorCount > 0) {
            $swalHtml .= "<details><summary>Detalles de errores</summary><ul>";
            foreach ($errorsDetails as $error) {
                $swalHtml .= "<li>$error</li>";
            }
            $swalHtml .= "</ul></details>";
        }
        $swalHtml .= "</div>";
        
        // Determinar el tipo de alerta según resultados
        $swalType = ($errorCount > 0) ? 'warning' : 'success';
        
        // JavaScript para mostrar SweetAlert
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '$swalTitle',
                    html: `$swalHtml`,
                    icon: '$swalType',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    window.location.href = 'instalaciones.php';
                });
            });
        </script>";
        
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al subir el archivo',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    window.location.href = 'instalaciones.php';
                });
            });
        </script>";
    }
}
?>