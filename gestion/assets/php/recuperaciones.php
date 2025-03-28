<?php
$query = "SELECT *
    FROM sucursales
    WHERE departamento_id = 25"; 
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
?>