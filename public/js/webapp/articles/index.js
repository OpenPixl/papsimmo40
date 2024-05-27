const modalArticleBs = new bootstrap.Modal(document.getElementById('modalArticle'), {keyboard: false});
const modalArticle = document.getElementById('modalArticle');
let bntModalArticle = document.getElementById('btnModalArticle');

document.querySelectorAll('a.delarticle').forEach(function (link) {
    link.addEventListener('click',openModalArticle);
});

function openModalArticle(event) {
    event.preventDefault();
    let a = event.currentTarget;
    let url = event.target.href;
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
        let url = a.href;
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
            })
            .catch(function(error) {
                console.log(error);
            });
    }

}

function reloadEventArticle(){
    document.getElementById('btnSupprArticle').addEventListener('click', submitModalArticle);
}