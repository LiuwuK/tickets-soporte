//checkbox para mostrar/ocultar campos de software y hardware
const checkboxes = document.querySelectorAll('.expenses input[type="checkbox"]');
const selectElement = document.getElementById('pClass');
const classInfo = document.getElementById('classInfo');

//select y contenedor de licitacion y datos de contacto
const selectPtype = document.getElementById('pType');
const licTitle = document.getElementById('licitacionT');
const licMain = document.getElementById('licitacion');

const contTitle = document.getElementById('contactoT');
const contMain = document.getElementById('contacto');

//Sumar total de materiales
let sumaTotal = 0; 
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
  const fechaInicio = document.getElementById('fechaInicio').value.trim();
  const fechaTermino = document.getElementById('fechaTermino').value.trim();
  const descripcion = document.getElementById('descripcionActividad').value.trim();
  const area = document.getElementById('areaAct').value.trim();
  const fechaF = formatearFecha(fechaInicio,fechaTermino);

  const nuevaActividad = document.createElement('li');
  nuevaActividad.className = 'list-group-item';
  nuevaActividad.innerHTML = `
    <h6>${nombre}</h6>
    <p>${fechaF}</p>
    <p>${descripcion}</p>
    <input type="hidden" name="actividades[nombre][]" value="${nombre}">
    <input type="hidden" name="actividades[fechaInicio][]" value="${fechaInicio}">
    <input type="hidden" name="actividades[fechaTermino][]" value="${fechaTermino}">
    <input type="hidden" name="actividades[descripcion][]" value="${descripcion}">
    <input type="hidden" name="actividades[area][]" value="${area}">
  `;
  document.getElementById('listadoActividades').appendChild(nuevaActividad);
  document.getElementById('formActividad').reset();
  const modalElement = document.getElementById('actividadModal');
  const modalInstance = bootstrap.Modal.getInstance(modalElement);
  modalInstance.hide();
});

function formatearFecha(fechaI, fechaT) {
  const partesI = fechaI.split('T');
  const datePartI = partesI[0].split('-');
  const timePartI = partesI[1].split(':');

  const partesT = fechaT.split('T');
  const datePartT = partesT[0].split('-');
  const timePartT = partesT[1].split(':');

  const startDate = new Date(Date.UTC(datePartI[0], datePartI[1] - 1, datePartI[2], timePartI[0], timePartI[1]));
  const endDate = new Date(Date.UTC(datePartT[0], datePartT[1] - 1, datePartT[2], timePartT[0], timePartT[1]));


  const opciones = { 
    month: 'long', 
    day: 'numeric', 
    hour: 'numeric', 
    minute: 'numeric',
    timeZone: 'UTC'
  };

  const fechaInicioFormateada = startDate.toLocaleDateString('es-ES', opciones);
  const fechaTerminoFormateada = endDate.toLocaleDateString('es-ES', opciones);

  return `${fechaInicioFormateada.replace(/de (\d{4})$/, 'del $1')} - ${fechaTerminoFormateada.replace(/de (\d{4})$/, 'del $1')}`;
}

//BOM
// Calculo de materiales ya cargados
document.querySelectorAll('#materialContainer .material-item').forEach((item) => {
  const total = parseFloat(item.querySelector('div:nth-child(4)').textContent.replace(/[^0-9.-]+/g, ''));
  sumaTotal += total;
});
actualizarSumaTotal();

// Agregar evento para el formulario BOM
document.getElementById('formBom').addEventListener('submit', function (e) {
  e.preventDefault();
  // Material container main
  const materialContainer = document.querySelector('#materialContainer'); 

  // Datos del material
  const nombre = document.getElementById('nombreMaterial').value.trim();
  const cantidad = parseInt(document.getElementById('cantidadMaterial').value.trim(), 10);
  const total = parseFloat(document.getElementById('totalMaterial').value.trim());
  // Formatear total
  const totalFormatted = new Intl.NumberFormat('en-US', { 
    style: 'currency', 
    currency: 'USD', 
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(total);

  // Actualizar suma total
  sumaTotal += total;
  actualizarSumaTotal();

  // Crear nuevo material y agregarlo al contenedor
  let nuevoMaterial = document.createElement('div');
  nuevoMaterial.className = 'material-item list-group-item';
  nuevoMaterial.innerHTML = `
      <div>${nombre}</div>
      <div><i class="bi bi-x-lg"></i></div>
      <div>${cantidad}</div>
      <div>${totalFormatted}</div>
      <input type="hidden" name="material[nombre][]" value="${nombre}">
      <input type="hidden" name="material[cantidad][]" value="${cantidad}">
      <input type="hidden" name="material[total][]" value="${total}">
  `;
  materialContainer.style.display = 'flex';
  materialContainer.appendChild(nuevoMaterial);

  document.getElementById('formBom').reset();
  const modalElement = document.getElementById('modalBom');
  const modalInstance = bootstrap.Modal.getInstance(modalElement);
  modalInstance.hide();
});

//Agregar costo de materiales al div
function actualizarSumaTotal() {
  const totalFormatted = new Intl.NumberFormat('en-US', { 
    style: 'currency', 
    currency: 'USD', 
    minimumFractionDigits: 0,
    maximumFractionDigits: 0 
  }).format(sumaTotal);

  let totalContainer = document.querySelector('#sumaTotal');
  if (!totalContainer) {
    totalContainer = document.createElement('div');
    totalContainer.id = 'sumaTotal';
    totalContainer.className = 'mt-3 font-weight-bold';
    document.querySelector('#materialContainer').appendChild(totalContainer);
  }

  totalContainer.innerHTML = `Total: ${totalFormatted}`;
}

//activar selects para actualizar proyecto
document.getElementById('updtProject').addEventListener('submit', function (e) {
  const disabledFields = this.querySelectorAll('[disabled]');
  disabledFields.forEach(field => field.removeAttribute('disabled'));
});




