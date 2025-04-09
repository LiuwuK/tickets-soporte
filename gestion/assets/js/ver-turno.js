document.addEventListener('DOMContentLoaded', function () {
    const filtros = {
        estado: document.getElementById('filtroEstado'),
        fechaInicio: document.getElementById('filtroFechaInicio'),
        fechaFin: document.getElementById('filtroFechaFin'),
        texto: document.getElementById('filtroTexto') 
    };

    Object.values(filtros).forEach(filtro => {
        filtro.addEventListener('change', actualizarResultados);
    });

    // Escuchar cambios en el campo de búsqueda
    filtros.texto.addEventListener('input', actualizarResultados);

    function actualizarResultados() {
        const estado = filtros.estado.value.toLowerCase();
        const fechaInicio = Date.parse(filtros.fechaInicio.value) || null;
        const fechaFin = Date.parse(filtros.fechaFin.value) || null;
        const texto = filtros.texto.value.toLowerCase();

        const resultados = turnosData.filter(item => {
            const itemFecha = Date.parse(item.fechaCreacion);

            // filtro texto coincide con algún campo
            const coincideTexto = (
                texto === '' || 
                (item.colaborador || '').toLowerCase().includes(texto) ||
                (item.estado || '').toLowerCase().includes(texto) ||
                (item.autorizadoPor || '').toLowerCase().includes(texto) ||
                (item.rut || '').toLowerCase().includes(texto) ||
                (item.motivo || '').toLowerCase().includes(texto) ||
                (item.instalacion || '').toLowerCase().includes(texto)
            );

            const coincideFecha = (
                (fechaInicio === null || itemFecha >= fechaInicio) &&
                (fechaFin === null || itemFecha <= fechaFin)
            );
            // filtros de  estado
            const coincideEstado = (
                (estado === '' || item.estado.toLowerCase() === estado)
            );
            // Aplicar todos los filtros
            return coincideTexto && coincideFecha && coincideEstado;
        });

        mostrarResultados(resultados);
    }

    function mostrarResultados(data) {
        const contenedor = document.getElementById('resultadoTurnos');
        contenedor.innerHTML = '';
        data.forEach(item => {
            // Formatear la fecha
            const fechaFormateada = new Date(item.fechaCreacion).toLocaleDateString('es-CL', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
 
            const fechaT = new Date(item.fechaTurno).toLocaleDateString('es-CL', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            // Formatear la hora
            const horaFormateada = new Date(item.fechaCreacion).toLocaleTimeString('es-CL', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            const resultadoFinal = `${fechaFormateada} ${horaFormateada}`;

            const itemHTML = `
                <div class="h-container" onclick="window.location.href='detalle-turno.php?id=${item.id}';">
                    <div class="h-header d-flex justify-content-between">
                        <div class="colab-turno">
                            <strong>${item.colaborador}</strong>
                            <p>Rut: ${item.rut}</p>
                        </div>
                        <div class="estado mt-2">
                            <span class="label label-estado">${item.estado}</span>
                        </div>
                    </div>
                    <div class="h-body">
                        <p>Instalación: ${item.instalacion ?? 'Sin Instalación'} </p>
                        <p>Fecha del Turno: ${item.fechaTurno}</p>
                        <p>Horas Cubiertas: ${item.horas} hrs</p>
                        <p>Motivo: ${item.motivo}</p>
                        <div class="h-footer">
                            <p>Autorizado por: ${item.autorizadoPor}</p>
                            <p>${resultadoFinal}</p>
                        </div>
                        
                    </div>
              
                </div>
            `;

            contenedor.insertAdjacentHTML('beforeend', itemHTML);
        });
    }
    // Ejecutar la función al cargar la página
    actualizarResultados();
});

