function mostrarFormulario(id) {
    const traslado = document.getElementById('traslado');
    const desvinculacion = document.getElementById('desvinculacion');
    const trasladoForm = document.getElementById('trasladoForm');
    const desvinculacionForm = document.getElementById('desvinculacionForm');

    if (document.getElementById(id).style.display === 'block') {
        traslado.checked = false;
        desvinculacion.checked = false;
        trasladoForm.style.display = 'none';
        desvinculacionForm.style.display = 'none';
    } else {
        trasladoForm.style.display = 'none';
        desvinculacionForm.style.display = 'none';
        document.getElementById(id).style.display = 'block';
    }
}
document.addEventListener("input", function(e){
    if (e.target.matches("input[id='rut']")) {
        let input = e.target; 
        let rut = input.value.toUpperCase().replace(/[^0-9K]/g, ''); 
        
        if (rut.length > 9) rut = rut.slice(0, 9);

        let cuerpo = rut.slice(0, -1);
        let dv = rut.slice(-1); 

        if (cuerpo.length > 0) {
            cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            rut = cuerpo + (dv ? "-" + dv : "");
        }
        input.value = rut; 
    }
});

document.querySelectorAll('.del-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var ID = btn.getAttribute('data-sup-id');
        document.getElementById('idTr').value = ID;
        document.getElementById('idDesv').value = ID;
    });
});

document.addEventListener("DOMContentLoaded", function() {
    let fechaInput = document.getElementById("fechaSoli");
    let hoy = new Date().toISOString().split("T")[0]; 
    fechaInput.value = hoy;
});

document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("select.search-form").forEach(selectElement => {
        const choices = new Choices(selectElement, {
            searchEnabled: true,
            itemSelectText: "",
            placeholder: true
        });
    });
});

document.querySelectorAll(".estado-select").forEach(select => {
    select.addEventListener("change", function() {
        let trasladoId = this.dataset.id;
        let nuevoEstado = this.value;

        console.log(trasladoId);
        console.log(nuevoEstado)
        fetch("assets/php/actualizar_traslado.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${trasladoId}&estado=${nuevoEstado}`
        })
        .then(response => response.text())
        .then(data => {
            if (data === "OK") {
                alert("Estado actualizado correctamente");
            } else {
                alert("Error al actualizar el estado");
            }
        });
    });
});
document.querySelectorAll(".desv-select").forEach(select => {
    select.addEventListener("change", function() {
        let desvId = this.dataset.id;
        let nuevoEstado = this.value;

        console.log(desvId);
        console.log(nuevoEstado)
        fetch("assets/php/actualizar_desv.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${desvId}&estado=${nuevoEstado}`
        })
        .then(response => response.text())
        .then(data => {
            if (data === "OK") {
                alert("Estado actualizado correctamente");
            } else {
                alert("Error al actualizar el estado");
            }
        });
    });
});