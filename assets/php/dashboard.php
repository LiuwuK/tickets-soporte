<?php
include("dbconnection.php");
//Informacion para dashboard 
//user tecnico
if ($_SESSION['cargo'] == 5){
  $query = "SELECT * 
            FROM ticket
            WHERE tecnico_asignado = '".$_SESSION['user_id']."'
            ORDER BY posting_date ASC";
  $ticketsData = $con->prepare($query);
  $ticketsData->execute();
  $tickets_pendiente = $ticketsData->get_result();
  $num_t = $tickets_pendiente->num_rows;
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

  $top = $con->prepare($query);
  $top->execute();
  $top_proyectos = $top->get_result();
  $num_top = $top_proyectos->num_rows;

  //TOTAL DE LOS PROYECTOS
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
            ORDER BY pr.fecha_actualizacion DESC";
  $xfacturar = mysqli_query($con, $query);

  $pdata = $con->prepare($query);
  $pdata->execute();
  $xfacturar = $pdata->get_result();
  $num_xf = $xfacturar->num_rows;
}

//Informacion para graficos ------------------------------------------------
if($_SESSION['cargo'] == 4){
  //total monto por vertical 
  $query = "SELECT vt.nombre, COALESCE(SUM(pr.monto), 0) AS total_proyecto 
            FROM verticales vt
            JOIN proyectos pr ON (pr.vertical = vt.id)
            GROUP BY vt.id
            ORDER BY vt.nombre ASC";
  $proyectos_data = mysqli_query($con, $query);
  $tProject = [];
  $tProjectData = [];
  
  while ($row = mysqli_fetch_assoc($proyectos_data)) {
      $tProject[] = $row['nombre']; 
      $tProjectData[] = $row['total_proyecto']; 
      
  }
  //-------------------------------------------------------------------------------
  //Total monto proyectos por estado 
  $query = "SELECT es.nombre AS estado, MONTH(pr.fecha_creacion) AS mes, COALESCE(SUM(pr.monto), 0) AS total_proyecto
            FROM estados es
            LEFT JOIN proyectos pr ON pr.estado_id = es.id
            WHERE es.type = 'project' AND YEAR(pr.fecha_creacion) = YEAR(CURDATE())
            GROUP BY es.id, mes
            ORDER BY mes ASC, es.nombre ASC";

  $pdata = $con->prepare($query);
  $pdata->execute();
  $results = $pdata->get_result();

  $mesesP = range(1, 12); 
  $estados = ['Ganado', 'En Evaluación', 'Perdido']; 

  // array para almacenar los datos
  $dataEstados = array_fill(1, 12, array_fill_keys($estados, 0));

  // Procesar resultados
  while ($row = $results->fetch_assoc()) {
      $estado = $row['estado'];
      $mes = (int)$row['mes'];
      $total = (float)$row['total_proyecto'];
      
      $data[$mes][$estado] = $total;
  }
  
  $datasetsP = [];
  foreach ($estados as $estado) {
      $datasetsP[] = [
          'label' => $estado,
          'data' => array_map(function($mes) use ($estado, $data) {
              return isset($data[$mes][$estado]) ? $data[$mes][$estado] : 0;
          }, $mesesP),
          'fill' => false,
      ];
  }
 
  // Generar los datos para JavaScript
  $mp_json = json_encode($mesesP);
  $datap_json = json_encode($datasetsP);
  //-------------------------------------------------------------------------------

  // datos para total proyectos registrados
  $trimestre = isset($_POST['trimestre']) ? $_POST['trimestre'] : 1;
  // trimestres
  $meses_por_trimestre = [
      1 => ['01', '02', '03'],  
      2 => ['04', '05', '06'],  
      3 => ['07', '08', '09'],  
      4 => ['10', '11', '12']   
  ];
  if (!isset($meses_por_trimestre[$trimestre])) {
      echo "Trimestre no válido.";
      exit;
  }
  $meses_seleccionados = $meses_por_trimestre[$trimestre];
  // Consulta SQL para filtrar por los meses del trimestre seleccionado
  $con->query("SET lc_time_names = 'es_ES';");
  $query = " SELECT es.nombre AS estado, MONTHNAME(pr.fecha_creacion) AS mes, MONTH(pr.fecha_creacion) AS mes_num, COALESCE(COUNT(pr.id), 0) AS total_proyecto
              FROM estados es
              LEFT JOIN proyectos pr ON (pr.estado_id = es.id)
              WHERE es.type = 'project' AND YEAR(pr.fecha_creacion) = YEAR(CURDATE())
              AND MONTH(pr.fecha_creacion) IN (" . implode(',', $meses_seleccionados) . ")
              GROUP BY es.id, mes
              ORDER BY mes_num ASC;";
  
  $pdata = $con->prepare($query);
  $pdata->execute();
  $results = $pdata->get_result();

  $maximo = 0;
  // Sumar cantidad proyectos 
  if ($results->num_rows > 0) {
      while ($row = $results->fetch_assoc()) {
          $maximo += $row['total_proyecto'];
      }
  } else {
      echo "No se encontraron datos.";
  }
  $data = [];
  $meses = [];
  foreach ($results as $row) {
      $estado = $row['estado'];
      $mes = $row['mes'];
      $total = (int) $row['total_proyecto'];

      if (!in_array($mes, $meses)) {
          $meses[] = $mes;
      }

      // Organiza los datos por estado
      if (!isset($data[$estado])) {
          $data[$estado] = array_fill_keys($meses, 0); 
      }
      $data[$estado][$mes] = $total;
  }


  foreach ($data as $estado => &$values) {
      foreach ($meses as $mes) {
          if (!isset($values[$mes])) {
              $values[$mes] = 0;
          }
      }
  }
  unset($values); 

  // Organiza los datos 
  $datasets = [];
  foreach ($data as $estado => $values) {
      $datasets[] = [
          'label' => $estado,
          'data' => array_values($values), 
          'fill' => false,
          'tension' => 0.4
      ];
  }

  $meses_json = json_encode($meses);
  $datasets_json = json_encode($datasets);
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