document.getElementById('updtProject').addEventListener('submit', function (e) {
    const disabledFields = this.querySelectorAll('[disabled]');
    disabledFields.forEach(field => field.removeAttribute('disabled'));
});
