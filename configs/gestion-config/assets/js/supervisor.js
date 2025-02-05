//limitar numero a 9 datos
document.addEventListener("input", function (e) {
    if (e.target.matches("input[name='numeroC[]']")) {
        if (e.target.value.length > 9) {
            e.target.value = e.target.value.slice(0, 9);
        }
    }
});
//formatear rut de la tabla
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
