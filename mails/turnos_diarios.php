<?php
include("../dbconnection.php");
require '../vendor/autoload.php'; // PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//credenciales  USER = CORREO / PASS = CLAVE DE APLICACION GOOGLE 
$user = 'stsafeteck@gmail.com'; // Correo
$pass = 'molc xtfj nfev kruf'; // Contraseña de aplicación

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Definir estilos para los encabezados
$styleColor1 = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFD9E1F2'] //(azul claro)
    ],
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'] // Texto en negro
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];
$styleColor2 = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFF2D9D9'] //(rojo claro)
    ],
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'] // Texto en negro
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];
$styleColor3 = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFE2EFDA'] //(verde claro)
    ],
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'] // Texto en negro
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];

// Encabezados de la tabla
$headers = [
    'Turno ID', 'Fecha de Subida', 'Estado', 'Autorizado Por', 'Instalación',
    'Fecha del Turno (DIA-MES-AÑO)', 'Horas cubiertas', 'Monto',
    'Nombre y Apellido', 'RUT', 'Nacionalidad',
    'Banco', 'RUT Cuenta', 'Número de cuenta',
    'Motivo', 'Persona del Motivo', 'Motivo Rechazo', 'Justificación',
    'Contratado'
];

// Aplicar estilos a los encabezados
$sheet->fromArray([$headers], NULL, 'A1');
$sheet->getStyle('A1:H1')->applyFromArray($styleColor1);
$sheet->getStyle('I1:K1')->applyFromArray($styleColor2);
$sheet->getStyle('L1:N1')->applyFromArray($styleColor3);
$sheet->getStyle('O1:S1')->applyFromArray($styleColor1);

$query = "
    SELECT
        te.id AS idTurno,       
        te.created_at AS fechaCreacion,          
        te.estado AS estado, 
        us.name AS autorizadoPor,
        su.nombre AS instalacion,
        te.fecha_turno AS fechaTurno, 
        te.horas_cubiertas AS horas, 
        te.monto AS monto,
        te.nombre_colaborador AS colaborador, 
        te.rut AS rut,
        te.nacionalidad AS nacionalidad,  
        bc.nombre_banco AS banco, 
        CONCAT(IFNULL(dp.rut_cta, ''), '-', IFNULL(dp.digito_verificador, '')) AS RUTcta, 
        dp.numero_cuenta AS numCuenta,
        mg.motivo AS motivo,
        te.persona_motivo AS persona_motivo,
        te.motivo_rechazo AS motivoN,
        te.justificacion AS justificacion,
        te.contratado AS contratado
    FROM turnos_extra te
    LEFT JOIN sucursales su ON te.sucursal_id = su.id
    LEFT JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
    LEFT JOIN bancos bc ON dp.banco = bc.id
    LEFT JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
    LEFT JOIN `user` us ON te.autorizado_por = us.id
    WHERE te.fecha_turno = DATE_ADD(CURRENT_DATE(), INTERVAL -1 DAY)
    ORDER BY te.created_at DESC
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Error en la consulta SQL: " . mysqli_error($con));
}
$rowIndex = 2;
// Agregar datos a la hoja
while ($row = mysqli_fetch_assoc($result)) {
    // Formatear fechas
    $fechaTurno = DateTime::createFromFormat('Y-m-d', $row['fechaTurno']);
    $row['fechaTurno'] = $fechaTurno ? $fechaTurno->format('d-m-Y') : '';

    $fechaCreacion = DateTime::createFromFormat('Y-m-d H:i:s', $row['fechaCreacion']);
    $row['fechaCreacion'] = $fechaCreacion ? $fechaCreacion->format('d-m-Y H:i:s') : '';

    // Formatear "contratado"
    $row['contratado'] = ($row['contratado'] == 1) ? "SI" : "NO";

    // Convertir array asociativo a indexado
    $rowData = array_values($row);
    $sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Definir anchos de columna
$columnWidths = [
    'A' => 15, 'B' => 20, 'C' => 25, 'D' => 20, 'E' => 30, 'F' => 20,
    'G' => 15, 'H' => 15, 'I' => 25, 'J' => 15, 'K' => 15, 'L' => 20,
    'M' => 20, 'N' => 20, 'O' => 25, 'P' => 30, 'Q' => 20, 'R' => 30,
    'S' => 20, 'T' => 15
];

// Aplicar anchos
foreach ($columnWidths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}

// Generar el archivo
$writer = new Xlsx($spreadsheet);
$fileName = 'turnos_' . date('Y-m-d') . '.xlsx';
$tempFile = tempnam(sys_get_temp_dir(), $fileName);
$writer->save($tempFile);

$mail = new PHPMailer(true);
$fTurnos =  date('d/m/Y', strtotime('-1 day'));
try {
    // Configuración SMTP 
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $user;
    $mail->Password = $pass;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Remitente y destinatarios
    $mail->setFrom('stsafeteck@gmail.com', 'Turnos Diarios');
    $destinatarios = [
        'fnorton@gruposafeteck.com' => 'Usuario',
        'fponce@gruposafeteck.com' => 'Usuario',
        'fsilva@gruposafeteck.com' => 'Usuario',
        'kcarvajal@gruposafeteck.com' => 'Usuario',
        'aarriagada@gruposafeteck.com' => 'Usuario',
    ];

    foreach ($destinatarios as $email => $nombre) {
        $mail->addAddress($email, $nombre);
    }

    date_default_timezone_set('America/Santiago');
    // Asunto y cuerpo del mensaje
    $mail->Subject = 'Reporte Diario de Turnos - '.$fTurnos;
    $mail->Body = '
        <h2>Reporte de Turnos</h2>
        <p>Adjunto encontrarás el reporte de turnos correspondiente al día de ayer.</p>
        <p><strong>Fecha de generación:</strong> '.date('d/m/Y H:i:s').'</p>
    ';
    $mail->AltBody = 'Reporte de turnos adjunto.';

    // Adjuntar el Excel generado
    $mail->addAttachment($tempFile, $fileName);

    $mail->send();

    // Limpieza y confirmación
    unlink($tempFile);
    echo '<script>alert("Reporte enviado por correo exitosamente"); window.history.back();</script>';

} catch (Exception $e) {
    unlink($tempFile); 
    echo '<script>alert("Error al enviar el reporte: ' . $mail->ErrorInfo . '"); window.history.back();</script>';
    error_log("Error al enviar reporte: " . $e->getMessage());
}
?>