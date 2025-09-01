<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['billBtn'])) {
    $pID = intval($_POST['pId'] ?? 0);
    if ($pID > 0) {
        $stmt2 = $con->prepare("UPDATE proyectos SET xfacturar = '0' WHERE id = ?");
        if ($stmt2) {
            $stmt2->bind_param('i', $pID);
            $stmt2->execute();
            $stmt2->close();
        }
    }
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'projects-main.php'));
    exit;
}


$projectIds = array_column($rt->fetch_all(MYSQLI_ASSOC), 'id');
$rt->data_seek(0); 
$boms = [];
if (!empty($projectIds)) {
    $idsStr = implode(',', array_map('intval', $projectIds));
    $bomQuery = "SELECT proyecto_id, nombre, cantidad, total FROM bom WHERE proyecto_id IN ($idsStr)";
    $resBom = $con->query($bomQuery);
    while ($rowBom = $resBom->fetch_assoc()) {
        $boms[$rowBom['proyecto_id']][] = $rowBom;
    }
}