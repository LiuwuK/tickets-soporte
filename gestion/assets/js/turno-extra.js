let contadorTurnos = 1;
let filaBaseHTML = '';

document.addEventListener("input", function(e){
    if (e.target.matches("input[id='rut']")) {
        let input = e.target; 
        let rut = input.value.toUpperCase().replace(/[^0-9K]/g, ''); 
        
        if (rut.length > 9) rut = rut.slice(0, 9);

        let cuerpo = rut.slice(0, -1);
        let dv = rut.slice(-1); 

        if (cuerpo.length > 0) {
            cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, "");
            rut = cuerpo + (dv ? "-" + dv : "");
        }
        input.value = rut; 
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const primeraFila = document.querySelector('#cuerpo-tabla tr');
    if (primeraFila) {
        // Guardar copia limpia del HTML sin Choices aplicado
        filaBaseHTML = primeraFila.outerHTML;

        // Inicializar Choices en la fila original
        const selectInstalacion = primeraFila.querySelector('select[name="nuevos_turnos[0][instalacion]"]');
        if (selectInstalacion) {
            new Choices(selectInstalacion, {
                shouldSort: false,
                searchEnabled: true,
                itemSelectText: ''
            });
        }

        const selectMotivo = primeraFila.querySelector('select[name="nuevos_turnos[0][motivo]"]');
        if (selectMotivo) {
            new Choices(selectMotivo, {
                shouldSort: false,
                searchEnabled: true,
                itemSelectText: ''
            });
        }
    }

    // Botón agregar fila
    document.getElementById('agregar-fila').addEventListener('click', function () {
        agregarFilaTurno();
    });

    // Botón eliminar fila
    document.getElementById('cuerpo-tabla').addEventListener('click', function (e) {
        if (e.target.classList.contains('eliminar-fila')) {
            e.target.closest('tr').remove();
        }
    });
});

function agregarFilaTurno() {
    const cuerpoTabla = document.getElementById('cuerpo-tabla');
    if (!filaBaseHTML) return;

    // Convertimos string HTML a nodo
    const tempDiv = document.createElement('tbody');
    tempDiv.innerHTML = filaBaseHTML;

    const nuevaFila = tempDiv.querySelector('tr');

    // Actualizar los nombres
    nuevaFila.querySelectorAll('[name^="nuevos_turnos["]').forEach(el => {
        const name = el.getAttribute('name');
        const nuevoName = name.replace(/nuevos_turnos\[\d+\]/, `nuevos_turnos[${contadorTurnos}]`);
        el.setAttribute('name', nuevoName);
    });

    // Limpiar valores
    nuevaFila.querySelectorAll('input').forEach(input => {
        if (input.type !== 'button') input.value = '';
    });
    nuevaFila.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });

    // Inicializar Choices en el nuevo select de instalación
    const nuevoSelectInstalacion = nuevaFila.querySelector(`select[name="nuevos_turnos[${contadorTurnos}][instalacion]"]`);
    if (nuevoSelectInstalacion) {
        new Choices(nuevoSelectInstalacion, {
            shouldSort: false,
            searchEnabled: true,
            itemSelectText: ''
        });
    }
     // Inicializar Choices en el nuevo select de instalación
    const nuevoSelectMotivo = nuevaFila.querySelector(`select[name="nuevos_turnos[${contadorTurnos}][motivo]"]`);
    if (nuevoSelectMotivo) {
        new Choices(nuevoSelectMotivo, {
            shouldSort: false,
            searchEnabled: true,
            itemSelectText: ''
        });
    }

    cuerpoTabla.appendChild(nuevaFila);
    contadorTurnos++;
}