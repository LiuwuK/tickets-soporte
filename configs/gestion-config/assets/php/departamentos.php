<?php
require __DIR__.'/../../../../vendor/autoload.php';
//obtener info 
$query = "SELECT * FROM departamentos";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();


//nuevo 
if(isset($_POST['newSup'])){ 
    $name = $_POST['nombre'];
    $deptoId = $_POST['deptoId'];
    $query  = "INSERT INTO departamentos(nombre_departamento, depto_id)
                VALUES (?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si",$name, $deptoId);
    if ($stmt->execute()) {
        echo "<script>alert('Departamento registrado correctamente'); location.href='departamentos.php';</script>";
    } else {
        echo "<script>alert('Error en la consulta: ".$stmt->error."');</script>";
    }
}
//actualizar
if(isset($_POST['btnUpdt'])){
    $ids = $_POST['id'];
    $name = $_POST['name'];
    $depto_id = $_POST['deptoId'];

    foreach ($ids as $index => $id) {
        $nombre = $name[$index];
        $deptoId = $depto_id[$index];
        $query = "UPDATE departamentos 
                    SET nombre_departamento = ?
                        depto_id = ?
                    WHERE id = ? 
                    AND (nombre_departamento <> ? OR depto_id <> ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("siisi", $nombre,$deptoId, $id, $nombre, $deptoId);
        $stmt->execute();
    }
    echo "<script>alert('departamentos actualizados correctamente.'); location.href='departamentos.php';</script>";

}
//eliminar 
if(isset($_POST['delSup'])){
    $id = $_POST['idSup'];
    $query = "DELETE FROM departamentos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('departamento eliminado correctamente.'); location.href='departamentos.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}

use PhpOffice\PhpSpreadsheet\IOFactory;
if(isset($_POST['carga'])){
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filePath = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        $stmt = $con->prepare("INSERT INTO departamentos (nombre_departamento, depto_id) VALUES (?,?)");
    
        foreach ($data as $index => $row) {
            if ($index == 0) continue;
            $nombre = $row[0];
            $id = $row[1];
            $stmt->bind_param("si", $nombre,$id);
            $stmt->execute();
        }
    
        echo "<script>alert('Departamentos registrados correctamente'); location.href='departamentos.php';</script>";
    } else {
        echo "Error al subir el archivo.";
    }
}

?>