function mostrarFormulario(id) {
    // Oculta ambos formularios
    document.getElementById('trasladoForm').style.display = 'none';
    document.getElementById('desvinculacionForm').style.display = 'none';

    // Muestra solo el seleccionado
    document.getElementById(id).style.display = 'block';
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
