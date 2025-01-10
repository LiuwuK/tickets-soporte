document.addEventListener('DOMContentLoaded', function() {
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        locale: 'es',  // Establecer el idioma español
        initialView: 'dayGridMonth', 
        headerToolbar: {
            left: 'title',
            center: 'prev,next today', 
            right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        }
        //
        });
    calendar.render();
});
