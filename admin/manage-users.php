<?php
session_start();
include("dbconnection.php");
include("checklogin.php");
include("assets/php/manage-users.php");
check_login();
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Admin | Gestionar Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Calendario CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker@0.6.6/dist/css/litepicker.css"/>
    <!-- CSS personalizados -->
    <link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/manage-users.css" rel="stylesheet" type="text/css" />
    <!-- Toast notificaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>


<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
    <?php include("header.php"); ?>
    </div>
    <div class="page-content">
    <?php include("leftbar.php"); ?>
        <div class="content">
            <div class="page-title">
                <h2>Gestionar usuarios</h2>
            </div>
            <br>
            <div class="row main-table">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="grid simple ">
                                <div class="grid-title no-border">
                                    <h4>Información de los Usuarios</h4>
                                    <div class="tools"> <a href="javascript:;" class="collapse"></a>
                                    </div>
                                </div>
                                <div class="grid-body no-border">

                                    <table class="table table-bordered table-hover no-more-tables">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre Completo</th>
                                                <th>Correo</th>
                                                <th>Numero de Contacto</th>
                                                <th>Fecha de Registro</th>
                                                <th>Activo</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $ret = mysqli_query($con, "select * from user where rol != 'admin' ");
                                            $cnt = 1;
                                            while ($row = mysqli_fetch_array($ret)) {
                                                $_SESSION['ids'] = $row['id'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo $row['name']; ?></td>
                                                    <td><?php echo $row['email']; ?></td>
                                                    <td><?php echo $row['mobile']; ?></td>
                                                    <td><?php echo $row['posting_date']; ?></td>
                                                    <td class="icons d-flex justify-content-center">
                                                    <?php
                                                        if( $row['status'] == 1 ){
                                                            echo '<i class="bi bi-check-circle"></i>';
                                                        } else {
                                                            echo '<i class="bi bi-x-circle"></i>'; 
                                                        }
                                                    ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <form name="abc" action="" method="post">
                                                            <a href="edit-user.php?id=<?php echo $row['id']; ?>" class="btn btn-updt ">Editar</a>
                                                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                            <button type="submit" name="delete" class="btn btn-del">Eliminar</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php $cnt = $cnt + 1;
                                            } ?>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>
  </div>

  <!-- Popper.js (para tooltips y otros componentes) -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <!-- Bootstrap Bundle (con Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Calendario -->
  <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
  <!-- Scripts propios -->
  <script src="../assets/js/sidebar.js"></script>

</body>

</body>

</html>


