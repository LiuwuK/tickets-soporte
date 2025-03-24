<?php
session_start();
include("../../dbconnection.php");
include("../../../checklogin.php");
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Toast notificaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <!-- CSS personalizados -->
    <link href="../../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
    <link href="../../../assets/css/manage-users.css" rel="stylesheet" type="text/css" />
</head>

<body class="test">
    <!-- Sidebar -->
    <div class="page-container ">
        <div class="sidebar">
            <?php include("../../header.php"); ?>
        </div>
        <div class="page-content">
            <?php include("../../leftbar.php"); ?>
            <div class="content">
                <div class="page-title d-flex justify-content-between">
                    <h2>
                        <i class="bi bi-person-gear"></i> 
                        Gestionar usuarios
                    </h2>
                    <button class=" btn-back" onclick="window.location.href='user-main.php';"> 
                        <i class="bi bi-arrow-left"></i>
                    </button>
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
                                        <table id="usersTable" class="table table-bordered table-hover no-more-tables display nowrap" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Correo</th>
                                                    <th>Fecha de Registro</th>
                                                    <th>Activo</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $query = "select * from user where rol != 'admin' ";
                                                $ret = mysqli_query($con, $query);
                                                $cnt = 1;
                                                while ($row = mysqli_fetch_array($ret)) {
                                                    $_SESSION['ids'] = $row['id'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo $cnt; ?></td>
                                                        <td><?php echo $row['name']; ?></td>
                                                        <td><?php echo $row['email']; ?></td>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Scripts propios -->
    <script src="../../../assets/js/sidebar.js"></script>

    <script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            responsive: true,
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activar para ordenar la columna ascendente",
                    "sortDescending": ": activar para ordenar la columna descendente"
                }
            },
            columnDefs: [
                { responsivePriority: 1, targets: 1 }, 
                { responsivePriority: 2, targets: -1 },
                { orderable: false, targets: [0, -1] } 
            ],
            order: [[0, 'asc']]
        });
    });
    </script>
</body>
</html>