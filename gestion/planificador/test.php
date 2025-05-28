<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Probar Endpoint</title></head>
<body>
<button onclick="probar()">Probar guardar-horario.php</button>
<pre id="resultado"></pre>
<script>
async function probar() {
    const datos = {
        sucursal_id: 97,
        fecha_inicio: "2025-05-26",
        fecha_fin: "2025-06-26",
        turno_id: 8,
        hora_entrada: "08:30",
        hora_salida: "18:30",
        patron_jornada: "5x2",
    };

    const res = await fetch("assets/php/test.php", {
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