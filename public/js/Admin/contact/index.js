const modal = new bootstrap.Modal(document.getElementById('modalContact'), {keyboard: false});
console.log(document.querySelectorAll('a.delContact'));

// FONCTIONS
// -----------------------------------------------------------

function openModal(event){
    event.preventDefault();
    let url = this.href;
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let id = opt.split('-')[2];
    modal.show();
    if(crud === "DEL"){
        //document.getElementById('modalContact').querySelector('.modal-dialog').classList.add('modal-xl');
        document.getElementById('modalContact').querySelector('.modal-title').textContent = contentTitle;
        document.getElementById('modalContact').querySelector('.modal-body').textContent = "Vous êtes sur le point de supprimmer le message. Pour valider cette opération veuillez cliquer sur \"Supprimer\".";
    }else if(crud === "SHOW"){
        document.getElementById('modalContact').querySelector('.modal-dialog').classList.add('modal-xl');
        document.getElementById('modalContact').querySelector('.modal-title').textContent = contentTitle;
        document.getElementById('modalContact').querySelector('.modal-body').textContent = "Vous êtes sur le point de supprimmer le message. Pour valider cette opération veuillez cliquer sur \"Supprimer\".";
    }
}

function reloadEvent(){
    document.querySelectorAll('a.openModal').forEach(function(link){
        link.addEventListener('click', openModal);
    });
}

reloadEvent();
document.getElementById('modalContact').addEventListener('hidden.bs.modal', function(){
    document.getElementById('modal').querySelector('.modal-dialog').classList.remove('modal-xl');
    document.getElementById('modal').querySelector('.modal-title').textContent = "Contacts";
    document.getElementById('modal').querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">"+
        "<div class=\"spinner-border text-primary\" role=\"status\">"+
        "<span class=\"visually-hidden\">Chargement ...</span>"+
        "</div>"+
        "</div>"
    ;
});
