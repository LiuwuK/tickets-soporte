<?php
require __DIR__.'/../../../../vendor/autoload.php';

//obtener info de los supervisores
$query = "SELECT * FROM supervisores";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();


//nuevo supervisor
if(isset($_POST['newSup'])){ 
    $nombre = $_POST['nombreSupervisor'];
    $email = $_POST['email'];
    $rut = $_POST['rut'];
    $num = $_POST['numC'];
    $query  = "INSERT INTO supervisores(nombre_supervisor, email, rut, numero_contacto)
                VALUES (?,?,?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssi",$nombre, $email, $rut, $num);
    if ($stmt->execute()) {
        echo "<script>alert('Supervisor registrado correctamente'); location.href='supervisor.php';</script>";
    } else {
        echo "<script>alert('Error en la consulta: ".$stmt->error."');</script>";
    }
}
//actualizar supervisor
if(isset($_POST['btnUpdt'])){
    $ids = $_POST['id'];
    $nombres = $_POST['name'];
    $correos = $_POST['email'];
    $ruts = $_POST['rut'];
    $nums = $_POST['numeroC'];

    foreach ($ids as $index => $id) {
        $nombre = $nombres[$index];
        $correo = $correos[$index];
        $rut = $ruts[$index];
        $num = $nums[$index];

        $query = "UPDATE supervisores 
                    SET nombre_supervisor = ?, 
                        email = ?,
                        rut = ?,
                        numero_contacto = ? 
                    WHERE id = ? 
                    AND (nombre_supervisor <> ? OR email <> ? OR rut <> ? OR numero_contacto <> ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssiisssi",$nombre, $correo, $rut, $num, $id, $nombre, $correo, $rut, $num,);
        $stmt->execute();
    }
    echo "<script>alert('Supervisores actualizados correctamente.'); location.href='supervisor.php';</script>";

}
//eliminar supervisor
if(isset($_POST['delSup'])){
    $id = $_POST['idSup'];
    $query = "DELETE FROM supervisores WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Supervisor eliminado correctamente.'); location.href='supervisor.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}


//Carga masiva
use PhpOffice\PhpSpreadsheet\IOFactory;
if(isset($_POST['carga'])){
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        $stmt = $con->prepare("INSERT INTO supervisores (nombre_supervisor, email, rut, numero_contacto) VALUES (?,?,?,?)");
    
        foreach ($data as $index => $row) {
            if ($index == 0) continue;
            $nombre = $row[0];
            $email = $row[1];
            $rut = $row[2];
            $num = $row[3];
    
            $stmt->bind_param("sssi", $nombre, $email, $rut, $num);
            $stmt->execute();
        }
    
        echo "<script>alert('Supervisores registrados correctamente'); location.href='supervisor.php';</script>";
    } else {
        echo "Error al subir el archivo.";
    }
}
?>