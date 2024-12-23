//Obtener el id del ticket y pasarlo al modal
document.querySelectorAll('.taskbtn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var ticketId = btn.getAttribute('data-ticket-id'); 
      document.getElementById('modalTicketId').value = ticketId;
      //console.log(ticketId);
    });
});

//Agregar tareas
document.getElementById('addTaskBtn').addEventListener('click', function() {
    //Contenedor de las tareas (Body del modal)
    var taskContainer = document.getElementById('tasksContainer');
    var taskCount = taskContainer.getElementsByClassName('form-group').length + 1;

    //se crea un nuevo div para cada tarea, con su respectivo input y label
    var newTaskDiv= document.createElement('div');
    newTaskDiv.classList.add('form-group');

    //creacion del label
    var newLabel = document.createElement('label');
    newLabel.setAttribute('for', 'title' + taskCount);
    newLabel.textContent = 'Tarea #' + taskCount;
    //creacion del input
    var newInput = document.createElement('input');
    newInput.type = 'text';
    newInput.classList.add('form-control', 'task-input');
    newInput.setAttribute('id', 'title' + taskCount);
    newInput.setAttribute('name','title' + taskCount);

    //se agregan el input y label al contenedor
    newTaskDiv.appendChild(newLabel);
    newTaskDiv.appendChild(newInput);

    //se agrega el contenedor de una nueva tarea despues de la ultima existente
    tasksContainer.appendChild(newTaskDiv);
});

//Eliminar tareas 
document.getElementById('delTaskBtn').addEventListener('click', function(){
    //Contenedor de las tareas (Body del modal)
    var taskContainer = document.getElementById('tasksContainer');
    //se obtiene la ultima tarea 
    var lastTask = taskContainer.lastElementChild;
    var taskCount = taskContainer.getElementsByClassName('form-group').length;
    
    //se verifica si existen tareas y si es que son mas de 1 
    if(lastTask && taskCount > 1){
        taskCount = taskCount - 1;
        //se elimina la ultima tarea
        lastTask.remove();
    }
});

 