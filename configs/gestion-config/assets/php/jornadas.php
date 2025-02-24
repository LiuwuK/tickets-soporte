<?php
require __DIR__.'/../../../../vendor/autoload.php';
//obtener info 
$query = "SELECT * FROM jornadas";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();


//nueva jornada
if(isset($_POST['newSup'])){ 
    $tipo = $_POST['tipoJornada'];
    $entrada = $_POST['entrada'];
    $salida = $_POST['salida'];

    $query  = "INSERT INTO jornadas(tipo_jornada, hora_entrada, hora_salida)
                VALUES (?,?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sss",$tipo, $entrada, $salida);
    if ($stmt->execute()) {
        echo "<script>alert('Jornada registrada correctamente'); location.href='jornadas.php';</script>";
    } else {
        echo "<script>alert('Error en la consulta: ".$stmt->error."');</script>";
    }
}
//actualizar jornada
if(isset($_POST['btnUpdt'])){
    $ids = $_POST['id'];
    $tipos = $_POST['name'];
    $entradas = $_POST['entrada'];
    $salidas = $_POST['salida'];
    

    foreach ($ids as $index => $id) {
        $tipo = $tipos[$index];
        $entrada = $entradas[$index];
        $salida = $salidas[$index];

        $query = "UPDATE jornadas 
                    SET tipo_jornada = ?, 
                        hora_entrada = ?,
                        hora_salida = ? 
                    WHERE id = ? 
                    AND (tipo_jornada <> ? OR hora_entrada <> ? OR hora_salida <> ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssisss", $tipo, $entrada, $salida, $id, $tipo, $entrada, $salida);
        $stmt->execute();
    }
    echo "<script>alert('Jornadas actualizadas correctamente.'); location.href='jornadas.php';</script>";

}
//eliminar jornada
if(isset($_POST['delSup'])){
    $id = $_POST['idSup'];
    $query = "DELETE FROM jornadas WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Jornada eliminado correctamente.'); location.href='jornadas.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}

//carga masiva
use PhpOffice\PhpSpreadsheet\IOFactory;
if(isset($_POST['carga'])){
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        $query = "INSERT INTO jornadas(tipo_jornada, hora_entrada, hora_salida)
                VALUES (?,?,?)";
        $stmt = $con->prepare($query);
    
        foreach ($data as $index => $row) {
            if ($index == 0) continue;
            $tipo = $row[0];
            $he = $row[1];
            $hs = $row[2];
            
            $stmt->bind_param("sss", $tipo,$he, $hs);
            $stmt->execute();
        }
    
        echo "<script>alert('Jornadas registradas correctamente'); location.href='jornadas.php';</script>";
    } else {
        echo "Error al subir el archivo.";
    }
}
?>