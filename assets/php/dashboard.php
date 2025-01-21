<?php
include("dbconnection.php");
//Informacion para dashboard 
//user tecnico
if ($_SESSION['cargo'] == 5){
  $query = "SELECT * 
            FROM ticket
            WHERE tecnico_asignado = '".$_SESSION['user_id']."'
            ORDER BY posting_date ASC";
  $tickets_pendiente = mysqli_query($con, $query);
} 
//user comercial
else if ($_SESSION['cargo'] == 2 or $_SESSION['cargo'] == 4){
  $query = "SELECT pr.*, es.nombre AS estado, cl.nombre AS clasiN
            FROM proyectos pr
            JOIN estados es ON (pr.estado_id = es.id)
            JOIN clasificacion_proyecto cl ON (pr.clasificacion = cl.id)";
  if($_SESSION['cargo'] == 2){
    $query .= "WHERE comercial_responsable = '".$_SESSION['user_id']."'
              AND estado_id = '19'
              ORDER BY monto DESC";
  }else{
    $query .= "WHERE  estado_id IN('19','20')
                ORDER BY monto DESC";
  }      
  $top_proyectos = mysqli_query($con, $query);

  $total_query = "SELECT COUNT(id) AS total_proyectos, SUM(monto) AS total_monto, SUM(CASE WHEN estado_id = '20' THEN monto ELSE 0 END) AS total_ganados
                  FROM proyectos";
  if($_SESSION['cargo'] == 2){
    $total_query .= " WHERE comercial_responsable = '".$_SESSION['user_id']."'";
  }      

  $total_result = mysqli_query($con, $total_query);
  $total_row = mysqli_fetch_assoc($total_result);
  $total_proyectos = $total_row['total_proyectos']; 
  $monto_general = $total_row['total_monto'];
  $monto_ganados = $total_row['total_ganados'];
}
//user contabilidad y finanzas
else if ($_SESSION['cargo'] == 3){
  $query = "SELECT pr.*, cl.nombre AS clasiN
            FROM proyectos pr
            JOIN clasificacion_proyecto cl ON (pr.clasificacion = cl.id)
            WHERE pr.estado_id = '20' AND pr.xfacturar = '1'
            ORDER BY pr.fecha_creacion DESC";
  $xfacturar = mysqli_query($con, $query);
}

//Informacion para graficos ------------------------------------------------
if($_SESSION['cargo'] == 4){
  $query = "SELECT	es.nombre, SUM(pr.monto) AS total_proyecto 
            FROM proyectos pr 
            RIGHT JOIN estados es ON(pr.estado_id = es.id)
            WHERE es.type = 'project'
            GROUP BY pr.estado_id
            ORDER BY es.nombre ASC";
  $proyectos_data = mysqli_query($con, $query);
  $tProject = [];
  $tProjectData = [];
  
  while ($row = mysqli_fetch_assoc($proyectos_data)) {
      $tProject[] = $row['nombre']; 
      $tProjectData[] = $row['total_proyecto']; 
      
  }


  $query = "SELECT	es.nombre, COUNT(pr.estado_id) AS total_proyecto 
  FROM proyectos pr 
  RIGHT JOIN estados es ON(pr.estado_id = es.id)
  WHERE es.type = 'project'
  GROUP BY pr.estado_id
  ORDER BY es.nombre desc";
  $proyectos_data = mysqli_query($con, $query);
  $cProject = [];
  $cProjectData = [];

  while ($row = mysqli_fetch_assoc($proyectos_data)) {
  $cProject[] = $row['nombre']; 
  $cProjectData[] = $row['total_proyecto']; 
  }
}
//--------------------------------------------------------------------------
//Notificaciones 
  $noti_query = "SELECT * 
                  FROM notificaciones
                  WHERE usuario_id = '".$_SESSION['user_id']."' 
                  AND admin = 0
                  ORDER BY creada_en DESC";
  $noti = mysqli_query($con, $noti_query);
//---------------------------------------------------------------------------
?>