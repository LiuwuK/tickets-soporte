<?php
$userId = $_SESSION['id'];
//obtener tipo_actividades 
$query = "SELECT * FROM tipo_actividades";
$tipoAct = mysqli_query($con, $query);
//obtener work log
$query = "SELECT wl.*, ta.nombre_actividad AS tAct
            FROM work_log wl
            JOIN tipo_actividades ta ON(wl.tipo_actividad = ta.id)
            WHERE user_id = ? AND DATE(wl.fecha) = CURDATE()
            ORDER BY wl.fecha ASC";
$stmt = $con->prepare($query);
$stmt->bind_param("i",$userId);
$stmt->execute();
$act = $stmt->get_result(); 
$num = $act->num_rows;

if(isset($_POST['addLog'])){
    $title = $_POST['title'];
    $tAct = $_POST['tAct'];
    $desc = $_POST['desc'];
    $date = $_POST['fecha'];

    $query  = "INSERT INTO  work_log (titulo, tipo_actividad, descripcion, fecha, user_id)
                VALUES (?, ?, ?, ?, ?) ";
    $stmt   = $con->prepare($query);
    $stmt->bind_param("sissi",$title, $tAct, $desc, $date , $userId);
    $stmt->execute();
    $stmt->close();
    echo "<script>location.replace(document.referrer)</script>";
}
?>