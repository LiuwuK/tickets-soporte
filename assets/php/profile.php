<?php
    $userId = $_SESSION["user_id"];
    $query = "SELECT us.*, ca.nombre AS cargoUser 
                FROM user us 
                JOIN cargos ca ON(us.cargo = ca.id) 
                WHERE us.id = $userId";
    $usr = mysqli_query($con, $query);
    $rt =  mysqli_num_rows($usr);
    if (isset($_POST['update'])) {
        $name = $_POST['name'];
        $aemail = $_POST['alt_email'];
        $mobile = $_POST['phone'];
        $address = $_POST['address'];
        $a = mysqli_query($con, "update user set name='$name',mobile='$mobile',alt_email='$aemail',address='$address' where email='" . $_SESSION['login'] . "'");
        if ($a) {
            echo "<script>alert('Tu perfil ha sido actualizado correctamente');location.replace(document.referrer)</script>";
        }
    }    
?>