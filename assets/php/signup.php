<?php
    include("dbconnection.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            echo "<script>window.location.href='registration.php'</script>";
        } else {
            // Hashear la contraseña antes de insertarla
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("INSERT INTO user (name, email, password, mobile, gender) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $mobile, $gender);
            $stmt->execute();
    
            echo "<script>alert('Tu cuenta ha sido creada correctamente');</script>";
            echo "<script>window.location.href='login.php'</script>";
        }
        $stmt->close();
        $con->close();
    }
?>