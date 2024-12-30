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

// Enviar información al servidor cuando se conecta
socket.onopen = () => {
    console.log("Conexion abierta")
};


socket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    if (userId == data.clientId && data.type === 'ticket_update') {
        toastr.info(data.message, "Actualización de Ticket");
    } else if (data.type === 'new_ticket') {
        toastr.info(data.message, "Importante") 
    }
};

socket.onerror = (error) => {
    console.error('Error en WebSocket:', error);
};

socket.onclose = () => {
    console.log('Conexión cerrada con el servidor WebSocket');
};