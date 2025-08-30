document.addEventListener('DOMContentLoaded', function () {
    const filtros = {
        supervisor: document.getElementById('filtroSupervisor'),
        texto: document.getElementById('filtroTexto'),
        cc: document.getElementById('filtroCentro')
    };

    const form = document.getElementById('filtrosForm');
    const paginaInput = document.getElementById('paginaInput');

    // Mostrar resultados (solo para la página actual)
    function mostrarResultados() {
        const contenedor = document.getElementById('resultadoSucursal');
        contenedor.innerHTML = '';

        if (sucursalData.length === 0) {
            contenedor.innerHTML = '<div class="alert alert-info">No se encontraron sucursales</div>';
            return;
        }

        sucursalData.forEach(item => {
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

    // Inicializar
    mostrarResultados();

    // Cuando se cambia cualquier filtro, resetear a página 1
    Object.values(filtros).forEach(filtro => {
        if (filtro) {
            filtro.addEventListener('change', function() {
                paginaInput.value = 1;
                form.submit();
            });
        }
    });

    // Debounce para búsqueda de texto
    let timeout;
    filtros.texto.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            paginaInput.value = 1;
            form.submit();
        }, 500);
    });
});