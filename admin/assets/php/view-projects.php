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

//Se obtienen todos los proyectos
$query = "SELECT pr.id AS projectId, pr.*, es.nombre AS estado, ci.nombre_ciudad AS ciudadN, us.name AS ingeniero, tp.nombre AS tipoP
            FROM proyectos pr 
            JOIN estados es ON(pr.estado_id = es.id)
            JOIN ciudades ci ON(pr.ciudad = ci.id)
            LEFT JOIN user us ON(pr.ingeniero_responsable = us.id)
            JOIN tipo_proyecto tp ON(pr.tipo = tp.id)";
$stmt = $con->prepare($query);
$stmt->execute();
$rt = $stmt->get_result();

//total de resultados
$num = $rt->num_rows; 


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
?>