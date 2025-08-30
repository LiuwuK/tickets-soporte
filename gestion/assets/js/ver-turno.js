document.addEventListener('DOMContentLoaded', function () {
    const filtros = {
        estado: document.getElementById('filtroEstado'),
        fechaInicio: document.getElementById('filtroFechaInicio'),
        fechaFin: document.getElementById('filtroFechaFin'),
        texto: document.getElementById('filtroTexto'),
        supervisor: document.getElementById('filtroSupervisor'),
        semanaActual: document.getElementById('filtroSemanaActual')
    };

    const form = document.getElementById('filtrosForm');
    const paginaInput = document.getElementById('paginaInput');

    // Configuración de semana actual
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

    // Mostrar resultados (solo para la página actual)
    function mostrarResultados() {
        const contenedor = document.getElementById('resultadoTurnos');
        contenedor.innerHTML = '';

        if (turnosData.length === 0) {
            contenedor.innerHTML = '<div class="alert alert-info">No se encontraron resultados</div>';
            return;
        }

        turnosData.forEach(item => {
            const fechaFormateada = new Date(item.fechaCreacion).toLocaleDateString('es-CL', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
 
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
            const fechaTurno = fecha.toLocaleDateString('es-ES');

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

    // Inicializar
    mostrarResultados();
    Object.values(filtros).forEach(filtro => {
        if (filtro) {
            filtro.addEventListener('change', function() {
                paginaInput.value = 1;
            });
        }
    });

    // Debounce para búsqueda de texto
    let timeout;
    filtros.texto.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            paginaInput.value = 1;
            form.submit();
        }, 500);
    });
});