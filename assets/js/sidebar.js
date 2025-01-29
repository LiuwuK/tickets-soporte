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
//boton para ocultar/ver sidebar
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.toggle('expanded');
    overlay.classList.toggle('visible');
}

document.querySelector('.sidebar-overlay').addEventListener('click', toggleSidebar);
document.querySelector('.sidebar-toggle').addEventListener('click', toggleSidebar);