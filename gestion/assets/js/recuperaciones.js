document.addEventListener("DOMContentLoaded", function() {
    var calendarEl = document.getElementById('calendario');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: function(fetchInfo, successCallback, failureCallback) {
            var sucursalId = document.getElementById('filtro-sucursal').value;
            fetch(`../assets/php/get_recuperaciones.php?sucursal_id=${sucursalId}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            // Obtener datos del evento
            const eventData = info.event.extendedProps;
            const modal = new bootstrap.Modal(document.getElementById('eventoModal'));
            const fechaEvento = new Date(info.event.start);
            const fechaFormatoInput = fechaEvento.toISOString().split('T')[0];
            // Asignar valores
            document.getElementById('inputFecha').value = fechaFormatoInput;
            document.getElementById('inputMonto').value = eventData.monto || '';
            document.getElementById('inputId').value = eventData.id || 'N/A';
            document.getElementById('eventoModalLabel').textContent = eventData.sucursal;
            
            // Mostrar modal
            modal.show();            
            info.jsEvent.preventDefault();
        },
        eventDidMount: function(info) {
            // Tooltip con Bootstrap (opcional)
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
        buttonText: {
            dayGridMonth: 'Mes',
            dayGridDay: 'Día'
        }
    });

    calendar.render();
    // Filtra eventos al cambiar la sucursal
    document.getElementById('filtro-sucursal').addEventListener('change', function() {
        //actualizar calendario
        calendar.refetchEvents();
        //actualizar totales
        const sucursalId = this.value;
        fetch(`../assets/php/get_totalRec.php?sucursal_id=${sucursalId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                actualizarMontos(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    function actualizarMontos(data) {
        const formateador = new Intl.NumberFormat('en-US');
        const contenedorResultados = document.getElementById('contenedor-resultados');
        // Limpiar contenedor
        contenedorResultados.innerHTML = '';
        if (data.length === 0) {
            contenedorResultados.innerHTML = '<p>No hay datos disponibles</p>';
            return;
        }
        if (data.length > 1 || data[0].sucursal === null) {
            let html = '<h3>Total por sucursal:</h3>';
            data.forEach(item => {
                const monto = item['SUM(r.monto)'] || 0;
                html += `<li><strong>${item.sucursal || 'Sin sucursal'}:</strong> $${formateador.format(monto)}</li>`;
            });
            contenedorResultados.innerHTML = html;
        } 
        //sucursal seleccionada
        else {
            const item = data[0];
            const monto = item['SUM(r.monto)'] || 0;
            contenedorResultados.innerHTML = `
                <h3>Total para ${item.sucursal}:</h3>
                <p><strong>Monto recuperado:</strong> $${formateador.format(monto)}</p>
            `;
        }
    }

    // Cargar montos iniciales 
    fetch('../assets/php/get_totalRec.php')
        .then(response => response.json())
        .then(data => actualizarMontos(data))
        .catch(error => console.error('Error:', error));
    document.querySelectorAll("select.search-form").forEach(selectElement => {
        const choices = new Choices(selectElement, {
            searchEnabled: true,
            itemSelectText: "",
            placeholder: true
        });
    });
});
