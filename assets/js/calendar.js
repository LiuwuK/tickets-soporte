 document.addEventListener('DOMContentLoaded', function() {
    const picker = new Litepicker({
        element: document.getElementById('calendar'),
        format: 'YYYY-MM-DD',
        singleMode: true,
        selectable: false,
        inlineMode: true,
        lang: 'es',
        theme: 'light',
        dropdowns: {
           minYear: 2020,
           maxYear: 2025,
        },
        numberOfMonths: 1,
        showTooltip: false,
        showWeekNumbers: false,
     });

     picker.container.querySelectorAll('.day').forEach(day => {
        day.style.pointerEvents = 'none';
        day.style.opacity = '0.5';
    });
 });