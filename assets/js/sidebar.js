//Inicializar tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

//animacion de carga 
document.querySelectorAll("form[name='form']").forEach(form => {
    form.addEventListener("submit", function() {
        document.getElementById("loading").style.display = "flex";
    });
});