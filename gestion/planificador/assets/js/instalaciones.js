document.addEventListener('DOMContentLoaded', function () {
    const filtros = {
        texto: document.getElementById('filtroTexto') 
    };

    Object.values(filtros).forEach(filtro => {
        filtro.addEventListener('change', actualizarResultados);
    });

    //cambios en el campo de búsqueda
    filtros.texto.addEventListener('input', actualizarResultados);

    function actualizarResultados() {
        //const estado = filtros.estado.value.toLowerCase();
        const texto = filtros.texto.value.toLowerCase();

        const resultados = sucursalData.filter(item => {

            // filtro texto coincide con algún campo
            const coincideTexto = (
                texto === '' || 
                item.nombre.toLowerCase().includes(texto) ||
                item.nSup.toLowerCase().includes(texto) ||
                item.nCiudad.toLowerCase().includes(texto) ||
                item.estado.toLowerCase().includes(texto) ||
                item.cost_center.toLowerCase().includes(texto)
            );

         

            /* filtros de tipo y estado
            const coincideTipoYEstado = (
                (tipo === '' || item.tipo.toLowerCase() === tipo) &&
                (estado === '' || item.estado.toLowerCase() === estado)
            );
            */
            // Aplicar todos los filtros
            return coincideTexto ;
        });

        mostrarResultados(resultados);
    }

    function mostrarResultados(data) {
        const contenedor = document.getElementById('resultadoSucursal');
        contenedor.innerHTML = '';
        data.forEach(item => {

            const itemHTML = `
                <div class="h-container" onclick="window.location.href='detalle-instalacion.php?id=${item.id}';">
                    <div class="h-header d-flex justify-content-between">
                        <div class="colab">
                            <strong>${item.nombre}</strong>
                           <p>Razón Social: ${item.razon_social ? item.razon_social : 'Sin definir'}</p>
                           <p>Centro de Costos: ${item.cost_center} </p>
                        </div>
                        <div class="estado mt-2">
                            <span class="label label-estado" style="text-transform: capitalize;">${item.estado}</span>
                        </div>
                    </div>
                     <div class="h-body">
                        <p>Supervisor: ${item.nSup}</p>
                        <p>Ciudad: ${item.nCiudad} </p>
                    </div>
                </div>
            `;

            contenedor.insertAdjacentHTML('beforeend', itemHTML);
        });
    }

    // Ejecutar la función al cargar la página
    actualizarResultados();
});