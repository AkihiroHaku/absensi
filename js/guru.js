// modal logout
const modal = document.getElementById('modalKeluar');
function bukaModal() {
    modal.style.display = 'flex';
}
function tutupModal() {
    modal.style.display = 'none';
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}