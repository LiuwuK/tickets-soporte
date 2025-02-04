//contactos
document.getElementById('formContacto').addEventListener('submit', function (e) {
    e.preventDefault(); 

    const nombre = document.getElementById('cName').value.trim();
    const email = document.getElementById('cEmail').value.trim();
    const cargo = document.getElementById('cargo').value.trim();
    const contacto = document.getElementById('cNumero').value.trim();
    

    const nuevoContacto = document.createElement('div');
    nuevoContacto.className = 'card mt-3 p-2';
    nuevoContacto.innerHTML = `
        <div class="d-flex justify-content-evenly">
        <div class="cinfo">
            <strong class="ml-3" >Nombre</strong> 
            <p>${nombre}</p>
        </div>  
        <div class="cinfo">
            <strong class="ml-3" >Cargo</strong> 
            <p> ${cargo}</p>
        </div> 
        <div class="cinfo">
            <strong class="ml-3" >Email</strong> 
            <p> ${email}</p>
        </div> 
        <div class="cinfo">
            <strong class="ml-3" >Numero de Contacto</strong> 
            <p> ${contacto}</p>
        </div> 
        </div>
        
        <input type="hidden" name="contacto[nombre][]" value="${nombre}">
        <input type="hidden" name="contacto[email][]" value="${email}">
        <input type="hidden" name="contacto[cargo][]" value="${cargo}">
        <input type="hidden" name="contacto[contacto][]" value="${contacto}">
    `;
    document.getElementById('contacto').appendChild(nuevoContacto);
    document.getElementById('formContacto').reset();
    const modalElement = document.getElementById('contactoModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    modalInstance.hide();
});