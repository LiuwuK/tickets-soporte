//TURNOS---------------------------------------------------------------------------------------------------------------
document.getElementById('formTurnos')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  console.log('SUBMIT ejecutado');
  if (this.dataset.submitting === "true") return;
  this.dataset.submitting = "true";

  try {
    const formData = new FormData(this);
    const response = await fetch(this.action, {
      method: 'POST',
      body: formData
    });

    if (response.redirected) {
      window.location.href = response.url;
    } else {
      const data = await response.json();
      if (data.success) {
        alert('Turnos guardados correctamente');
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Ocurrió un error al guardar los turnos');
  } finally {
    this.dataset.submitting = "false";
  }
});

function agregarTurno() {
  const tbody = document.getElementById('cuerpo-tabla');
  const plantilla = document.getElementById('plantilla-fila');
  const nuevaFila = plantilla.cloneNode(true);
  nuevaFila.style.display = '';
  nuevaFila.removeAttribute('id');

  const inputs = nuevaFila.querySelectorAll('[name]');
  inputs.forEach(input => {
    const originalName = input.name;
    const newName = originalName.replace('nuevos_turnos[]', `turnos[${contadorNuevos}]`);
    input.name = newName;
    input.required = false; 
  });

  tbody.insertBefore(nuevaFila, plantilla);
  contadorNuevos++;
  actualizarBotonesEliminar();
}
function eliminarTurno(boton) {
    const fila = boton.closest('tr');
    const filasVisibles = document.querySelectorAll('#cuerpo-tabla tr:not(#plantilla-fila)').length;
    
    if (filasVisibles <= 1) {
        alert('Debe haber al menos un turno');
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
    const horariosPorDia = {};
    const filasDias = fila.querySelectorAll('table tbody tr');
    
    filasDias.forEach(filaDia => {
      const dia = filaDia.cells[0].textContent.trim().toLowerCase();
      const entrada = filaDia.cells[1].textContent.trim();
      const salida = filaDia.cells[2].textContent.trim();
      
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
      const tr = document.createElement('tr');
      const horario = horariosPorDia[dia] || { entrada: '', salida: '' };
      
      tr.innerHTML = `
        <td>${dia.charAt(0).toUpperCase() + dia.slice(1)}</td>
        <td>${horario.entrada || '-'}</td>
        <td>${horario.salida || '-'}</td>
        <td>
          <div class="form-check form-switch">
            <input class="form-check-input dia-checkbox" type="checkbox" data-dia="${dia}" ${horario.entrada ? 'checked' : ''}>
          </div>
        </td>
      `;
      
      tbody.appendChild(tr);
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