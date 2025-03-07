<?php
$tipo = $_GET['tipo'];
$id = $_GET['id'];
if($tipo == 'traslado'){
    $query = "SELECT tr.nombre_colaborador AS colaborador,
                tr.rut AS rutC,
                tr.fecha_inicio_turno AS fecha_turno,
                tr.observacion AS obs,
                us.name AS soliN, -- Nombre del solicitante
                su_origen.nombre AS suOrigen, -- Sucursal de origen
                jo_origen.tipo_jornada AS joOrigen, -- Jornada de origen
                su_destino.nombre AS suDestino, -- Sucursal de destino
                jo_destino.tipo_jornada AS joDestino, -- Jornada de destino
                sup_origen.nombre_supervisor AS supOrigen, -- Supervisor de origen
                sup_destino.nombre_supervisor AS supDestino, -- Supervisor destino
                mg.motivo AS motivoN, -- Motivo traslado
                rol_origen.nombre_rol AS rolOrigen, -- rol origen
                rol_destino.nombre_rol AS rolDestino, -- rol destino
                tr.estado
            FROM traslados tr
            JOIN user us ON tr.solicitante = us.id
            JOIN sucursales su_origen ON tr.instalacion_origen = su_origen.id
            JOIN sucursales su_destino ON tr.instalacion_destino = su_destino.id
            JOIN jornadas jo_origen ON tr.jornada_origen = jo_origen.id
            JOIN jornadas jo_destino ON tr.jornada_destino = jo_destino.id
            JOIN supervisores sup_origen ON tr.supervisor_origen = sup_origen.id
            JOIN supervisores sup_destino ON tr.supervisor_destino = sup_destino.id
            JOIN motivos_gestion mg ON tr.motivo_traslado = mg.id
            JOIN roles rol_origen ON tr.rol_origen = rol_origen.id
            JOIN roles rol_destino ON tr.rol_destino = rol_destino.id
            WHERE tr.id = $id
           ";
    $tr = mysqli_query($con, $query);

}else{
    $query = "SELECT de.*, 
                    su.nombre AS instalacion,
                    us.name AS soliN,
                    sup.nombre_supervisor AS supervisor,
                    mo.motivo AS motivoEgreso
                FROM desvinculaciones de
                JOIN user us ON(de.solicitante = us.id)
                JOIN sucursales su ON(de.instalacion = su.id)
                JOIN supervisores sup ON(de.supervisor_origen = sup.id)
                JOIN motivos_gestion mo ON(de.motivo = mo.id)
                WHERE de.id = $id
                ";
    $dv = mysqli_query($con, $query);
}

?>