<?php
include("../dbconnection.php");
//obtener mes y año actual
$current_year = date('Y'); 
$current_month = date('m');

//Consulta para obtener datos mensuales
$query = "SELECT st.nombre AS statusN, COUNT(ti.id) AS cantidad
            FROM ticket ti
            JOIN estados st ON ti.status = st.id
            WHERE YEAR(ti.posting_date) = '$current_year' 
            AND MONTH(ti.posting_date) = '$current_month'
            GROUP BY st.nombre";

$monthly = $con->query($query);
$mdata = array();

if ($monthly->num_rows > 0) {
    while ($row = $monthly->fetch_assoc()) {
        $mdata[] = $row;
    }
}

//datos anuales
            
$query = "SELECT st.nombre AS statusN, COUNT(ti.id) AS cantidad, MONTH(ti.posting_date) AS mes
            FROM ticket ti
            JOIN estados st ON ti.status = st.id
            WHERE YEAR(ti.posting_date) = '$current_year'
            GROUP BY st.nombre, MONTH(ti.posting_date)";

$year = $con->query($query);
$ydata = array();

if ($year->num_rows > 0) {
    while ($row = $year->fetch_assoc()) {
        $ydata[] = $row;
    }
}

$response = array( 'monthly' => $mdata, 'yearly' => $ydata ); 
echo json_encode($response);

$con->close();
?>