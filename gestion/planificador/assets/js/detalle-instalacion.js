//TURNOS-------------------------------------------------------------------------------------------------------------
const btnGuardar = document.querySelector('#formTurnos button[type="submit"]');
const formTurnos = document.getElementById('formTurnos');

// Bloquear botón inicialmente
if (btnGuardar) btnGuardar.disabled = true;

function habilitarGuardar() {
  if (btnGuardar) btnGuardar.disabled = false;
}
formTurnos.querySelectorAll('input, select').forEach(el => {
  el.addEventListener('input', habilitarGuardar);
  el.addEventListener('change', habilitarGuardar);
});

document.getElementById('btn-agregar-turno')?.addEventListener('click', () => {
  agregarTurno();
  habilitarGuardar();
});


formTurnos.addEventListener('submit', async function(e) {
  e.preventDefault();

  document.querySelectorAll('#plantilla-fila input, #plantilla-fila select').forEach(input => input.disabled = true);
  
  if (this.dataset.submitting === "true") return;
  this.dataset.submitting = "true";

  try {
    // Validar formulario
    if (!validarFormularioTurnos()) {
      this.dataset.submitting = "false";
      return;
    }

    // Recolectar datos del formulario
    const formData = new FormData(this);
    
    // Recolectar IDs de turnos eliminados
    const turnosEliminados = Array.from(document.querySelectorAll('tr[data-turno-id]'))
      .filter(tr => tr.style.display === 'none')
      .map(tr => tr.dataset.turnoId);
    
    turnosEliminados.forEach(id => {
      formData.append('turnos_eliminados[]', id);
    });

    console.log('Enviando datos:', Object.fromEntries(formData.entries()));

    const response = await fetch(this.action, {
      method: 'POST',
      body: formData
    });

    // Manejar respuesta del servidor
    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }

    const data = await response.json();
    console.log('Respuesta del servidor:', data);

    if (data.success) {
      alert(data.message || 'Turnos guardados correctamente');
      if (data.redirect) {
        window.location.href = data.redirect;
      } else {
        location.reload();
      }
    } else {
      throw new Error(data.message || 'Error al guardar los turnos');
    }
  } catch (error) {
    console.error('Error:', error);
    alert(error.message || 'Ocurrió un error al guardar los turnos');
  } finally {
    this.dataset.submitting = "false";
  }
});

function validarFormularioTurnos() {
  let valido = true;
  const filas = document.querySelectorAll('#cuerpo-tabla tr[data-turno-id], #cuerpo-tabla tr:not(#plantilla-fila):has(input[name*="[nombre]"])');
  
  filas.forEach((fila, index) => {
    if (fila.style.display === 'none') return;

    // Validar nombre
    const nombre = fila.querySelector('input[name*="[nombre]"]');
    if (!nombre?.value.trim()) {
      alert(`Por favor ingrese el nombre del turno en la fila ${index + 1}`);
      nombre?.focus();
      valido = false;
      return;
    }

    // Validar jornada
    const jornada = fila.querySelector('select[name*="[jornada_id]"]');
    if (!jornada?.value) {
      alert(`Por favor seleccione una jornada en la fila ${index + 1}`);
      jornada?.focus();
      valido = false;
      return;
    }

    // Validar horarios
    const dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
    for (const dia of dias) {
      const entrada = fila.querySelector(`input[name*="[dias][${dia}][entrada]"]`);
      const salida = fila.querySelector(`input[name*="[dias][${dia}][salida]"]`);
      
      if (!entrada || !salida) continue;
      
      if (entrada.value && !salida.value) {
        alert(`Por favor ingrese la hora de salida para ${dia} en la fila ${index + 1}`);
        salida.focus();
        valido = false;
        return;
      }
      
      if (!entrada.value && salida.value) {
        alert(`Por favor ingrese la hora de entrada para ${dia} en la fila ${index + 1}`);
        entrada.focus();
        valido = false;
        return;
      }
      
     
    }
  });
  
  return valido;
}

function agregarTurno() {
  const tbody = document.getElementById('cuerpo-tabla');
  const plantilla = document.getElementById('plantilla-fila');
  const nuevaFila = plantilla.cloneNode(true);
  
  nuevaFila.style.display = '';
  nuevaFila.removeAttribute('id');

  // Actualizar los nombres de los campos para evitar colisiones
  const inputs = nuevaFila.querySelectorAll('[name]');
  inputs.forEach(input => {
    const originalName = input.name;
    const newName = originalName.replace('nuevos_turnos[]', `nuevos_turnos[${contadorNuevos}]`);
    input.name = newName;
    input.required = false;
  });

  tbody.insertBefore(nuevaFila, plantilla);
  contadorNuevos++;
  actualizarBotonesEliminar();
  
  // Enfocar el primer campo de la nueva fila
  const primerInput = nuevaFila.querySelector('input');
  if (primerInput) primerInput.focus();
}

