<?php
error_reporting(0);
include("dbconnection.php");

if (isset($_POST['login'])) {
    $username = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; 
    
    if ($role == 'admin') {
        // Validación para admin
        $ret = mysqli_query($con, "SELECT * FROM admin WHERE name='$username' AND password='$password'");
        $num = mysqli_fetch_array($ret);
        if ($num) {
            $_SESSION['alogin'] = $username;
            $_SESSION['id'] = $num['id'];
            $_SESSION['admin_id'] = $num['id'];
            echo "<script>window.location.href='admin/home.php'</script>";
            exit();
        } else {
            $_SESSION['action1'] = "*Usuario o Contraseña Inválidos";
            echo "<script>window.location.href='login.php'</script>";
            exit();
        }
    } else if ($role == 'user') {
        // Validación para usuarios normales
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $num = $result->fetch_array();
        
        if ($num && password_verify($password, $num['password'])) {
            $_SESSION['login'] = $username;
            $_SESSION['user_id'] = $num['id'];
            $_SESSION['name'] = $num['name'];
            $_SESSION['id'] = $num['id'];
            echo "<script>window.location.href='dashboard.php'</script>";
            exit();
        } else {
            $_SESSION['action1'] = "Usuario o Contraseña Inválidos";
            echo "<script>window.location.href='login.php'</script>";
            exit();
        }
    } else {
        // Si el rol no es ni admin ni user
        $_SESSION['action1'] = "Selección de rol inválida";
        echo "<script>window.location.href='index.php'</script>";
        exit();
    }
}
?>
