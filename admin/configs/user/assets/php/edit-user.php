<?php
    $id = $_GET['id'];
    $query = "SELECT * 
              FROM user 
              WHERE id = $id";
    $rt = mysqli_query($con, $query);

  //obtener ciudades
  $query = "SELECT * FROM cargos";
  $cargos = mysqli_query($con, $query);
  //obtener departamentos
  $query = "SELECT * FROM departamentos_usuarios";
  $deptos = mysqli_query($con, $query);
  // Obtener los departamentos asignados al usuario
  $query_user_deptos = "SELECT departamento_id FROM usuario_departamento WHERE usuario_id = $id";
  $result_user_deptos = mysqli_query($con, $query_user_deptos);
  $user_departamentos = [];
  while ($row = mysqli_fetch_assoc($result_user_deptos)) {
      $user_departamentos[] = $row['departamento_id'];
  }
  if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $altemail = $_POST['alt_email'];
    $contact = $_POST['mobile'];
    $address = $_POST['address'];
    $userid = $_GET['id'];
    $cargo = $_POST['cargo'];
    $status = '0';
    $rol = 'user';
    if (isset($_POST['status'])) {
      $status = 1;
    }
    if (isset($_POST['rol'])){
      $rol = 'supervisor';
    }
    $ret = mysqli_query($con, "update user set name='$name', alt_email='$altemail',mobile='$contact',address='$address',status='$status',cargo='$cargo',rol='$rol' where id='$userid'");
    if ($ret) {
      $departamentos = isset($_POST['departamentos']) ? $_POST['departamentos'] : [];

      // Eliminar departamentos actuales del usuario
      $query_delete = "DELETE FROM usuario_departamento WHERE usuario_id = $userid";
      mysqli_query($con, $query_delete);
      // Insertar los nuevos departamentos seleccionados
      foreach ($departamentos as $depto_id) {
          $query_insert = "INSERT INTO usuario_departamento (usuario_id, departamento_id) VALUES ($userid, $depto_id)";
          mysqli_query($con, $query_insert);
      }
      echo "<script>alert('Datos Actualizados'); location.replace(document.referrer)</script>";
    }
  }
?>