function eliminarTurno(boton) {
  const fila = boton.closest('tr');
  const filasVisibles = document.querySelectorAll('#cuerpo-tabla tr:not(#plantilla-fila)').length;
  
  if (filasVisibles <= 1) {
    alert('Debe haber al menos un turno');
    return;
  }
  
  const turnoId = fila.dataset.turnoId;
  if (turnoId && !confirm('¿Está seguro de eliminar este turno?')) {
    return;
  }
  
  fila.remove();
  actualizarBotonesEliminar();
}

function actualizarBotonesEliminar() {
  const filasVisibles = document.querySelectorAll('#cuerpo-tabla tr:not(#plantilla-fila)').length;
  const botones = document.querySelectorAll('#cuerpo-tabla button.btn-danger');
  
  botones.forEach(boton => {
    boton.disabled = filasVisibles <= 1;
  });
}

//ASIGNAR FECHAS PARA EL TURNO 
/// Asignar evento a los botones "Asignar fecha"
document.querySelectorAll('.btn-dates').forEach(btn => {
  btn.addEventListener('click', function() {
    const fila = this.closest('tr');
    const turnoId = fila.dataset.turnoId; 
    
    // Obtener los horarios por día desde la tabla
    const tablasHorarios = fila.querySelectorAll('td table');
    if (tablasHorarios.length !== 2) {
      console.warn('No se encontraron ambas tablas de horario.');
      return;
    }

    const tablaEntradas = tablasHorarios[0];
    const tablaSalidas = tablasHorarios[1];

    const filasEntradas = tablaEntradas.querySelectorAll('tr');
    const filasSalidas = tablaSalidas.querySelectorAll('tr');

    const horariosPorDia = {};
    
    filasEntradas.forEach((filaDia, index) => {
      const celdasEntrada = filaDia.querySelectorAll('td');
      const celdasSalida = filasSalidas[index].querySelectorAll('td');

      const diaTexto = celdasEntrada[0].textContent.trim().toLowerCase();
      const dia = diaTexto
        .replace(':', '')                          
        .replace(/\s+/g, '');

      const entradaInput = celdasEntrada[1].querySelector('input[type="time"]');
      const salidaInput = celdasSalida[0].querySelector('input[type="time"]');

      const entrada = entradaInput?.value || '-';
      const salida = salidaInput?.value || '-';
      //console.log(`Día: ${dia}, Entrada: ${entrada}, Salida: ${salida}`);
      if (entrada !== '-' && salida !== '-') {
        horariosPorDia[dia] = { entrada, salida };
      }
    });
    
    document.getElementById('turnoId').value = turnoId;
    document.getElementById('patronJornada').value = fila.dataset.jornada;
    // Llenar tabla de horarios en el modal
    const tbody = document.getElementById('horariosDias');
    tbody.innerHTML = '';

    const diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];

    diasSemana.forEach(dia => {
      const horario = horariosPorDia[dia] || { entrada: '', salida: '' };

      if (horario.entrada && horario.salida) {
        const tr = document.createElement('tr');

        tr.innerHTML = `
          <td>${dia.charAt(0).toUpperCase() + dia.slice(1)}</td>
          <td>${horario.entrada}</td>
          <td>${horario.salida}</td>
          <td>
            <div class="form-check form-switch">
              <input class="form-check-input dia-checkbox" type="checkbox" data-dia="${dia}" checked>
            </div>
          </td>
        `;

        tbody.appendChild(tr);
      }
    });
    
    // Configurar fechas por defecto
    const hoy = new Date();
    document.getElementById('fechaInicio').valueAsDate = hoy;
    
    const fin = new Date();
    fin.setMonth(fin.getMonth() + 1);
    document.getElementById('fechaTermino').valueAsDate = fin;
    
    const modal = new bootstrap.Modal(document.getElementById('modalHorario'));
    modal.show();
  });
});

