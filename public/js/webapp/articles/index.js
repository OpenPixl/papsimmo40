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
    modalArticle.querySelector('.modal-footer a').textContent = 'Supprimer l\'article';
    if(crud === 'ADD'){
        let modalHeaderH5 = modalArticle.querySelector('.modal-title');
        modalHeaderH5.textContent = contentTitle;
    }else if(crud === 'EDIT'){
        let modalHeaderH5 = modalArticle.querySelector('.modal-title');
        modalHeaderH5.textContent = contentTitle;
    }else if(crud === 'DEL'){
        let modalHeaderH5 = modalArticle.querySelector('.modal-title');
        modalHeaderH5.textContent = contentTitle;
        modalArticle.querySelector('.modal-body').innerHTML =
            '<p><b>Attention</b> : Vous êtes sur le point de supprimer l\'article '+ id +'.</p>' +
            '<p>Pour valider cette opération, veuillez clisur sur \'Supprimer\'.</p>';
    }
}

function submitModalArticle() {

}

function reloadEventArticle(){}