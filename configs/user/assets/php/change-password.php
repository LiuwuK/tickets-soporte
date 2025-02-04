<?php
    if (isset($_POST['change'])) {
        // Obtener la contraseña actual del usuario
        $stmt = $con->prepare("SELECT password FROM user WHERE email = ?");
        $stmt->bind_param("s", $_SESSION['login']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        //se valida que la contraseña ingresada como actual sea igual a la almacenada
        if ($user && password_verify($_POST['oldpass'], $user['password'])) {
            // Validar que la nueva contraseña y la confirmación coincidan
            if ($_POST['newpass'] === $_POST['confirmpassword']) {
                $hashed_password = password_hash($_POST['newpass'], PASSWORD_DEFAULT);
                $update_stmt = $con->prepare("UPDATE user SET password = ? WHERE email = ?");
                $update_stmt->bind_param("ss", $hashed_password, $_SESSION['login']);
                if ($update_stmt->execute()) {
                    $_SESSION['msg1'] = "¡Contraseña cambiada correctamente!";
                } else {
                    $_SESSION['msg1'] = "Error al actualizar la contraseña. Inténtalo de nuevo.";
                }
            } else {
                $_SESSION['msg1'] = "La nueva contraseña y la confirmación no coinciden.";
            }
        } else {
            $_SESSION['msg1'] = "La contraseña actual no es correcta.";
        }
    }
?>