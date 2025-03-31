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
        eventDidMount: function(info) {
            const tooltip = document.createElement('div');
            tooltip.className = 'fc-custom-tooltip';
            tooltip.textContent = info.event.title;
            info.el.appendChild(tooltip);

            info.el.addEventListener('mouseenter', () => tooltip.style.opacity = '1');
            info.el.addEventListener('mouseleave', () => tooltip.style.opacity = '0');
        },
        headerToolbar: {
            left:'dayGridMonth,dayGridDay',
            center: 'title',
            right: 'prev,next',
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
