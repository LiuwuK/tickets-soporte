<?php
//obtener todos los clientes 
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

//obtener info del cliente 
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

    //Obtener info de actividades

    $query = "SELECT * 
                FROM actividades
                WHERE cliente_id = $id
                ORDER BY fecha_inicio ASC
                ";
    $actData = $con->prepare($query);
    $actData->execute();
    $actividades = $actData->get_result();
    $num_act = $actividades->num_rows;

    //obtener licitaciones ganadas por los competidores
    $query = "SELECT pr.*, co.nombre_competidor AS competidorN , cl.nombre AS clasiN
                FROM proyectos pr
                LEFT JOIN competidores co ON(pr.competidor = co.id)
                LEFT JOIN clasificacion_proyecto cl ON(cl.id = pr.clasificacion)
                WHERE cliente = $id AND tipo = '1'";
    $licData = $con->prepare($query);
    $licData->execute();
    $licitaciones = $licData->get_result();
    $num_lic = $licitaciones->num_rows;

    if(isset($_POST['addComp'])){
        $competidor = $_POST['nombreCompetidor'];
        $rut = $_POST['rut'];
        $especialidad = $_POST['especialidad'];

        $checkQuery = "SELECT id 
                        FROM competidores 
                        WHERE rut = ?";
        $stmtCheck = $con->prepare($checkQuery);
        $stmtCheck->bind_param("s", $rut);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if($stmtCheck->num_rows > 0){
            echo "<script>alert('El competidor ya esta registrado'); location.replace(document.referrer)</script>";
        }else{
            //Subida de imagen-------------------------------------------------------------------------------------------------------
            // Configuración del directorio de carga
            $uploadDir = 'assets/img/competidores/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            // Procesar la imagen
            if(isset($_FILES['compImg']) && $_FILES['compImg']['error'] === UPLOAD_ERR_OK){
                $uploadedFile = $_FILES['compImg'];
                $filePath = '';

                $fileName = uniqid('comp_', true) .'.'. pathinfo($_FILES['compImg']['name'], PATHINFO_EXTENSION);
                $targetPath = $uploadDir . $fileName;

                // Mover archivo a la carpeta de destino
                if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                    $dir = 'assets/img/competidores/';
                    $filePath = $dir . $fileName;
                } else {
                    echo "Error al subir la imagen.";
                    exit;
                }
                
            } 
            $query =  "INSERT INTO competidores(nombre_competidor, rut, especialidad, img_perfil)
                        VALUES(?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssss",$competidor, $rut, $especialidad, $filePath); 
            $stmt->execute();
            echo "<script>alert('Competidor Registrado Correctamente'); location.replace(document.referrer)</script>";
        }
    }
    if(isset($_POST['addAct'])){
        $nombre = $_POST['nombreActividad']; 
        $fInicio = $_POST['fechaInicio'];
        $fTermino = $_POST['fechaTermino'];
        $descripcion = $_POST['descripcionActividad'];
        $cliente_id = $_GET['clientID'];

        $query =  "INSERT INTO actividades(nombre, fecha_inicio, fecha_termino, descripcion, cliente_id)
        VALUES(?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssssi", $nombre, $fInicio, $fTermino, $descripcion, $cliente_id); 
        $stmt->execute();
        echo "<script>alert('Actividad Registrada Correctamente'); location.replace(document.referrer)</script>";
    }

}

//NUEVO CLIENTE
if(isset($_POST['addClient'])){
    $cliente = $_POST['nombreCliente'];
    $vertical = $_POST['vertical'];
    $encargado = $_POST['nombreEnc'];
    $cargo = $_POST['cargo'];
    $correo = $_POST['correo'];
    // Verificar si el cliente ya existe
    $checkQuery = "SELECT id FROM clientes WHERE nombre = ?";
    $stmtCheck = $con->prepare($checkQuery);
    $stmtCheck->bind_param("s", $cliente);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        echo "<script>alert('El cliente ya está registrado.');</script>";
    }else{
        //Subida de imagen-------------------------------------------------------------------------------------------------------
        // Configuración del directorio de carga
        $uploadDir = 'assets/img/client/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Procesar la imagen
        if(isset($_FILES['clientImg']) && $_FILES['clientImg']['error'] === UPLOAD_ERR_OK){
            $uploadedFile = $_FILES['clientImg'];
            $filePath = '';

            $fileName = uniqid('client_', true) .'.'. pathinfo($_FILES['clientImg']['name'], PATHINFO_EXTENSION);
            $targetPath = $uploadDir . $fileName;

            // Mover archivo a la carpeta de destino
            if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                $dir = 'assets/img/client/';
                $filePath = $dir . $fileName;
            } else {
                echo "Error al subir la imagen.";
                exit;
            }
            
        } 

        $query =  "INSERT INTO clientes(nombre, vertical, encargado, cargo, correo, img_perfil)
                VALUES(?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sissss", $cliente, $vertical, $encargado, $cargo, $correo, $filePath); 
        $stmt->execute();
        echo "<script>alert('Cliente Registrado Correctamente'); location.replace(document.referrer)</script>";
    }
}
?>