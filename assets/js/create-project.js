
const checkboxes = document.querySelectorAll('.expenses input[type="checkbox"]');
const selectElement = document.getElementById('pClass');
const classInfo = document.getElementById('classInfo');

//Mostrar campos software y hardware si la clasificacion es tecnología
selectElement.addEventListener('change', function() {
  if (this.value == 1) {
      classInfo.style.display = '';
  } else {
      classInfo.style.display = 'none'; 
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
    <strong>${nombre}</strong>
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
