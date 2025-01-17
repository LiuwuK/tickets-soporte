<?php
    include("dbconnection.php");
    $id = $_GET['id'];
    $query = "SELECT * 
              FROM user 
              WHERE id = $id";
    $rt = mysqli_query($con, $query);

  //obtener ciudades
  $query = "SELECT * FROM cargos";
  $cargos = mysqli_query($con, $query);

    if (isset($_POST['update'])) {
      $name = $_POST['name'];
      $altemail = $_POST['alt_email'];
      $contact = $_POST['mobile'];
      $address = $_POST['address'];
      $userid = $_GET['id'];
      $cargo = $_POST['cargo'];
      $status = '0';
      if (isset($_POST['status'])) {
        $status = 1;
    }
      $ret = mysqli_query($con, "update user set name='$name', alt_email='$altemail',mobile='$contact',address='$address',status='$status',cargo='$cargo' where id='$userid'");
      if ($ret) {
        echo "<script>alert('Datos Actualizados'); location.replace(document.referrer)</script>";
      }
    }
?>