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

});