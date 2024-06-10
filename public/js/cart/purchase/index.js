const showCommande = document.getElementById('showCommande');

document.querySelectorAll('a.showCommande').forEach(function(link){
    link.addEventListener('click', modalCommande);
});

showCommande.addEventListener('hidden.bs.modal', function(){
    if(modalRecoBs.querySelector('.modal-dialog').classList.contains('modal-xl')){
        modalRecoBs.querySelector('.modal-dialog').classList.remove('modal-xl');
    }
    if(modalRecoBs.querySelector('.modal-dialog #btnEditCommande')){
        modalRecoBs.querySelector('.modal-dialog #btnEditCommande').id = "btnModalSubmit";
    }
    modalRecoBs.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">" +
        "<div class=\"spinner-border text-primary\" role=\"status\">" +
        "<span class=\"visually-hidden\">Loading...</span>" +
        "</div>" +
        "</div>"
    ;
});

function modalCommande(event){
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let url = this.href;
    document.getElementById('showCommande').querySelector('.modal-title').textContent = contentTitle;
    if(crud === 'ADD'){
        showCommande.querySelector('#btnModalSubmit').textContent = "Ajouter la recommandation";
        showCommande.querySelector('#btnModalSubmit').href = url;
        showCommande.querySelector('#btnModalSubmit').addEventListener('click', submitReco);
    }else if(crud === 'DEL'){
        document.getElementById('showCommande').querySelector('.modal-body').innerHTML = '<p><b>Attention:</b><br>Vous êtes sur le point de supprimer votre commande.<br>Pour continuer, cliquez sur "Supprimer"</p>';
        document.getElementById('showCommande').querySelector('#btnSubmitCommande').textContent = "Supprimer la commande";
        document.getElementById('showCommande').querySelector('#btnSubmitCommande').href = url;
        document.getElementById('showCommande').querySelector('#btnSubmitCommande').addEventListener('click', submitCommande);
    }
}

function submitCommande(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('listeSupport').innerHTML = response.data.liste;
            toasterMessage(response.data.message);
        })
        .catch();
}

function toasterMessage(message){
// préparation du toaster
    let option = {animation: true,autohide: true,delay: 5000};
    // initialisation du toaster
    let toastHTMLElement = document.getElementById("toaster");
    let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
    toastBody.textContent = message;
    let toastElement = new bootstrap.Toast(toastHTMLElement, option);
    toastElement.show();
}