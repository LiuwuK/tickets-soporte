    const socket = new WebSocket('ws://localhost:8080');

    // Conexión establecida
    socket.onopen = function() {
        console.log('Conexión WebSocket abierta');
    };

    // Recepción de mensaje
    socket.onmessage = function (event) {
        const data = JSON.parse(event.data);
        addMessageToChat(data);
    };


    // Enviar mensaje
    function sendMessage(message, ticketId, sender) {
        console.log(ticketId)
        console.log(message)
        console.log(sender)
        if (!message.trim()) {
            alert('El mensaje no puede estar vacío.');
            return;
        }
        if (!ticketId) {
            alert('El ID del ticket no es válido.');
            return;
        }
        socket.send(JSON.stringify({ ticket_id: ticketId, sender: sender, message: message }));
    }

    // Función para agregar mensaje al chat
    function addMessageToChat(data) {
        const chatArea = document.getElementById(`chat_area_${data.ticket_id}`);
        if (chatArea) {
            const messageElement = document.createElement('div');
            messageElement.textContent = `${data.sender}: ${data.message}`;
            chatArea.appendChild(messageElement);
        } else {
            console.error(`No se encontró el área de chat para el ticket_id ${data.ticket_id}`);
        }
    }
