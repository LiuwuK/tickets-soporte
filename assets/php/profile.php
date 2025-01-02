<?php
    if (isset($_POST['update'])) {
        $name = $_POST['name'];
        $aemail = $_POST['alt_email'];
        $mobile = $_POST['phone'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $a = mysqli_query($con, "update user set name='$name',mobile='$mobile',gender='$gender',alt_email='$aemail',address='$address' where email='" . $_SESSION['login'] . "'");
        if ($a) {
            echo "<script>alert('Tu perfil ha sido actualizado correctamente');location.replace(document.referrer)</script>";
        }
    }    
?>