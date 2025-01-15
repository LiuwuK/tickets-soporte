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