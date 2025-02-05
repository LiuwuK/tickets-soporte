function enableUpdateButton() {
    document.getElementById("btnUpdt").disabled = false;
  }
  //pasar id al modal
document.querySelectorAll('.del-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var supervisorId = btn.getAttribute('data-sup-id');
        document.getElementById('idSup').value = supervisorId;
    });
});