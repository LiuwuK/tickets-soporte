document.addEventListener('DOMContentLoaded', function() {
    const filtros = {
        tipo: document.getElementById('filtroTipo'),
        estado: document.getElementById('filtroEstado'),
        fechaInicio: document.getElementById('filtroFechaInicio'),
        fechaFin: document.getElementById('filtroFechaFin')
    };

    // Event listeners para todos los filtros
    Object.values(filtros).forEach(filter => {
        filter.addEventListener('change', actualizarResultados);
    });

    function actualizarResultados() {
        const tipo = filtros.tipo.value.toLowerCase();
        const estado = filtros.estado.value.toLowerCase();
        const fechaInicio = Date.parse(filtros.fechaInicio.value) || 0;
        const fechaFin = Date.parse(filtros.fechaFin.value) || Infinity;

        const resultados = historicoData.filter(item => {
            const itemFecha = Date.parse(item.fecha);
            return (
                (tipo === '' || item.tipo.toLowerCase() === tipo) &&
                (estado === '' || item.estado.toLowerCase() === estado) &&
                (itemFecha >= fechaInicio) &&
                (itemFecha <= fechaFin)
            );
        });

        mostrarResultados(resultados);
    }

    function mostrarResultados(data) {
      const contenedor = document.getElementById('resultadoHistorico');
      contenedor.innerHTML = ''; 
      data.forEach(item => {
          const fechaFormateada = new Date(item.fecha).toLocaleDateString('es-CL', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric'
          });

          
        const itemHTML = `
            <div class="h-container"  onclick="window.location.href='detalle-historico.php?id=${item.id}&tipo=${item.tipo}';">
                <div class="h-header d-flex justify-content-between">
                    <div class="colab">
                        <strong>${item.colaborador}</strong>
                    </div>
                    <div class="estado mt-2">
                        <span class="label label-estado">${item.estado}</span>
                    </div> 
                </div>
                <div class="h-body">
                    <p>Fecha: ${fechaFormateada}</p>
                    <p>Tipo: ${item.tipo}</p>
                    <p>Creado por: ${item.solicitante}</p>
                    <p>Observación SSPP: ${item.observacion ? item.observacion : 'No hay observación'}</p>
                    <p>Observación RRHH: ${item.obs_rrhh ? item.obs_rrhh : 'No hay observación'}</p>
                </div>
            </div>
        `;

        contenedor.insertAdjacentHTML('beforeend', itemHTML);
      });
    }
    actualizarResultados();
});