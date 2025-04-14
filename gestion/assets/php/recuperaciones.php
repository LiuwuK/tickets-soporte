<?php
$query = "SELECT *
    FROM sucursales"; 
$sucursalesData = $con->prepare($query);
$sucursalesData->execute();
$sucursalData = $sucursalesData->get_result();
while ($row = mysqli_fetch_assoc($sucursalData)) {
$inst[] = $row; 
}

//Insertar 
if(isset($_POST['send'])){
    $sucursalId = $_POST['instalacion'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $userid = $_SESSION['id'];

    $query = "INSERT INTO recuperaciones (sucursal_id, fecha, monto, usuario_id) 
                    VALUES (?, ?, ?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("isii", $sucursalId, $fecha, $monto, $userid);
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta de inserción de turnos: " . $stmt->error);
    }
    echo "<script>alert('Recuperacion registrada correctamente'); location.href='recuperaciones.php';</script>";
    $stmt->close();
}
//actualizar
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $monto = $_POST['monto'];
    $query = "UPDATE recuperaciones 
                SET fecha = ?,
                monto = ?
                WHERE id = ? 
                AND (fecha <> ? OR monto <> ? )";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssiss", $fecha,$monto, $id, $fecha, $monto);
    $stmt->execute();

    echo "<script>alert('Recuperacion actualizada correctamente.'); location.href='recuperaciones.php';</script>";

}
//eliminar 
if(isset($_POST['del'])){
    $id = $_POST['id'];
    $query = "DELETE FROM recuperaciones WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Recuperacion eliminada correctamente.'); location.href='recuperaciones.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar.');</script>";
    }
    $stmt->close();
}
?>