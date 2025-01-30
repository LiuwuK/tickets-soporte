<?php
//obtener ingenieros
$query =  "SELECT id, name 
            FROM user
            WHERE cargo = '1'";
$inge = mysqli_query($con, $query);
$ingenieros = [];
while ($row = mysqli_fetch_assoc($inge)) {
    $ingenieros[] = $row;
}

//carga las verticales, estados y distribuidores para filtrar--------------------------------------------------------------
$query = "SELECT * FROM verticales ";
$verticalData = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($verticalData)) {
  $verticales[] = $row; 
}

$query = "SELECT * FROM distribuidores ";
$distribuidorData = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($distribuidorData)) {
  $distData[] = $row; 
}

$query = "SELECT * FROM tipo_proyecto ";
$tipoProyectoData = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($tipoProyectoData)) {
  $tipoProyecto[] = $row; 
}

$query = "SELECT * FROM portales";
$portalData = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($portalData)) {
  $portal[] = $row; 
}

$query = "SELECT * FROM clasificacion_proyecto";
$clasData = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($clasData)) {
  $clasif[] = $row; 
}

$query_st = "SELECT * FROM estados WHERE type = 'project'";
$statusF = mysqli_query($con, $query_st);
//----------------------------------------------------------------------------------------------------------
//La query cambia dependiendo si se filtra por prioridad y/o estado (FILTROS)
$vertical_id = isset($_GET['verticalF']) ? intval($_GET['verticalF']) : '';
$distribuidor_id = isset($_GET['distribuidorF']) ? intval($_GET['distribuidorF']) : '';
$status_id = isset($_GET['statusF']) ? intval($_GET['statusF']) : '';
$searchText = isset($_GET['textSearch']) ? trim($_GET['textSearch']) : '';
$tipoPrj = isset($_GET['tipoprjF']) ? intval($_GET['tipoprjF']) : '';
$portalF = isset($_GET['portalF']) ? intval($_GET['portalF']) : '';
$clasificacion =  isset($_GET['clasif']) ? intval($_GET['clasif']) : '';

//----------------------------------------------------------------------------------------------------------

//Se obtienen todos los proyectos
$query = "SELECT pr.id AS projectId, pr.*, es.nombre AS estado, ci.nombre_ciudad AS ciudadN, us.name AS ingeniero, 
            us_com.name AS comercial, tp.nombre AS tipoP,lic.portal AS portal
            FROM proyectos pr 
            JOIN estados es ON(pr.estado_id = es.id)
            JOIN ciudades ci ON(pr.ciudad = ci.id)
            LEFT JOIN user us ON(pr.ingeniero_responsable = us.id)
            JOIN user us_com ON (pr.comercial_responsable = us_com.id)
            LEFT JOIN licitacion_proyecto lic ON(pr.id = lic.proyecto_id)
            JOIN tipo_proyecto tp ON(pr.tipo = tp.id)
            ";

// Filtros dinámicos
    $conditions = [];
    $params = [];
    $types = '';

// Filtrar por distribuidor
if (!empty($distribuidor_id)) {
    $conditions[] = "pr.distribuidor = ?";
    $params[] = $distribuidor_id;
    $types .= 'i';
}

// Filtrar por prioridad
if (!empty($vertical_id)) {
    $conditions[] = "pr.vertical = ?";
    $params[] = $vertical_id;
    $types .= 'i';
}

// Filtrar por estado
if (!empty($status_id)) {
    $conditions[] = "pr.estado_id = ?";
    $params[] = $status_id;
    $types .= 'i';
}
// Filtrar por portal
if (!empty($portalF)) {
    $conditions[] = "lic.portal = ?";
    $params[] = $portalF;
    $types .= 'i';
}

// Filtrar por tipo de proyecto
if (!empty($tipoPrj)) {
    $conditions[] = "pr.tipo = ?";
    $params[] = $tipoPrj;
    $types .= 'i';
}

// Filtrar por clasificacion
if (!empty($clasificacion)) {
    $conditions[] = "pr.clasificacion = ?";
    $params[] = $clasificacion;
    $types .= 'i';
}



// Filtrar por texto (nombre del proyecto o ID)
if (!empty($searchText)) {
    $conditions[] = "(pr.id LIKE ? OR pr.nombre LIKE ?)";
    $searchWildcard = '%' . $searchText . '%';
    $params[] = $searchWildcard;
    $params[] = $searchWildcard;
    $types .= 'ss';
}

// Combinar las condiciones
if (!empty($conditions)) {
$query .= ' WHERE ' . implode(' AND ', $conditions);
}

$query .= ' ORDER BY id DESC';
$stmt = $con->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$rt = $stmt->get_result(); 
if (!$rt) {
    die("Error en la consulta: " . mysqli_error($con));
}
//total de resultados
$num = $rt->num_rows; 
//-------------------------------------------------------------------------------------------------------------------------------------------
//asignar ingeniero al proyecto
if (isset($_POST["asignarIng"])) {
    $ingeId =  $_POST['ingeniero'];
    $pID    =  $_POST['pId'];

    $query =  " UPDATE proyectos
                SET ingeniero_responsable = ?
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii",$ingeId, $pID);

    if ($stmt->execute()) {
        echo "<script>alert('Ingeniero asignado correctamente');location.replace(document.referrer)</script>";
    } else {
        echo "<script>alert('error');location.replace(document.referrer)</script>";
    }

    $stmt->close();
}

//cerrar proyecto
if(isset($_POST['endBtn'])){
    $pID    =  $_POST['pId'];
    $estado = $_POST['estado'];
    
    $query =  " UPDATE proyectos
                SET estado_id = ?
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii",$estado, $pID);

    if ($stmt->execute()) {
        echo "<script>alert('Cierre realizado correctamente');location.replace(document.referrer)</script>";
    } else {
        echo "<script>alert('error');location.replace(document.referrer)</script>";
    }

    $stmt->close();
}


?>