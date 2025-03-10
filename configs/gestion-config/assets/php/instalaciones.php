<?php
require __DIR__.'/../../../../vendor/autoload.php';
//obtener info 
$query = "SELECT * FROM sucursales";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();

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
        echo "<script>alert('sucursal eliminada correctamente.'); location.href='instalaciones.php';</script>";
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
        $query_main = "INSERT INTO sucursales(nombre, direccion_calle, comuna, ciudad_id, departamento_id, supervisor_id, estado)
                VALUES (?,?,?,?,?,?,?)";
        
        $stmt = $con->prepare($query_main);
    
        foreach ($data as $index => $row) {
            if ($index == 0) continue;
            $nombre = $row[0];
            $ciudad = strtolower($row[1]);
            $comuna = $row[2];
            $calle =  $row[3];
            $supervisor  = $row[4];
            $depto = $row[5];
            $estado = strtolower($row[6]);

            //Obtener departamento
            $query_d = "SELECT id FROM departamentos WHERE depto_id = ?";
            $stmt_d = $con->prepare($query_d);
            $stmt_d->bind_param("s", $depto); 
            $stmt_d->execute();
            $stmt_d->bind_result($depto_id);
            $stmt_d->fetch();
            $stmt_d->close();
            //obtener supervisor 
            $query_s = "SELECT id FROM supervisores WHERE rut = ?";
            $stmt_s = $con->prepare($query_s);
            $stmt_s->bind_param("s", $supervisor); 
            $stmt_s->execute();
            $stmt_s->bind_result($supervisor_id);
            $stmt_s->fetch();
            $stmt_s->close();
            //obtener ciudad
            $query_c = "SELECT id FROM ciudades WHERE LOWER(nombre_ciudad) = ?";
            $stmt_c = $con->prepare($query_c);
            $stmt_c->bind_param("s", $ciudad); 
            $stmt_c->execute();
            $stmt_c->bind_result($ciudad_id);
            $stmt_c->fetch();
            $stmt_c->close();


            $stmt->bind_param("sssiiis", $nombre,$calle, $comuna, $ciudad_id, $depto_id, $supervisor_id,$estado);
            $stmt->execute();
        }
    
        echo "<script>alert('Sucursales registrados correctamente'); location.href='instalaciones.php';</script>";
    } else {
        echo "Error al subir el archivo.";
    }
}
?>