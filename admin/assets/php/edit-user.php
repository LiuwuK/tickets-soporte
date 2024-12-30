<?php
    include("dbconnection.php");
    
    if (isset($_POST['update'])) {
      $name = $_POST['name'];
      $altemail = $_POST['alt_email'];
      $contact = $_POST['mobile'];
      $address = $_POST['address'];
      $gender = $_POST['gender'];
      $userid = $_GET['id'];
      $ret = mysqli_query($con, "update user set name='$name', alt_email='$altemail',mobile='$contact',gender='$gender',address='$address' where id='$userid'");
      if ($ret) {
        echo "<script>alert('Datos Actualizados'); location.replace(document.referrer)</script>";
      }
    }
?>