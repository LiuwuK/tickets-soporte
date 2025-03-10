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

const descField = document.getElementById('descRRHH');
const updateBtn = document.getElementById('updateBtn');

function toggleButtonState() {
    if (descField.value.trim() !== '') {
        updateBtn.disabled = false; 
    } else {
        updateBtn.disabled = true;
    }
}

descField.addEventListener('input', toggleButtonState);
toggleButtonState();