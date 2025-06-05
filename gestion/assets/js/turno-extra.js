document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("select.search-form").forEach(selectElement => {
        const choices = new Choices(selectElement, {
            searchEnabled: true,
            itemSelectText: "",
            placeholder: true
        });
    });

    // Agregar nueva fila
    document.getElementById('agregar-fila').addEventListener('click', function() {
        agregarFilaTurno();
    });
    
    // Eliminar fila
    document.getElementById('cuerpo-tabla').addEventListener('click', function(e) {
        if (e.target.classList.contains('eliminar-fila')) {
            e.target.closest('tr').remove();
        }
    });
});

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

let contadorTurnos = 1;
function agregarFilaTurno() {
    const cuerpoTabla = document.getElementById('cuerpo-tabla');
    const primeraFila = cuerpoTabla.querySelector('tr');
    
    if (!primeraFila) return;
    
    const nuevaFila = primeraFila.cloneNode(true);
    
    nuevaFila.querySelectorAll('[name^="nuevos_turnos["]').forEach(elemento => {
        const nameOriginal = elemento.getAttribute('name');
        const nuevoName = nameOriginal.replace(/nuevos_turnos\[\d+\]/, `nuevos_turnos[${contadorTurnos}]`);
        elemento.setAttribute('name', nuevoName);
    });
    
    nuevaFila.querySelectorAll('input').forEach(input => {
        if (input.type !== 'button') input.value = '';
    });
    
    nuevaFila.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });
    
    cuerpoTabla.appendChild(nuevaFila);
    contadorTurnos++;
}