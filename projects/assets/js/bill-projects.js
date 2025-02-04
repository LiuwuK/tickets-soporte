
document.addEventListener('DOMContentLoaded', function () {
    var endBtns = document.querySelectorAll('[data-bs-target="#closeModal"]');

    endBtns.forEach(function(button) {
        button.addEventListener('click', function() {
            var pId = this.getAttribute('data-id');
            var modal = document.getElementById('closeModal');
            var hiddenInput = modal.querySelector('input[name="pId"]');
            if (hiddenInput) {
                hiddenInput.value = pId;
            }
        });
    });
});