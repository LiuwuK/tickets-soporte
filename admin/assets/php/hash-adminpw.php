<?php 
  include("../../../checklogin.php");
  include('../../../dbconnection.php');
   die(); 
    $sql = mysqli_query($con, "SELECT id, password FROM user WHERE id = 53");
    while ($row = mysqli_fetch_assoc($sql)) {
        $id = $row['id'];
        $plainPassword = $row['password'];

        // Generar hash de la contraseña
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        mysqli_query($con, "UPDATE user SET password='$hashedPassword' WHERE id='$id'");
    }
  echo "Migración completada.";
?>  