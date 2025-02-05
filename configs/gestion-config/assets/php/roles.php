<?php
//obtener info 
$query = "SELECT * FROM roles";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();


//nuevo rol
if(isset($_POST['newSup'])){ 
    $name = $_POST['nombre'];
    $desc = $_POST['description'];

    $query  = "INSERT INTO roles(nombre_rol, descripcion)
                VALUES (?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss",$name, $desc);
    if ($stmt->execute()) {
        echo "<script>alert('Rol registrado correctamente'); location.href='roles.php';</script>";
    } else {
        echo "<script>alert('Error en la consulta: ".$stmt->error."');</script>";
    }
}
//actualizar
if(isset($_POST['btnUpdt'])){
    $ids = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    foreach ($ids as $index => $id) {
        $nombre = $name[$index];
        $desc = $description[$index];

        $query = "UPDATE roles 
                    SET nombre_rol = ?, 
                        descripcion = ?
                    WHERE id = ? 
                    AND (nombre_rol <> ? OR descripcion <> ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssiss", $nombre, $desc, $id, $nombre, $desc);
        $stmt->execute();
    }
    echo "<script>alert('Roles actualizados correctamente.'); location.href='roles.php';</script>";

}
//eliminar 
if(isset($_POST['delSup'])){
    $id = $_POST['idSup'];
    $query = "DELETE FROM roles WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Rol eliminado correctamente.'); location.href='roles.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}
?>