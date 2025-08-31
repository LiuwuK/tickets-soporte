document.getElementById('filtroSemanaActual').addEventListener('change', function(e) {
    const inicio = document.getElementById('filtroFechaInicio');
    const fin = document.getElementById('filtroFechaFin');
    if (e.target.checked) {
        inicio.disabled = true;
        fin.disabled = true;
        inicio.value = '';
        fin.value = '';
    } else {
        inicio.disabled = false;
        fin.disabled = false;
    }
});
document.getElementById('filtrosForm').addEventListener('submit', function() {
    document.getElementById('paginaInput').value = 1;
});