<?php 
    $sql = mysqli_query($con, "SELECT id, password FROM admin");
    while ($row = mysqli_fetch_assoc($sql)) {
        $id = $row['id'];
        $plainPassword = $row['password'];

        // Generar hash de la contraseña
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        mysqli_query($con, "UPDATE admin SET password='$hashedPassword' WHERE id='$id'");
    }
  echo "Migración completada.";
?>