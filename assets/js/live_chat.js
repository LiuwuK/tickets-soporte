const socket = new WebSocket('ws://localhost:8080');

// Conexión establecida
socket.onopen = function() {
    console.log('Conexión WebSocket abierta');
};

// Recepción de mensaje
socket.onmessage = function (event) {
    const data = JSON.parse(event.data);
    addMessageToChat(data); // Mantienes tu función para agregar los mensajes al chat
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
    // Enviar el mensaje al servidor WebSocket
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

// Cargar mensajes previos de la base de datos (función ya existente)
function loadMessages(ticketId) {
    const chatArea = document.getElementById(`chat_area_${ticketId}`);
    if (!chatArea) return;

    fetch(`/tickets-soporte/load_messages.php?ticket_id=${ticketId}`)
        .then(response => {
            // Si la respuesta es correcta, se procesan los datos
            if (response.ok) {
                return response.json();
            } else {
                // Si la primera ruta falla, intenta con la ruta relativa (ya que la vista de admin está en una subcarpeta)
                return fetch(`../load_messages.php?ticket_id=${ticketId}`);
            }
        })
        .then(data => {
            if (data.error) {
                chatArea.innerHTML = `<p>Error: ${data.error}</p>`;
                return;
            }

            // Agregar solo los nuevos mensajes
            data.forEach(message => {
                const messageElement = document.createElement('div');
                messageElement.textContent = `${message.sender}: ${message.message}`;
                chatArea.appendChild(messageElement);
            });
        })
        .catch(error => {
            console.error("Error al cargar mensajes:", error);
        });
}

window.addEventListener("load", () => {
    console.log('La página y todos los recursos han sido cargados');
    const chatAreas = document.querySelectorAll("[id^='chat_area_']");
    chatAreas.forEach(chatArea => {
        const ticketId = chatArea.id.split('_')[2]; // Extraer ticket ID del ID del div
        console.log(`Ticket ID: ${ticketId}`);
        loadMessages(ticketId);
    });
});
