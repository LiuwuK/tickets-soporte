<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Probar Endpoint</title></head>
<body>
<button onclick="probar()">Probar guardar-horario.php</button>
<pre id="resultado"></pre>
<script>
async function probar() {
    const datos = {
        colaborador_id: 1536,
        sucursal_id: 166,
        fecha_inicio: "2025-05-26",
        turno_id: 2,
        hora_entrada: "08:00",
        hora_salida: "17:00",
        jornada: "5x2",
        duracion: 4
    };

    const res = await fetch("assets/php/guardar-horario.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify(datos)
    });

    const text = await res.text();
    document.getElementById("resultado").textContent = text;
}
</script>
</body>
</html>