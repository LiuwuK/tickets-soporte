//TURNOS---------------------------------------------------------------------------------------------------------------
document.getElementById('formTurnos').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../php/guardar-turnos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Turnos guardados correctamente');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
    
});
function agregarTurno() {
    const tbody = document.getElementById('cuerpo-tabla');
    const plantilla = document.getElementById('plantilla-fila');
    
    // Clonar la plantilla
    const nuevaFila = plantilla.cloneNode(true);
    nuevaFila.style.display = '';
    nuevaFila.removeAttribute('id');

    const inputs = nuevaFila.querySelectorAll('[name]');
    inputs.forEach(input => {
        const originalName = input.name;
        const newName = originalName.replace('nuevos_turnos[]', `turnos[${contadorNuevos}]`);
        input.name = newName;
        
        if (input.tagName === 'INPUT' || input.tagName === 'SELECT') {
            input.required = true;
        }
    });
    
    tbody.insertBefore(nuevaFila, plantilla);
    contadorNuevos++;
    actualizarBotonesEliminar();
}

function eliminarTurno(boton) {
    const fila = boton.closest('tr');
    const filasVisibles = document.querySelectorAll('#cuerpo-tabla tr:not(#plantilla-fila)').length;
    
    if (filasVisibles <= 1) {
        alert('Debe haber al menos un turno');
        return;
    }
    
    fila.remove();
    actualizarBotonesEliminar();
}

function actualizarBotonesEliminar() {
    const filasVisibles = document.querySelectorAll('#cuerpo-tabla tr:not(#plantilla-fila)').length;
    const botones = document.querySelectorAll('#cuerpo-tabla button.btn-danger');
    
    botones.forEach(boton => {
        boton.disabled = filasVisibles <= 1;
    });
}

// Manejo del formulario
document.getElementById('formTurnos').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validar que al menos haya un turno
    const turnos = document.querySelectorAll('input[name^="turnos["]');
    if (turnos.length === 0) {
        alert('Debe agregar al menos un turno');
        return;
    }
    
    // Enviar formulario
    this.submit();
});
//FIN TURNOS------------------------------------------------------------------------------------------------------------------
