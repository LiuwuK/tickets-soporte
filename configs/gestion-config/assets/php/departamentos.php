<?php
//obtener info 
$query = "SELECT * FROM departamentos";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();


//nuevo rol
if(isset($_POST['newSup'])){ 
    $name = $_POST['nombre'];
    $query  = "INSERT INTO departamentos(nombre_departamento)
                VALUES (?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s",$name);
    if ($stmt->execute()) {
        echo "<script>alert('Departamento registrado correctamente'); location.href='departamentos.php';</script>";
    } else {
        echo "<script>alert('Error en la consulta: ".$stmt->error."');</script>";
    }
}
//actualizar
if(isset($_POST['btnUpdt'])){
    $ids = $_POST['id'];
    $name = $_POST['name'];

    foreach ($ids as $index => $id) {
        $nombre = $name[$index];
        $query = "UPDATE departamentos 
                    SET nombre_departamento = ?
                    WHERE id = ? 
                    AND (nombre_departamento <> ? )";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sis", $nombre, $id, $nombre);
        $stmt->execute();
    }
    echo "<script>alert('departamentos actualizados correctamente.'); location.href='departamentos.php';</script>";

}
//eliminar 
if(isset($_POST['delSup'])){
    $id = $_POST['idSup'];
    $query = "DELETE FROM departamentos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('departamento eliminado correctamente.'); location.href='departamentos.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}
?>