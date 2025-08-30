<?php

// ------------------- Cargar filtros estáticos -------------------
$ingenieros = $con->query("SELECT id,name FROM user WHERE cargo=1")->fetch_all(MYSQLI_ASSOC);
$clients = $con->query("SELECT id,nombre FROM clientes")->fetch_all(MYSQLI_ASSOC);
$verticales = $con->query("SELECT id,nombre FROM verticales")->fetch_all(MYSQLI_ASSOC);
$distData = $con->query("SELECT id,nombre FROM distribuidores")->fetch_all(MYSQLI_ASSOC);
$tipoProyecto = $con->query("SELECT id,nombre FROM tipo_proyecto")->fetch_all(MYSQLI_ASSOC);
$portal = $con->query("SELECT id,nombre_portal FROM portales")->fetch_all(MYSQLI_ASSOC);
$clasif = $con->query("SELECT id,nombre FROM clasificacion_proyecto")->fetch_all(MYSQLI_ASSOC);
$statusF = $con->query("SELECT * FROM estados WHERE type='project'");

// ------------------- Filtros GET -------------------
$tipoPrj = isset($_GET['tipoprjF'])?intval($_GET['tipoprjF']):'';
$portalF = isset($_GET['portalF'])?intval($_GET['portalF']):'';
$vertical_id = isset($_GET['verticalF'])?intval($_GET['verticalF']):'';
$status_id = isset($_GET['statusF'])?intval($_GET['statusF']):'';
$searchText = isset($_GET['textSearch'])?trim($_GET['textSearch']):'';
$clasificacion = isset($_GET['clasif'])?intval($_GET['clasif']):'';

// Paginación
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // dinámico
$page = isset($_GET['page'])?max(1,intval($_GET['page'])):1;
$offset = ($page-1)*$limit;

// ------------------- Query principal totalmente optimizada -------------------
$userId = $_SESSION['user_id'];
$conditions = [];
$params = [];
$types = '';

$query = "SELECT SQL_CALC_FOUND_ROWS
            pr.id AS projectId, pr.nombre, pr.monto, pr.fecha_creacion, pr.clasificacion,
            es.id AS estado_id, es.nombre AS estado,
            ci.nombre_ciudad AS ciudadN,
            us.name AS ingeniero,
            us_com.name AS comercial,
            tp.nombre AS tipoP,
            dt.nombre AS distribuidorN,
            ep.nombre_etapa AS etapaN,
            cl.nombre AS clienteN,
            lic.licitacion_id, lic.portal AS portal_id,
            GROUP_CONCAT(DISTINCT CONCAT_WS('||',ac.nombre,ac.fecha_inicio) SEPARATOR ';;') AS actividades,
            GROUP_CONCAT(DISTINCT CONCAT_WS('||',c.nombre,c.correo,c.cargo,c.numero) SEPARATOR ';;') AS contactos,
            GROUP_CONCAT(DISTINCT CONCAT_WS('||',b.nombre,b.cantidad,b.total) SEPARATOR ';;') AS bom
          FROM proyectos pr
          JOIN estados es ON pr.estado_id = es.id
          JOIN ciudades ci ON pr.ciudad = ci.id
          LEFT JOIN user us ON pr.ingeniero_responsable = us.id
          LEFT JOIN user us_com ON pr.comercial_responsable = us_com.id
          LEFT JOIN distribuidores dt ON pr.distribuidor = dt.id
          LEFT JOIN tipo_proyecto tp ON pr.tipo = tp.id
          LEFT JOIN etapas_proyecto ep ON pr.estado_etapa = ep.id
          LEFT JOIN clientes cl ON pr.cliente = cl.id
          LEFT JOIN licitacion_proyecto lic ON pr.id = lic.proyecto_id
          LEFT JOIN actividades ac ON ac.proyecto_id = pr.id
          LEFT JOIN contactos_proyecto c ON c.proyecto_id = pr.id
          LEFT JOIN bom b ON b.proyecto_id = pr.id";

// ------------------- Condiciones según usuario -------------------
if($_SESSION['cargo']==3){
    $conditions[] = "es.nombre='ganado' AND pr.xfacturar='1'";
} elseif($_SESSION['cargo']==5){
    $conditions[] = "pr.comercial_responsable=?";
    $params[]=$userId; $types.='i';
}

// ------------------- Filtros GET -------------------
if(!empty($status_id)) { $conditions[]="pr.estado_id=?"; $params[]=$status_id; $types.='i'; }
if(!empty($vertical_id)) { $conditions[]="pr.vertical=?"; $params[]=$vertical_id; $types.='i'; }
if(!empty($portalF)) { $conditions[]="lic.portal=?"; $params[]=$portalF; $types.='i'; }
if(!empty($tipoPrj)) { $conditions[]="pr.tipo=?"; $params[]=$tipoPrj; $types.='i'; }
if(!empty($clasificacion)) { $conditions[]="pr.clasificacion=?"; $params[]=$clasificacion; $types.='i'; }
if(!empty($searchText)) { 
    $conditions[]="(pr.id LIKE ? OR pr.nombre LIKE ?)";
    $sw="%$searchText%";
    $params[]=$sw; $params[]=$sw; $types.='ss';
}

if(!empty($conditions)) $query.=" WHERE ".implode(' AND ',$conditions);

$query.=" GROUP BY pr.id ORDER BY pr.id DESC LIMIT ? OFFSET ?";
$params[]=$limit; $params[]=$offset; $types.='ii';

$stmt = $con->prepare($query);
if(!empty($params)) $stmt->bind_param($types,...$params);
$stmt->execute();
$rt = $stmt->get_result();
$num = $rt->num_rows;

// Total resultados
$totalRes = $con->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];
$totalPages = ceil($totalRes/$limit);

// ------------------- Facturar proyecto -------------------
if(isset($_POST['billBtn'])){
    $pID=intval($_POST['pId']);
    $stmt2=$con->prepare("UPDATE proyectos SET xfacturar='0' WHERE id=?");
    $stmt2->bind_param('i',$pID);
    $stmt2->execute();
    $stmt2->close();
    echo "<script>location.replace(document.referrer)</script>";
}
?>
