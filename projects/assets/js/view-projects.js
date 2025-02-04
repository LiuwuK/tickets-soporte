document.querySelectorAll("#editButton").forEach(function(btn) {
    btn.addEventListener("click",function() {
        const projectId = this.getAttribute("data-id");
        window.location.href = `update-project.php?projectId=${projectId}`;
    });
});