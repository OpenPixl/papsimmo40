const modalArticleBs = new bootstrap.Modal(document.getElementById('modalArticle'), {keyboard: false});
const modalArticle = document.getElementById('modalArticle');
let bntModalArticle = document.getElementById('btnModalArticle');

document.querySelectorAll('a.delarticle').forEach(function (link) {
    link.addEventListener('click',openModalArticle);
});

function openModalArticle(event) {
    event.preventDefault();
    let a = event.currentTarget;
    let url = this.href;
    let recipient = a.getAttribute('data-bs-whatever');
    let crud = recipient.split('-')[0];
    let contentTitle = recipient.split('-')[1];
    let id = recipient.split('-')[2];
    modalArticle.querySelector('.modal-footer a').href = url;
    if(crud === 'ADD'){
        let modalHeaderH5 = modalArticle.querySelector('.modal-title');
        modalHeaderH5.textContent = contentTitle;
    }else if(crud === 'EDIT'){
        let modalHeaderH5 = modalArticle.querySelector('.modal-title');
        modalHeaderH5.textContent = contentTitle;
    }else if(crud === 'DEL'){
        let btnSubmit = modalArticle.querySelector('.modal-footer a');
        btnSubmit.textContent = 'Supprimer l\'article';
        btnSubmit.id = "btnSupprArticle";
        let modalHeaderH5 = modalArticle.querySelector('.modal-title');
        modalHeaderH5.textContent = contentTitle;
        modalArticle.querySelector('.modal-body').innerHTML =
            '<p><b>Attention</b> : Vous êtes sur le point de supprimer l\'article '+ id +'.</p>' +
            '<p>Pour valider cette opération, veuillez clisur sur \'Supprimer\'.</p>';
        reloadEventArticle();
    }
}

function submitModalArticle(event) {
    event.preventDefault();
    let a = event.currentTarget;
    let id = a.id;
    if(id === 'btnSupprArticle'){
        let url = this.href;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('liste').innerHTML = response.data.liste;
                // initialisation du toaster
                let toastHTMLElement = document.getElementById("toaster");
                let message = response.data.message;
                let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
                toastBody.textContent = message;
                let toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true,autohide: true,delay: 3000,});
                toastElement.show();
                reloadEventArticle();
            })
            .catch(function(error) {
                console.log(error);
            });
    }else{
        console.log('Pas de bouton portant l\'id suppr');
    }

}

modalArticle.addEventListener('hide.bs.modal', event => {
    modalArticle.querySelector('.modal-header').innerHTML =
        '<h1 class="modal-title fs-5" id="exampleModalLabel">Modal des articles</h1>\n' +
        '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n';
    modalArticle.querySelector('.modal-body').innerHTML =
        '<div class="d-flex justify-content-center">\n' +
        '<div class="spinner-border text-primary" role="status">\n' +
        '<span class="sr-only">Loading...</span>\n' +
        '</div>\n' +
        '</div>';
    modalArticle.querySelector('.modal-footer').innerHTML =
        '<a id="SubmitArticle" href="#" class="btn btn-sm btn-outline-dark" data-bs-dismiss="modal">Modifier</a>' +
        '<button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fermer</button>';
});

function reloadEventArticle(){
    document.getElementById('btnSupprArticle').addEventListener('click', submitModalArticle);
}