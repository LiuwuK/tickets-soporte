function toggleDetails(maincard) {
    const additionalInfo = maincard.querySelector('.additional-info');
    additionalInfo.classList.toggle('open');
  }
function enableUpdateButton() {
  document.getElementById("updateButton").disabled = false;
}