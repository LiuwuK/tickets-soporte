document.addEventListener("DOMContentLoaded", function() {
    const calendarEl = document.getElementById('calendario');
    const contenedorResultados = document.getElementById('contenedor-resultados');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: function(fetchInfo, successCallback, failureCallback) {
            const sucursalId = document.getElementById('filtro-sucursal').value;
            fetch(`assets/php/get_recuperaciones_totales.php?sucursal_id=${sucursalId}`)
                .then(resp => resp.json())
                .then(data => {
                    successCallback(data.events);
                    actualizarMontos(data.totales);
                })
                .catch(failureCallback);
        },
        eventClick: function(info) {
            const eventData = info.event.extendedProps;
            const modal = new bootstrap.Modal(document.getElementById('eventoModal'));
            document.getElementById('inputFecha').value = new Date(info.event.start).toISOString().split('T')[0];
            document.getElementById('inputMonto').value = eventData.monto;
            document.getElementById('inputId').value = eventData.id;
            document.getElementById('eventoModalLabel').textContent = eventData.sucursal;
            modal.show();
            info.jsEvent.preventDefault();
        },
        eventDidMount: function(info) {
            new bootstrap.Tooltip(info.el, {
                title: info.event.title,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },
        headerToolbar: {
            left: 'dayGridMonth,dayGridDay',
            center: 'title',
            right: 'prev,next'
        },
        buttonText: { dayGridMonth: 'Mes', dayGridDay: 'Día' }
    });

    calendar.render();

    document.getElementById('filtro-sucursal').addEventListener('change', function() {
        calendar.refetchEvents();
    });

    function actualizarMontos(totales) {
        const formateador = new Intl.NumberFormat('en-US');
        if (!totales || Object.keys(totales).length === 0) {
            contenedorResultados.innerHTML = '<p>No hay datos disponibles</p>';
            return;
        }
        let html = '<h3>Total por sucursal:</h3><ul>';
        for (const sucursal in totales) {
            html += `<li><strong>${sucursal}:</strong> $${formateador.format(totales[sucursal])}</li>`;
        }
        html += '</ul>';
        contenedorResultados.innerHTML = html;
    }

    // Inicializar Choices.js
    document.querySelectorAll("select.search-form").forEach(el => new Choices(el, { searchEnabled: true, itemSelectText: "", placeholder: true }));

    // Botón de exportación
    document.getElementById('btn-exportar').addEventListener('click', function() {
        window.location.href = 'assets/php/exportar_recuperaciones.php';
    });
});
