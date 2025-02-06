<?php
//obtener info 
$query = "SELECT * FROM motivos_gestion";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();


//nuevo motivo
if(isset($_POST['newSup'])){ 
    $name = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $query  = "INSERT INTO motivos_gestion(motivo, tipo_motivo)
                VALUES (?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss",$name, $tipo);
    if ($stmt->execute()) {
        echo "<script>alert('Motivo registrado correctamente'); location.href='motivos.php';</script>";
    } else {
        echo "<script>alert('Error en la consulta: ".$stmt->error."');</script>";
    }
}
//actualizar
if(isset($_POST['btnUpdt'])){
    $ids = $_POST['id'];
    $name = $_POST['name'];
    $tipos = $_POST['tipo'];

    foreach ($ids as $index => $id) {
        $nombre = $name[$index];
        $tipo = $tipos[$index];
        $query = "UPDATE motivos_gestion 
                    SET motivo = ?,
                    tipo_motivo = ?
                    WHERE id = ? 
                    AND (motivo <> ? OR tipo_motivo <> ? )";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssiss", $nombre,$tipo, $id, $nombre, $tipo);
        $stmt->execute();
    }
    echo "<script>alert('Motivos actualizados correctamente.'); location.href='motivos.php';</script>";

}
//eliminar 
if(isset($_POST['delSup'])){
    $id = $_POST['idSup'];
    $query = "DELETE FROM motivos_gestion WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('motivo eliminado correctamente.'); location.href='motivos.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}
?>