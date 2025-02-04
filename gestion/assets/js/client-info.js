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