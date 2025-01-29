    //obtener dia actual 
    const today = new Date();
    const currentDay = new Intl.DateTimeFormat('es-ES', { weekday: 'long' }).format(today);
    document.getElementById('day').textContent = `${currentDay.charAt(0).toUpperCase()}${currentDay.slice(1)}`;
    //convertir en fecha completa y pasar a campo oculto
    const timeInput = document.getElementById('hora');
    timeInput.addEventListener('change', () => {
        const selectedTime = timeInput.value;
        const currentDate = new Date().toISOString().split('T')[0];
        const finalDateTime = `${currentDate}T${selectedTime}:00`; 
        console.log(finalDateTime);

        document.getElementById('fechaFinal').value = finalDateTime;
    });