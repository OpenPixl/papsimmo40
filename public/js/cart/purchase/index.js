const showCommande = document.getElementById('showCommande');

showCommande.addEventListener('show.bs.modal', (event) => {
    event.preventDefault();
    let opt = event.relatedTarget.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let url = event.relatedTarget.href;
});