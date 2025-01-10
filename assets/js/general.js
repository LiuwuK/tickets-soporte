document.getElementById("toggleFiltersBtn").addEventListener("click", function () {
    var form = document.getElementById("filtersForm");
    var icon = this.querySelector("i");

    // Alternar la clase 'expanded' para la animación
    if (form.classList.contains("expanded")) {
        form.classList.remove("expanded");
        icon.classList.remove("bi-arrow-up-short");
        icon.classList.add("bi-arrow-down-short");
    } else {
        form.classList.add("expanded");
        icon.classList.remove("bi-arrow-down-short");
        icon.classList.add("bi-arrow-up-short");
    }
});