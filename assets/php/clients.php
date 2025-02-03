<?php
//obtener info sobre los clientes
$query = "SELECT cl.*, vt.nombre AS verticalN
          FROM clientes cl
          JOIN verticales vt ON(cl.vertical = vt.id)";
if(isset($_GET['clientID'])){
    $id = $_GET['clientID'];
    $query .= "WHERE cl.id = $id";
}
$clientsData = $con->prepare($query);
$clientsData->execute();
$clientes = $clientsData->get_result();
$num_cl = $clientes->num_rows;

//obtener verticales
$query = "SELECT * FROM verticales";
$verticalData = $con->prepare($query);
$verticalData->execute();
$result = $verticalData->get_result();


if(isset($_GET['clientID'])){
    $id = $_GET['clientID'];
    //obtener monto de proyectos
    $query = "SELECT COALESCE(SUM(monto), 0) AS monto_total
                FROM proyectos
                WHERE cliente = $id";
    $montoData = $con->prepare($query);
    $montoData->execute();
    $result = $montoData->get_result();
    $monto = $result->fetch_assoc();
    //obtener info sobre los competidores
    $query = "SELECT * FROM competidores";
    $compData = $con->prepare($query);
    $compData->execute();
    $competidores = $compData->get_result();
    $num_com = $competidores->num_rows;
    //obtener licitaciones ganadas por los competidores
    $query = "SELECT pr.*, co.nombre_competidor AS competidorN 
                FROM proyectos pr
                LEFT JOIN competidores co ON(pr.competidor = co.id)
                WHERE cliente = $id AND tipo = '1'";
    $licData = $con->prepare($query);
    $licData->execute();
    $licitaciones = $licData->get_result();
    $num_lic = $licitaciones->num_rows;

    if(isset($_POST['addComp'])){
        $competidor = $_POST['nombreCompetidor'];
        $rut = $_POST['rut'];
        $especialidad = $_POST['especialidad'];
        $query =  "INSERT INTO competidores(nombre_competidor, rut, especialidad)
                    VALUES(?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sss",$competidor, $rut, $especialidad); 
        $stmt->execute();
        echo "<script>alert('Competidor Registrado Correctamente'); location.replace(document.referrer)</script>";
    }

}

//NUEVO CLIENTE
if(isset($_POST['addClient'])){
    $cliente = $_POST['nombreCliente'];
    $vertical = $_POST['vertical'];
    $query =  "INSERT INTO clientes(nombre, vertical)
                VALUES(?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $cliente, $vertical); 
    $stmt->execute();
    echo "<script>alert('Cliente Registrado Correctamente'); location.replace(document.referrer)</script>";
}
?>