<?php
    if (isset($_POST['updt-dist'])) {
        $ids = $_POST['id'];
        $montos = $_POST['monto'];
        $montosRestantes = $_POST['monto_restante'];
    
        foreach ($ids as $index => $id) {
            $monto = $montos[$index];
            $montoRestante = $montosRestantes[$index];
    
            $query = "UPDATE distribuidores 
                        SET monto = ?, 
                            monto_restante = ? 
                        WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("iii", $monto, $montoRestante, $id);
            $stmt->execute();
        }
    
        echo "<script>alert('Distribuidores actualizados correctamente.'); location.href='manage-dist.php';</script>";
    
    }
?>