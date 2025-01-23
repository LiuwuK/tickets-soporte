// Configuración grafico total proyectos registrados 
	const configProyect = {
	   type: 'line',
	   data: {
		   labels: meses, 
		   datasets: datasets 
	   },
	   options: {
		   responsive: true,
		   plugins: {
			   legend: {
				   position: 'top'
			   },
			   tooltip: {
				   mode: 'index',
				   intersect: false
			   }
		   },
		   interaction: {
			   mode: 'index',
			   intersect: false
		   },
		   scales: {
			   x: {
				   title: {
					   display: true,
					   text: 'Meses'
				   }
			   },
			   y: {
				   max: max,
				   ticks: {
				   callback: function (value, index, values) {
					   const totalLabels = values.length;
					   const step = Math.ceil(totalLabels / 6); // Divide en 6 bloques (ajustable)
					   if (index % step === 0 || index === totalLabels - 1) {
						   return this.getLabelForValue(value);
					   }
					   return null; // Ocultar etiqueta
				   }
			   },
				   title: {
					   display: true,
					   text: 'Total de Proyectos'
				   },
				   beginAtZero: true
			   }
		   }
		 }
	}
// 	Grafico total de proyectos generados--------------------------------------------------------------
	const transformarMes = (numeroMes) => {
	const date = new Date(2025, numeroMes - 1); // Crear fecha con el mes (restar 1 porque enero = 0)
	return new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(date);
	};
	// Aplicar la transformación a todo el array
	const mesesT = mp.map(transformarMes);
	const configCount = {
		type: 'bar',
		data: {
			labels: mesesT,
			datasets: datap 
		},
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Mes'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Monto'
                    },
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString(); 
                        }
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: $${context.raw.toLocaleString()}`;
                        }
                    }
                }
            }
        }
    }
//Grafico PIE total monto proyectos por vertical-----------------------------------------------------
    const configTotal = {
        type: 'doughnut',
        data: {
            labels: tProjects,
            datasets: [{
                data: tProjectsData,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const formattedValue = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD',minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
                            return `${context.label}: ${formattedValue}`;
                        }
                    }
                }
            }
        }
    };

// Renderizar gráficos-------------------------------------------------------------------------------
window.onload = function() {
	//grafico total proyectos registrados
	const totalRegistrados = document.getElementById('lineTotalProjects').getContext('2d');
	new Chart(totalRegistrados, configProyect);
	//grafico total monto
	const projectTotal = document.getElementById('totalProjects').getContext('2d');
	new Chart(projectTotal, configTotal);
	//grafico cantidad proyectos
	const projects = document.getElementById('projects').getContext('2d');
	new Chart(projects, configCount);
};