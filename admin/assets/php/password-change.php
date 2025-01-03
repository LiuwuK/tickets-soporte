<?php
    if (isset($_POST['change'])) {
        $oldpas = $_POST['oldpass'];
        $adminid = $_SESSION['id'];
        $newpassword = $_POST['newpass'];
        
        $sql = mysqli_query($con, "SELECT `password` FROM `admin` WHERE id='$adminid'");
        $num = mysqli_fetch_array($sql);
      
        if ($num && password_verify($oldpas, $num['password'])) {
            // hasheo contraseña nueva
            $hashedPassword = password_hash($newpassword, PASSWORD_BCRYPT);
      
            $update = mysqli_query($con, "UPDATE `admin` SET `password`='$hashedPassword' WHERE id='$adminid'");
            if ($update) {
              
                echo '<script>alert("La contraseña ha sido actualizada correctamente."); location.replace(document.referrer)</script>';
            } else {
                echo '<script>alert("Error al actualizar la contraseña. Inténtelo nuevamente.");</script>';
            }
        } else {
            $_SESSION['msg1'] = "La contraseña anterior no coincide.";
        }
      }
?>