document.getElementById("toggleFiltersBtn").addEventListener("click", function() {
    var form = document.getElementById("filtersForm");
    var icon = this.querySelector("i");

    // Alternar la visibilidad del formulario
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
        icon.classList.remove("glyphicon-chevron-down");
        icon.classList.add("glyphicon-chevron-up");
    } else {
        form.style.display = "none";
        icon.classList.remove("glyphicon-chevron-up");
        icon.classList.add("glyphicon-chevron-down");
    }
});