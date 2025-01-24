const transformarMes = (numeroMes) => {
	const date = new Date(2025, numeroMes - 1); // Crear fecha con el mes (restar 1 porque enero = 0)
	return new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(date);
};
const mesesT = mp.map(transformarMes);

//si el grafico esta vacio muestra un mensaje 
const emptyDataPlugin = {
    id: 'emptyDataMessage',
    beforeDraw: function(chart) {
        const datasets = chart.data.datasets;
        const hasData = datasets.some(dataset => dataset.data.some(value => value > 0));

        if (!hasData) {
            const ctx = chart.ctx;
            const { width, height } = chart;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = '16px Arial';
            ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
            ctx.fillText('No hay datos para mostrar', width / 2, height / 2);
            ctx.restore();
        }
    }
};
// Configuración grafico total proyectos registrados 
	const configProyect = {
	   	type: 'line',
	  	data: {
		   labels: mesesT, 
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
					max: maxnum,
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
		 },
		plugins: [emptyDataPlugin] 
	}

// 	Grafico total MONTO proyectos X estado--------------------------------------------------------------
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
					max : maximo,
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
        },
		plugins: [emptyDataPlugin]
    }
//Grafico PIE total monto por vertical-----------------------------------------------------
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
const totalChart = new Chart(totalRegistrados, configProyect);
//cambiar a trimestre seleccionado
const trimestreBtn = document.querySelectorAll('.btn-num');
let allNum = 1;
trimestreBtn.forEach(button => {
	button.addEventListener('click', function () {
		//cambiar clase btn
		if (button.classList.contains('active-btn')) {
			button.classList.remove('active-btn');
			allNum = 0;
		} else {
			trimestreBtn.forEach(b => b.classList.remove('active-btn'));
			button.classList.add('active-btn'); 
			allNum = 1;
		}
		//actualizar grafico
		const trimestre = this.getAttribute('data-trimestre'); 
		const mesesTrimestre = trimestres[trimestre];
		actualizarGraficoNum(mesesTrimestre, allNum);
	});
});

//funcion para actualizar trimestre
function actualizarGraficoNum(mesesTrimestre, allNum) {
	const mesesSeleccionados = mesesTrimestre;	
	if (allNum === 0) {
		totalChart.data.labels = mesesT; 
		totalChart.data.datasets = datasets; 
	} else {
		const datasetsFiltrados = datasets.map(dataset => {
			return {
				...dataset,
				data: dataset.data.filter((valor, index) => mesesSeleccionados.includes((index + 1).toString())) 
			};
		});

		const sumaTotal = datasetsFiltrados.reduce((sum, dataset) => {
			return sum + dataset.data.reduce((innerSum, value) => innerSum + value, 0);
		}, 0);
		totalChart.options.scales.y.max = sumaTotal > 0 ? sumaTotal : undefined;
		totalChart.data.labels = mesesSeleccionados.map(mes => transformarMes(mes));
		totalChart.data.datasets = datasetsFiltrados;
	}
	totalChart.update();
}
//grafico total monto por vertical--------------------------------------------------------------------
const projectTotal = document.getElementById('totalProjects').getContext('2d');
new Chart(projectTotal, configTotal);
//----------------------------------------------------------------------------------------------------
//grafico monto proyectos x estado
	const projects = document.getElementById('projects').getContext('2d');
	const testChart = new Chart(projects, configCount);
	//cambiar a trimestre seleccionado
	const trimestreButtons = document.querySelectorAll('.btn-q');
	let allinfo = 1;
	trimestreButtons.forEach(button => {
		button.addEventListener('click', function () {
			//cambiar clase btn
			if (button.classList.contains('active-btn')) {
				button.classList.remove('active-btn');
				allinfo = 0;
			} else {
				trimestreButtons.forEach(b => b.classList.remove('active-btn'));
				button.classList.add('active-btn'); 
				allinfo = 1;
			}
			//actualizar grafico
			const trimestre = this.getAttribute('data-trimestre'); 
			const mesesTrimestre = trimestres[trimestre];
			actualizarGrafico(mesesTrimestre, allinfo);
		});
	});

	//funcion para actualizar trimestre
	function actualizarGrafico(mesesTrimestre, allinfo) {
		const mesesSeleccionados = mesesTrimestre;
		if (allinfo === 0) {
			testChart.data.labels = mesesT; 
			testChart.data.datasets = datap; 
		} else {
			const datasetsFiltrados = datap.map(dataset => {
				return {
					...dataset,
					data: dataset.data.filter((valor, index) => mesesSeleccionados.includes((index + 1).toString())) 
				};
			});
			const sumaTotal = datasetsFiltrados.reduce((sum, dataset) => {
				return sum + dataset.data.reduce((innerSum, value) => innerSum + value, 0);
			}, 0);
			testChart.options.scales.y.max = sumaTotal > 0 ? sumaTotal : undefined;
			testChart.data.labels = mesesSeleccionados.map(mes => transformarMes(mes));
			testChart.data.datasets = datasetsFiltrados;
		}
		testChart.update();
	}

	//funcion para cambiar informacion por mes seleccionado
	const mesSelector = document.getElementById('mesSelector');
	mesSelector.addEventListener('change', function () {
			const mesSeleccionado = parseInt(this.value, 10);
		
			if (mesSeleccionado === 0) {
				// Mostrar todos los meses
				testChart.data.labels = mesesT; // Todos los meses
				testChart.data.datasets = datap; // Todos los datos completos
			} else {
				const mesTexto = mesesT[mesSeleccionado - 1]; // El texto del mes seleccionado
				testChart.data.labels = [mesTexto]; 
				testChart.data.datasets = datap.map(dataset => {
					return {
						...dataset,
						data: dataset.data.filter((value, index) => index + 1 === mesSeleccionado) // Filtra solo el valor del mes seleccionado
					};
				});
				//console.log(testChart.data.datasets)
			}
		
			
			testChart.update();
		});
	};
	//generar meses para el select
	mesesT.forEach((mes, index) => {
		const option = document.createElement("option");
		option.value = index + 1; 
		option.textContent = mes; 
		mesSelector.appendChild(option);
	});
