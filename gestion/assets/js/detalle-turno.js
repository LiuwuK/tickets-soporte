document.querySelectorAll('.den-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var turnoId = btn.getAttribute('data-sup-id');
        document.getElementById('idDen').value = turnoId;
    });
});