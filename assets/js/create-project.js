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
  const fecha = document.getElementById('fechaActividad').value.trim();
  const descripcion = document.getElementById('descripcionActividad').value.trim();
  const fechaF = formatearFecha(fecha);

  const nuevaActividad = document.createElement('li');
  nuevaActividad.className = 'list-group-item';
  nuevaActividad.innerHTML = `
    <h6>${nombre} -- ${fechaF}</h6>
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
  const fechaFormateada = date.toLocaleDateString('es-ES', opciones);
  
  return fechaFormateada.replace(/de (\d{4})$/, 'del $1');
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
document.querySelector('form').addEventListener('submit', function() {
  document.querySelectorAll('[disabled]').forEach(el => el.disabled = false);
});
