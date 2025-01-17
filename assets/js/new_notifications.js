const socket = new WebSocket('ws://localhost:8080/notifications');

toastr.options = {
    "closeButton": true, 
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right", // Posición de la notificación
    "preventDuplicates": true,
    "showDuration": "500", 
    "hideDuration": "1000", 
    "timeOut": "30000", 
    "extendedTimeOut": "10000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn", // Efecto al mostrar
    "hideMethod": "fadeOut" // Efecto al ocultar
};

const notificationSound = new Audio('assets/sounds/bells.mp3');

// Enviar información al servidor cuando se conecta
socket.onopen = () => {
    console.log("Conexion abierta")
};


socket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    if (userId == data.clientId && data.type === 'ticket_update') {
        notificationSound.play();
        toastr.info(data.message, "Actualización de Ticket");
        addNotificationToContainer(data);
    } else if (data.type === 'new_ticket') {
        notificationSound.play();
        toastr.info(data.message, "Importante") 
        addNotificationToContainer(data);
    }
};

socket.onerror = (error) => {
    console.error('Error en WebSocket:', error);
};

socket.onclose = () => {
    console.log('Conexión cerrada con el servidor WebSocket');
};


/* //Mostrar historial de notificaciones 
    document.getElementById('nt').addEventListener('click', function() {
        var notiContainer = document.getElementById('nt-div');
        var arrow = document.getElementById('arrow');
        
        
        // Alternar visibilidad de las notificaciones y la flecha
        if (notiContainer.classList.contains('visible')) {
            notiContainer.classList.remove('visible');


            arrow.style.display = "none";
        } else {
            notiContainer.classList.add('visible');
            notiContainer.style.display = "flex";
            arrow.style.display = "block"; 
        }
    });

    document.getElementById('close-btn').addEventListener('click', function() {
        var notiContainer = document.getElementById('nt-div');
        var arrow = document.getElementById('arrow');
        notiContainer.classList.remove('visible');
        arrow.style.display = "none"; 
    });

    //Calcular hace cuanto tiempo fue enviada la notificacion 
    function tiempoTranscurrido(creadaEn) {

        const fechaCreacion = new Date(creadaEn);
        const ahora = new Date(); 
        const diferencia = ahora - fechaCreacion;

        // Calcular los minutos, horas o días
        const segundos = Math.floor(diferencia / 1000);
        const minutos = Math.floor(diferencia / (1000 * 60));
        const horas = Math.floor(diferencia / (1000 * 60 * 60)); 
        const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24)); 
        
        if (segundos < 60) {
            return `Hace unos momentos`;
        } else if (minutos < 60) {
            return `Hace ${minutos} minuto${minutos !== 1 ? 's' : ''}`;
        } else if (horas < 24) {
            return `Hace ${horas} hora${horas !== 1 ? 's' : ''}`;
        } else {
            return `Hace ${dias} día${dias !== 1 ? 's' : ''}`;
        }
    }

    // mostrar el tiempo transcurrido para cada notificación
    document.querySelectorAll('.card').forEach(card => {
        const creadaEn = card.getAttribute('data-creada-en');
        const timeElapsed = card.querySelector('.time-elapsed');
        timeElapsed.textContent = tiempoTranscurrido(creadaEn); 
    });

    //Actualizar estado de notificacion
    document.addEventListener('DOMContentLoaded', () => {
        const links = document.querySelectorAll('.query-link');

        links.forEach(link => {
        link.addEventListener('click', async (event) => {
            event.preventDefault(); 

            // Capturar datos del enlace
            const url = link.getAttribute('href');
            const ticketId = link.getAttribute('data-ticketid');
            console.log(ticketId)
            try {
            const response = await fetch('http://localhost/tickets-soporte/assets/php/update-noti.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ticketId })
            });
            console.log("llego hasta aqui")
            if (response.ok) {
                // Navegar a la URL después de la consulta
                window.location.href = url;
            } else {
                console.error('Error en la consulta:', await response.text());
            }
            } catch (error) {
            console.error('Error en la conexión:', error);
            }
        });
        });
    });

    // Función para agregar la notificación al contenedor de notificaciones (WS)
    function addNotificationToContainer(notification) {

        const notiContainer = document.querySelector('.noti-b');  
        // Crear un nuevo enlace para la notificación
        const newNotification = document.createElement('a');
        newNotification.classList.add('query-link');
        newNotification.setAttribute('data-ticketid', notification.ticketId);
        

        if (notification.type === 'ticket_update'){
            newNotification.setAttribute('href', `http://localhost/tickets-soporte/view-tickets.php?textSearch=${notification.ticketId}`);
        }else{
            newNotification.setAttribute('href', `http://localhost/tickets-soporte/admin/manage-tickets.php?textSearch=${notification.ticketId}`);
        }

        //Estructura de la tarjeta de la notificación
        const card = document.createElement('div');
        card.classList.add('card');
        card.setAttribute('data-creada-en', new Date().toISOString());

        const imgDiv = document.createElement('div');
        imgDiv.classList.add('img');
        imgDiv.innerHTML = '<i class="fa fa-solid fa-user"></i>';

        const bdyDiv = document.createElement('div');
        bdyDiv.classList.add('bdy');

        const msgP = document.createElement('p');
        msgP.classList.add('msg');
        msgP.textContent = notification.message;

        const timeH6 = document.createElement('h6');
        timeH6.classList.add('time-elapsed');
        timeH6.textContent = 'Hace unos momentos'; 

        bdyDiv.appendChild(msgP);
        bdyDiv.appendChild(timeH6);

        card.appendChild(imgDiv);
        card.appendChild(bdyDiv);


        const newDiv = document.createElement('div');
        newDiv.classList.add('new');
        newDiv.innerHTML = '<p>!</p>';
        card.appendChild(newDiv);

        newNotification.appendChild(card);

        notiContainer.insertBefore(newNotification, notiContainer.firstChild);
    }
*/ 