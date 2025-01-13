<?php
error_reporting(0);
include("dbconnection.php");

if (isset($_POST['login'])) {
    $username = $_POST['email'];    
    $password = $_POST['password'];

    $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $num = $result->fetch_array();
    
    $role = $num['rol'];

    if ($role == 'admin') {
        // Validación para admin
        if ($num && password_verify($password, $num['password'])) {
            $_SESSION['alogin'] = $username;
            $_SESSION['id'] = $num['id'];
            $_SESSION['name'] = $num['name'];
            $_SESSION['admin_id'] = $num['id'];
            $_SESSION['role'] = $num['rol']; 
            $_SESSION['cargo'] = $num['cargo'];
            echo "<script>window.location.href='admin/home.php'</script>";
            exit(); 
        } else {
            $_SESSION['action1'] = "*Usuario o Contraseña Inválidos";
            echo "<script>window.location.href='login.php'</script>";
            exit();
        }
    } else {
        // Validación para usuarios normales
        if ($num['status']){        
            if ($num && password_verify($password, $num['password'])) {
                $_SESSION['login'] = $username;
                $_SESSION['user_id'] = $num['id'];
                $_SESSION['name'] = $num['name'];
                $_SESSION['id'] = $num['id'];
                $_SESSION['role'] = $num['rol']; 
                $_SESSION['cargo'] = $num['cargo']; 
                echo "<script>window.location.href='dashboard.php'</script>";
                exit();
            } else {
                $_SESSION['action1'] = "Usuario o Contraseña Inválidos";
                echo "<script>window.location.href='login.php'</script>";
                exit();

            }
        }else{
            $_SESSION['action1'] = "La cuenta no existe o no esta activa ";
            echo "<script>window.location.href='login.php'</script>";
            exit();        
        }   
    }
} else if (isset($_POST['registro'])) { 
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $mobile = $_POST['phone'];
    $gender = $_POST['gender'];

    // Validar si el correo ya está registrado
    $stmt = $con->prepare("SELECT email FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Correo electrónico ya registrado con nosotros. Intente con una identificación de correo electrónico diferente.');</script>";
        echo "<script>window.location.href='login.php'</script>";
    } else {
        // Hashear la contraseña antes de insertarla
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO user (name, email, password, mobile, gender) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $mobile, $gender);
        $stmt->execute();

        echo "<script>alert('Tu cuenta ha sido creada correctamente, esta sera activada por un administrador lo antes posible');</script>";
        echo "<script>window.location.href='login.php'</script>";
    }
    $stmt->close();
    $con->close();
}
?>
