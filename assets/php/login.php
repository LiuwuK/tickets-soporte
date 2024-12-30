<?php
    include("dbconnection.php");

    if (!isset($_SESSION['action1'])) {
      $_SESSION['action1'] = '';  // O algún otro valor por defecto que desees
    }
    
    if (isset($_POST['login'])) {
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $num = $result->fetch_array();
    
        if ($num > 0 && password_verify($_POST['password'], $num['password'])) {  
            $_SESSION['login'] = $_POST['email'];
            $_SESSION['id'] = $num['id'];
            $_SESSION['user_id'] = $num['id'];
            $_SESSION['name'] = $num['name'];
    
            // Redirigir a la página del dashboard
            $extra = "dashboard.php";
            echo "<script>window.location.href='" . $extra . "'</script>";
            exit();
        } else {
            // Si el usuario o la contraseña son incorrectos
            $_SESSION['action1'] = "Usuario o Contraseña Inválida";
            $extra = "login.php";
            echo "<script>window.location.href='" . $extra . "'</script>";
            exit();
        }
    }
?>