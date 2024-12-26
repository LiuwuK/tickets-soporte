function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

//graficos
const ctx = document.getElementById('yearlyChart');
const monthly = document.getElementById('monthlyChart')


/*const weekly =  document.getElementById('weeklyChart')*/

/*GRAFICO SEMANAL
new Chart(weekly, {
	type: 'bar',
	data: {
	labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
	datasets: [{
		label: '1',
		data: [12, 19, 3, 5, 2, 3],
		borderWidth: 1
	},
	{
		label: '2',
		data: [10, 17, 4, 1, 12, 5],
		borderWidth: 1
	},
	{
		label: '3',
		data: [2, 1, 13, 15, 12, 3],
		borderWidth: 1
	},
	{
		label: '4',
		data: [9, 1, 14, 11, 2, 8],
		borderWidth: 1
	}	
	]

	},
	options: {
	scales: {
		y: {
		beginAtZero: true
		}
	}
	}
});*/

//Obtener datos mensuales y anuales
fetch('../charts/get-data.php')  
    .then(response => response.json())  
    .then(data => {
	//Grafico mensual
		month_data = data['monthly']
		const values = month_data.map(item => parseInt(item.cantidad)); 
        // obtener total
        const total = values.reduce((sum, value) => sum + value, 0); 

		//Obtener mes actual
		const date = new Date();
		const currentMonth = new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(date);
		const month =  capitalize(currentMonth);

		//mapear datos 
		const datasets = month_data.map(item => ({
            label: item.statusN, 
            data: [item.cantidad], 
            borderWidth: 1
        }));

        new Chart(monthly, {
			type: 'bar',	
			data: {
				labels:[month],
				datasets: datasets
			},
			options: {
				responsive: true,
				scales: {
					y: {
						beginAtZero: true,
						max: total,
						title: {
							display: true,
							text: 'Total de tickets'
						}
					}
				}
			}
			
		});

		//Grafico Anual
		year_data = data['yearly']

		//Se agrupan los datos por mes 
		const groupedData = {};
		year_data.forEach(item => { 
			if (!groupedData[item.mes]) {
				groupedData[item.mes] = {};
			} 
			groupedData[item.mes][item.statusN] = parseInt(item.cantidad); 
		}); 
		
		//se obtiene el total de valores
		const valuesY = year_data.map(item => parseInt(item.cantidad)); 
		const totalY = valuesY.reduce((sum, value) => sum + value, 0);
		
		//Redondear numero 
		const maxG = Math.ceil(totalY / 5) * 5;

		// se obtienen los meses y estados
		const months = Object.keys(groupedData).sort((a, b) => a - b); 
		const states = Array.from(new Set(year_data.map(item => item.statusN))); 

		const datasetsY = states.map(state => ({ 
			label: state, 
			data: months.map(month => groupedData[month][state] || 0), 
			borderWidth: 1
		}));

		//se cambia el formato de los meses(de yy a MONTH )
		const monthNames = months.map(month => { const date = new Date(2000, month - 1, 1); 
			return new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(date); 
		});

		//se genera el grafico
		new Chart(ctx, {
			type: 'bar',	
			data: {
				labels: monthNames,
				datasets: datasetsY
			},
			options: {
				indexAxis: 'y',
				responsive: true,
				scales: {
					y: {
						title: {
							display: true,
							text: 'Meses'
						}
					},
					x: {
						beginAtZero: true,
						max: totalY,
						title: {
							display: true,
							text: 'Tickets generados'
						}
					}
				}
			}
		});

    })
    .catch(error => console.error('Error:', error));  


