document.getElementById('rut').addEventListener('input', function () {
    let rut = this.value.toUpperCase().replace(/[^0-9K]/g, '');
    
    if (rut.length > 9) rut = rut.slice(0, 9);
    let cuerpo = rut.slice(0, -1);
    let dv = rut.slice(-1); 

    if (cuerpo.length > 0) {
        cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        rut = cuerpo + (dv ? "-" + dv : "");
    }
    this.value = rut; 
});


document.addEventListener('DOMContentLoaded', function() {
    const editables = document.querySelectorAll('.editable');
    const guardarBtn = document.getElementById('guardarCambios');
    
    // Almacenar valores originales
    const originalValues = {};
    editables.forEach(editable => {
        const field = editable.dataset.field;
        originalValues[field] = editable.textContent.trim();
        editable.dataset.original = editable.textContent.trim();
    });

    // Función para verificar cambios
    function checkForChanges() {
        let hasChanges = false;
        
        editables.forEach(editable => {
            const field = editable.dataset.field;
            const currentValue = editable.textContent.trim();
            
            if (currentValue !== originalValues[field]) {
                hasChanges = true;
            }
        });
        
        guardarBtn.disabled = !hasChanges;
        guardarBtn.classList.toggle('btn-updt', hasChanges);
        guardarBtn.classList.toggle('btn-default', !hasChanges);
    }
    editables.forEach(editable => {
        editable.addEventListener('input', checkForChanges);    
        editable.addEventListener('blur', function() {
            this.textContent = this.textContent.trim();
            checkForChanges();
        });
    });

    guardarBtn.addEventListener('click', function() {
        const cambios = {};
        
        editables.forEach(editable => {
            const field = editable.dataset.field;
            cambios[field] = editable.textContent.trim();
        });
        
        if (cambios.correo && cambios.correo !== 'Sin definir') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(cambios.correo)) {
                alert('Por favor ingrese un correo electrónico válido');
                return;
            }
        }
        
        fetch('assets/php/updt-clientD.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: <?php echo $_GET['clientID'] ?>,
                datos: cambios
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cambios guardados correctamente');
                editables.forEach(editable => {
                    const field = editable.dataset.field;
                    originalValues[field] = editable.textContent.trim();
                    editable.dataset.original = editable.textContent.trim();
                });
                guardarBtn.disabled = true;
                guardarBtn.classList.remove('btn-updt');
                guardarBtn.classList.add('btn-default');
            } else {
                alert('Error al guardar: ' + (data.error || ''));
            }
        });
    });
});