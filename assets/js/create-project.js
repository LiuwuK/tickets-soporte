
const checkboxes = document.querySelectorAll('.expenses input[type="checkbox"]');
const selectElement = document.getElementById('pClass');
const classInfo = document.getElementById('classInfo');

//select y contenedor de licitacion y datos de contacto
const selectPtype = document.getElementById('pType');
const licTitle = document.getElementById('licitacionT');
const licMain = document.getElementById('licitacion');

const contTitle = document.getElementById('contactoT');
const contMain = document.getElementById('contacto');
//Mostrar campos software y hardware si la clasificacion es tecnología
selectElement.addEventListener('change', function() {
  if (this.value == 1) {
      classInfo.style.display = '';
  } else {
      classInfo.style.display = 'none'; 
  }
});

//Mostrar datos de licitacion/contacto
selectPtype.addEventListener('change', function(){
  if (this.value == 1){
    licMain.style.display = '';
    licTitle.style.display = '';
    contTitle.style.display = 'none';
    contMain.style.display = 'none';
  } else if (this.value == 2){
    licMain.style.display = 'none';
    licTitle.style.display = 'none';
    contTitle.style.display = '';
    contMain.style.display = '';
  } else {
    contMain.style.display = 'none';
    contTitle.style.display = 'none';
    licTitle.style.display = 'none';
    licMain.style.display =   'none';
  }

});


//Mostrar campos correspondientes al check seleccionado
checkboxes.forEach((checkbox) => {
  checkbox.addEventListener('change', (event) => {
    const inputId = `${event.target.id}-input`; 
    const relatedInput = document.getElementById(inputId);
    if (event.target.checked) {
      relatedInput.classList.remove('hidden');
    } else {
      relatedInput.classList.add('hidden');
    }
  });
});



// actividades 
document.getElementById('formActividad').addEventListener('submit', function (e) {
  e.preventDefault(); 

  const nombre = document.getElementById('nombreActividad').value.trim();
  const fecha = document.getElementById('fechaActividad').value.trim();
  const descripcion = document.getElementById('descripcionActividad').value.trim();
  const fechaF = formatearFecha(fecha);

  const nuevaActividad = document.createElement('li');
  nuevaActividad.className = 'list-group-item';
  nuevaActividad.innerHTML = `
    <h6>${nombre}</h6>
    <p>${fechaF}</p>
    <p>${descripcion}</p>
    <input type="hidden" name="actividades[nombre][]" value="${nombre}">
    <input type="hidden" name="actividades[fecha][]" value="${fecha}">
    <input type="hidden" name="actividades[descripcion][]" value="${descripcion}">
  `;
  document.getElementById('listadoActividades').appendChild(nuevaActividad);
  document.getElementById('formActividad').reset();
  const modalElement = document.getElementById('actividadModal');
  const modalInstance = bootstrap.Modal.getInstance(modalElement);
  modalInstance.hide();
});

function formatearFecha(fecha) {
  const partes = fecha.split('-');
  const date = new Date(Date.UTC(partes[0], partes[1] - 1, partes[2]));

  const opciones = { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'UTC' };
  return date.toLocaleDateString('es-ES', opciones);
}
