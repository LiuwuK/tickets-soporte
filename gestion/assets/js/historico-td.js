document.addEventListener('DOMContentLoaded', function () {
    const filtros = {
        tipo: document.getElementById('filtroTipo'),
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
        const tipo = filtros.tipo.value.toLowerCase();
        const estado = filtros.estado.value.toLowerCase();
        const fechaInicio = Date.parse(filtros.fechaInicio.value) || null;
        const fechaFin = Date.parse(filtros.fechaFin.value) || null;
        const texto = filtros.texto.value.toLowerCase();

        const resultados = historicoData.filter(item => {
            const itemFecha = Date.parse(item.fecha);

            // filtro texto coincide con algún campo
            const coincideTexto = (
                texto === '' || 
                item.colaborador.toLowerCase().includes(texto) ||
                item.tipo.toLowerCase().includes(texto) ||
                item.estado.toLowerCase().includes(texto) ||
                item.solicitante.toLowerCase().includes(texto) ||
                item.rut.toLowerCase().includes(texto)||
                (item.observacion && item.observacion.toLowerCase().includes(texto)) ||
                (item.obs_rrhh && item.obs_rrhh.toLowerCase().includes(texto))
            );

            const coincideFecha = (
                (fechaInicio === null || itemFecha >= fechaInicio) &&
                (fechaFin === null || itemFecha <= fechaFin)
            );

            // filtros de tipo y estado
            const coincideTipoYEstado = (
                (tipo === '' || item.tipo.toLowerCase() === tipo) &&
                (estado === '' || item.estado.toLowerCase() === estado)
            );

            // Aplicar todos los filtros
            return coincideTexto && coincideFecha && coincideTipoYEstado;
        });

        mostrarResultados(resultados);
    }

    function mostrarResultados(data) {
        const contenedor = document.getElementById('resultadoHistorico');
        contenedor.innerHTML = '';
        data.forEach(item => {
            // Formatear la fecha
            const fechaFormateada = new Date(item.fecha).toLocaleDateString('es-CL', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            // Formatear la hora
            const horaFormateada = new Date(item.fecha).toLocaleTimeString('es-CL', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            const resultadoFinal = `${fechaFormateada} ${horaFormateada}`;

            const itemHTML = `
                <div class="h-container" onclick="window.location.href='detalle-historico.php?id=${item.id}&tipo=${item.tipo}';">
                    <div class="h-header d-flex justify-content-between">
                        <div class="colab">
                            <strong>${item.colaborador}</strong>
                            <p>Rut: ${item.rut}</p>
                        </div>
                        <div class="estado mt-2">
                            <span class="label label-estado">${item.estado}</span>
                        </div>
                    </div>
                    <div class="h-body">
                        <p>Fecha: ${resultadoFinal}</p>
                        <p>Tipo: ${item.tipo.charAt(0).toUpperCase() + item.tipo.slice(1).toLowerCase()}</p>
                        <p>Creado por: ${item.solicitante}</p>
                        <p>Observación SSPP: ${item.observacion ? item.observacion.charAt(0).toUpperCase() +
                            item.observacion.slice(1).toLowerCase() : 'No hay observación'}</p>
                        <p>Observación RRHH: ${item.obs_rrhh ? item.obs_rrhh.charAt(0).toUpperCase() +
                            item.obs_rrhh.slice(1).toLowerCase() : 'No hay observación'}</p>
                    </div>
                </div>
            `;

            contenedor.insertAdjacentHTML('beforeend', itemHTML);
        });
    }

    // Ejecutar la función al cargar la página
    actualizarResultados();
});