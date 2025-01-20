//Actualizar ingeniero responsable
document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("change", function (event) {
        if (event.target.classList.contains("ingeniero-select")) {
            const selectElement = event.target;
            const saveButton = selectElement.closest(".ing-main").querySelector(".save-button");
            const initialValue = selectElement.getAttribute("data-initial-value");

            if (selectElement.value !== initialValue) {
                saveButton.style.display = "block";
            } else {
                saveButton.style.display = "none";
            }
        }
    });
});

document.querySelectorAll("#editButton").forEach(function(btn) {
    btn.addEventListener("click",function() {
        const projectId = this.getAttribute("data-id");
        window.location.href = `update-project.php?projectId=${projectId}`;
    });
});

document.addEventListener('DOMContentLoaded', function () {
var endBtns = document.querySelectorAll('[data-bs-target="#closeModal"]');

endBtns.forEach(function(button) {
        button.addEventListener('click', function() {
            var pId = this.getAttribute('data-pid');
            var modal = document.getElementById('closeModal');
            var hiddenInput = modal.querySelector('input[name="pId"]');
            
            // Asignar el 'pId' al input del modal
            if (hiddenInput) {
                hiddenInput.value = pId;
            }
        });
    });
});