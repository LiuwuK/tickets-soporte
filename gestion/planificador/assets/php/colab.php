<?php
//obtener todos los colaboradores 
$query = "SELECT cl.*, vt.nombre AS verticalN
          FROM clientes cl
          JOIN verticales vt ON(cl.vertical = vt.id)";
if(isset($_GET['colabID'])){
    $id = $_GET['colabID'];
    $query .= "WHERE cl.id = $id";
}
$clientsData = $con->prepare($query);
$clientsData->execute();
$clientes = $clientsData->get_result();
$num_cl = 0; //$clientes->num_rows;


//obtener info del colaborador 
if(isset($_GET['colabID'])){
    $id = $_GET['colabID'];


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