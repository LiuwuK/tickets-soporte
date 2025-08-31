// Botón Rechazar
document.addEventListener('click', function(e){
    if(e.target.classList.contains('den-btn')){
        document.getElementById('idDen').value = e.target.dataset.supId;
    }
});