// Generar turnos para la instalacion (boton Asignar fecha)
document.getElementById('guardarHorario').addEventListener('click', async function() {
  const btn = this;
  try {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

    const form = document.getElementById('formHorario');
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    const fechaInicio = new Date(document.getElementById('fechaInicio').value);
    const fechaTermino = new Date(document.getElementById('fechaTermino').value);
    if (fechaTermino < fechaInicio) {
      alert('La fecha de término no puede ser anterior a la de inicio');
      return;
    }

    // Recolectar días seleccionados y sus horarios
    const horariosDias = {};
    document.querySelectorAll('#horariosDias tr').forEach(tr => {
      const dia = tr.querySelector('.dia-checkbox').dataset.dia;
      const aplica = tr.querySelector('.dia-checkbox').checked;
      
      if (aplica) {
        const celdas = tr.cells;
        horariosDias[dia] = {
          entrada: celdas[1].textContent.trim(),
          salida: celdas[2].textContent.trim()
        };
      }
    });

    if (Object.keys(horariosDias).length === 0) {
      alert('Debe seleccionar al menos un día para aplicar el horario');
      return;
    }

    const datos = {
      sucursal_id: document.getElementById('sucursalId').value,
      turno_id: document.getElementById('turnoId').value,
      patron_jornada: document.getElementById('patronJornada').value,
      fecha_inicio: document.getElementById('fechaInicio').value,
      fecha_fin: document.getElementById('fechaTermino').value,
      bloque: document.getElementById('bloqueSelect').value,
      horarios: horariosDias
    };

    const response = await fetch('assets/php/test.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(datos)
    });

    const data = await response.json();
    console.log(datos)
    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Error al guardar el horario');
    }
    alert(data.message);
    if (calendar) {
      calendar.refetchEvents();
    } else {
      window.location.reload();
    }
    bootstrap.Modal.getInstance(document.getElementById('modalHorario')).hide();
  } catch (error) {
    console.error('Error:', error);
    alert(`Error: ${error.message}`);
  } finally {
    btn.disabled = false;
    btn.innerHTML = 'Guardar Horario';
  }
});

//FIN TURNOS------------------------------------------------------------------------------------------------------------------
//CALENDARIO
document.addEventListener('DOMContentLoaded', function() {
  const sucursalId = document.getElementById('sucursalId').value;
  const calendarEl = document.getElementById('calendar');
  const filtroColaborador = document.getElementById('filtroColaborador');
  
  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    headerToolbar: {
      left: 'prev,next',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: { 
      month: 'Mes',
      week: 'Semana',
      day: 'Día'
    },
    displayEventTime: false,
    events: function(fetchInfo, successCallback, failureCallback) {
      let url = `assets/php/listar-horarios.php?sucursal_id=${sucursalId}`;
      
      if (filtroColaborador.value) {
        url += `&colaborador_id=${filtroColaborador.value}`;
      }
      
      fetch(url)
      .then(response => response.json())
      .then(data => successCallback(data))
      .catch(error => failureCallback(error));
    },
    dateClick: function(info) {
      alert('Fecha clickeada: ' + info.dateStr);
    },
    eventClick: function(info) {
      const colaboradores = info.event.extendedProps.colaboradores || 'Sin asignar';
      const titulo = info.event.title;
      alert(`Turno: ${titulo}\nColaboradores: ${colaboradores}`);
    },
    eventDisplay: 'block',
    eventOrder: 'groupId'
  });
  calendar.render();
    filtroColaborador.addEventListener('change', function() {
    calendar.refetchEvents();
  });
});



// DESCARGAS------------------------------------------------------------------------------------------------------------------
//descargas
document.querySelector('.excel-btn').addEventListener('click', function () {
  descargarCalendario('excel');
});

document.querySelector('.pdf-btn').addEventListener('click', function () {
  descargarCalendario('pdf');
});

function descargarCalendario(formato) {
  const sucursalId = document.getElementById('sucursalId').value; 
  const colaboradorId = document.getElementById('filtroColaborador').value;
  const view = calendar.view;
  const startDate = view.currentStart;
  const mes = startDate.getMonth() + 1;
  const anio = startDate.getFullYear();
  const params = new URLSearchParams({
    formato,
    sucursal_id: sucursalId,
    colaborador_id: colaboradorId,
    mes,
    anio
  });
  
  fetch('assets/php/descargar-calendario.php?' + params.toString())
    .then(res => res.blob())
    .then(blob => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `calendario_${mes}_${anio}.${formato === 'excel' ? 'xlsx' : 'pdf'}`;
      document.body.appendChild(a);
      a.click();
      a.remove();
    });
}