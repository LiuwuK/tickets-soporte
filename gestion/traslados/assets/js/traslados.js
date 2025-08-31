

document.addEventListener("DOMContentLoaded", function() {
    // Mostrar formulario
    const radios = document.querySelectorAll('input[name="forms"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.form-data.mx-auto[id]').forEach(f => f.style.display='none');
            if(this.id === 'traslado') document.getElementById('trasladoForm').style.display='block';
            if(this.id === 'desvinculacion') document.getElementById('desvinculacionForm').style.display='block';
        });
    });

    // Configuración modales
    ['delTraslado','delDesv'].forEach(id => {
        const modal = document.getElementById(id);
        if(modal){
            modal.addEventListener('show.bs.modal', e => {
                const button = e.relatedTarget;
                const input = modal.querySelector('input[type=hidden]');
                input.value = button.getAttribute('data-id');
            });
        }
    });

    // Validación de formularios
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', e => {
            let valid = true;
            form.querySelectorAll('[required]').forEach(f => {
                if(!f.value.trim()){ f.classList.add('is-invalid'); valid=false; } 
                else f.classList.remove('is-invalid');
            });
            if(!valid){ e.preventDefault(); toastr.error('Por favor, complete todos los campos obligatorios'); }
        });
    });
    // Traslados
    document.querySelectorAll('.estado-select').forEach(select => {
    select.addEventListener('change', () => {
        const id = select.dataset.id;
        const estado = select.value;

        fetch('assets/php/update-estado.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `tipo=traslado&id=${id}&estado=${estado}`
        })
        .then(res => res.json())
        .then(data => {
        if(data.success){
            Swal.fire('¡Éxito!', 'Estado actualizado correctamente', 'success');
        } else {
            Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
        }
        })
        .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
    });
    });
    // Desvinculaciones
    document.querySelectorAll('.desv-select').forEach(select => {
select.addEventListener('change', () => {
    const id = select.dataset.id;
    const estado = select.value;

    fetch('assets/php/update-estado.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `tipo=desvinculacion&id=${id}&estado=${estado}`
    })
    .then(res => res.json())
    .then(data => {
    if(data.success){
        Swal.fire('¡Éxito!', 'Estado actualizado correctamente', 'success');
    } else {
        Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
    }
    })
    .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
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
});