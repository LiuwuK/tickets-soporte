document.addEventListener('DOMContentLoaded', function () {
    const filtros = {
        estado: document.getElementById('filtroEstado'),
        fechaInicio: document.getElementById('filtroFechaInicio'),
        fechaFin: document.getElementById('filtroFechaFin'),
        texto: document.getElementById('filtroTexto'),
        supervisor: document.getElementById('filtroSupervisor'),
        semanaActual: document.getElementById('filtroSemanaActual')
    };

    Object.values(filtros).forEach(filtro => {
        filtro.addEventListener('change', actualizarResultados);
    });

    // Escuchar cambios en el campo de búsqueda
    filtros.texto.addEventListener('input', actualizarResultados);


    filtros.semanaActual.addEventListener('change', function(e) {
        if (e.target.checked) {
            const weekDates = getCurrentWeekDates();
            filtros.fechaInicio.value = weekDates.start;
            filtros.fechaFin.value = weekDates.end;
            filtros.fechaInicio.disabled = true;
            filtros.fechaFin.disabled = true;
        } else {
            filtros.fechaInicio.value = '';
            filtros.fechaFin.value = '';
            filtros.fechaInicio.disabled = false;
            filtros.fechaFin.disabled = false;
        }
        actualizarResultados();
    });
   
    function getCurrentWeekDates() {
        const today = new Date();
        const dayOfWeek = today.getDay();
        const startDate = new Date(today);
        startDate.setDate(today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1));
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + 6);
        
        return {
            start: startDate.toISOString().split('T')[0],
            end: endDate.toISOString().split('T')[0]
        };
    }

    function actualizarResultados() {
        const estado = filtros.estado.value.toLowerCase();
        const fechaInicio = Date.parse(filtros.fechaInicio.value) || null;
        const fechaFin = Date.parse(filtros.fechaFin.value) || null;
        const texto = filtros.texto.value.toLowerCase();
        const supervisor = filtros.supervisor.value.toLowerCase();

        const resultados = turnosData.filter(item => {
            const itemFecha = Date.parse(item.fechaTurno);

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
                (filtros.fechaInicio.value === '' || fechaInicio === null || itemFecha >= fechaInicio) &&
                (filtros.fechaFin.value === '' || fechaFin === null || itemFecha <= fechaFin)
            );

            const coincideSupervisor = (
                (supervisor === '' || item.supID === supervisor)
            );
            // filtros de  estado
            const coincideEstado = (
                (estado === '' || item.estado.toLowerCase() === estado)
            );
            // Aplicar todos los filtros
            return coincideTexto && coincideFecha && coincideEstado && coincideSupervisor;
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
 
            // Formatear la hora
            const horaFormateada = new Date(item.fechaCreacion).toLocaleTimeString('es-CL', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            const resultadoFinal = `${fechaFormateada} ${horaFormateada}`;
            const spanHistorico = item.tiene_historico > 0 
            ? `<span class="label label-rechazo">Justificado</span>` 
            : '';
            const fechaOriginal = item.fechaTurno;
            const [anio, mes, dia] = fechaOriginal.split('-');
            const fecha = new Date(anio, mes - 1, dia);
            const fechaTurno = (fecha.toLocaleDateString('es-ES'));

            const itemHTML = `
                <div class="h-container" onclick="window.location.href='detalle-turno.php?id=${item.id}';">
                    <div class="h-header d-flex justify-content-between">
                        <div class="colab-turno">
                            <strong>${item.colaborador}</strong>
                            <p>Rut: ${item.rut}</p>
                        </div>
                        <div class="estado mt-2">
                            <span class="label label-estado">${item.estado}</span>
                            ${spanHistorico}
                        </div>
                    </div>
                    <div class="h-body">
                        <p>Instalación: ${item.instalacion ?? 'Sin Instalación'} </p>
                        <p>Fecha del Turno: ${fechaTurno}</p>
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
    actualizarResultados();
});

