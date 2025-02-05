<?php
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

$query = "SELECT * FROM roles";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$rolData = $supervisorData->get_result();
while ($row = mysqli_fetch_assoc($rolData)) {
    $rol[] = $row; 
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
    $rolS = $_POST['rol'];

    $query  = "INSERT INTO sucursales(nombre, direccion_calle, comuna, ciudad_id, departamento_id, supervisor_id, rol_id)
                VALUES (?,?,?,?,?,?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssiiii",$name,$direccion, $comuna, $city, $dept, $supervisor, $rolS);
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
    $roles = $_POST['rol'];
    $estados = $_POST['estado'];

    foreach ($ids as $index => $id) {
        $nombre = $names[$index];
        $ciudad = $ciudades[$index];
        $calle = $calles[$index];
        $comuna = $comunas[$index];
        $supervisor = $supervisores[$index];
        $dept = $depts[$index];
        $rol = $roles[$index];
        $estado = $estados[$index];

        $query = "UPDATE sucursales 
                    SET nombre = ?,
                        direccion_calle = ?,
                        comuna = ?, 
                        ciudad_id = ?,
                        departamento_id = ?,
                        supervisor_id = ?, 
                        rol_id = ?,
                        estado = ?
                    WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssiiiisi", $nombre,$calle, $comuna, $ciudad, $dept, $supervisot, $rol, $estado, $id);
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
?>