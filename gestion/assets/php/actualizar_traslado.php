<?php
require '../../../dbconnection.php';

if (isset($_POST['id']) && isset($_POST['estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    $sql = "UPDATE traslados SET estado = ? WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $estado, $id);
    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "Error al actualizar";
    }
}
?>
