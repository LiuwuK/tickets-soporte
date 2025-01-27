//calendario-----------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar'); 

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
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
        locales:'es', 
        selectable: true,            
        editable: false,
        slotMinTime: '08:00:00', 
        slotMaxTime: '21:00:00', 
        slotDuration: '00:30:00', 
        allDaySlot: true,    
        events: 'assets/php/get_events.php',
        eventMouseEnter: function (info) {
            const tooltip = new bootstrap.Tooltip(info.el, {
              title: `<strong>${info.event.title}</strong><br>${info.event.start.toLocaleString()}`,
              html: true, 
              placement: 'top', 
              container: 'body',
            });
            tooltip.show();
            info.el._tooltipInstance = tooltip;
          },
        eventMouseLeave: function (info) {
        if (info.el._tooltipInstance) {
            info.el._tooltipInstance.dispose();
            delete info.el._tooltipInstance;
        }
        },    
        eventClick: function (info) {
        alert('Evento: ' + info.event.title);
        },
    });

    calendar.render();
    });
