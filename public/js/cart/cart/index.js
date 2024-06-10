const modalCart = document.querySelector("#modalCart");

let arrayCheckbox = [];
let btnConfPurchase = document.getElementById('btnConfPurchase');

// déclaration des évènements de la page
document.querySelectorAll('a.showCart').forEach(function(link){
    link.addEventListener('click', showModalCart);
});

modalCart.addEventListener('hidden.bs.modal', function(){
    if(modalCart.querySelector('.modal-dialog').classList.contains('modal-xl')){
        modalCart.querySelector('.modal-dialog').classList.remove('modal-xl');
    }
    if(modalCart.querySelector('.modal-dialog #btnEditCart')){
        modalCart.querySelector('.modal-dialog #btnEditCart').id = "btnModalSubmit";
    }
    modalCart.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">" +
        "<div class=\"spinner-border text-primary\" role=\"status\">" +
        "<span class=\"visually-hidden\">Loading...</span>" +
        "</div>" +
        "</div>"
    ;
});

// sélectionner toutes les checkBox's à partir de la checkbox du ht de page
document.getElementById('AllCheckBoxes').onclick = function() {
    let checkboxes = document.getElementsByName('oneCheckbox');
    for (let checkbox of checkboxes) {
        checkbox.checked = this.checked;
        if(checkbox.checked){
            arrayCheckbox.push(checkbox.value);
            arrayCheckbox = [... new Set(arrayCheckbox)];
        }
        console.log(arrayCheckbox);
    }
};
document.getElementById('supprRows').onclick = function(event){
    event.preventDefault();
    let checkboxes = document.getElementsByName('oneCheckbox');
    for (let checkbox of checkboxes) {
        if(checkbox.checked){
            arrayCheckbox.push(checkbox.value);
            arrayCheckbox = [... new Set(arrayCheckbox)];
        }
    }
    axios
        .post('/cart/cart/delcheckboxes/', arrayCheckbox)
        .then(function(response){
            document.getElementById('listeSupport').innerHTML = response.data.liste;
            toasterMessage(response.data.message);
        })
        .catch(function(error){
            console.log(error);
        })
    ;
};

// Déclaration des fonctions de la page
// --------------------------------------------------------------
function validCart(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            toasterMessage(response.data.message);
            allReloadEvent();
        }).catch(function(error){
            console.log(error);
    });
}

function showModalCart(event){
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let url = this.href;
    document.getElementById('modalCart').querySelector('.modal-title').textContent = contentTitle;
    if(crud === 'ADD'){
        showCommande.querySelector('#btnModalSubmit').textContent = "Ajouter la recommandation";
        showCommande.querySelector('#btnModalSubmit').href = url;
        showCommande.querySelector('#btnModalSubmit').addEventListener('click', submitReco);
    }else if(crud === 'DEL'){
        document.getElementById('modalCart').querySelector('.modal-body').innerHTML = '<p><b>Attention:</b><br>Vous êtes sur le point de retirer ce produit du panier.<br>Pour continuer, cliquez sur "Retirer"</p>';
        document.getElementById('modalCart').querySelector('#btnSubmitCommande').textContent = "Retirer le produit";
        document.getElementById('modalCart').querySelector('#btnSubmitCommande').href = url;
        document.getElementById('modalCart').querySelector('#btnSubmitCommande').addEventListener('click', submitCart);
    }
}

function submitCart(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('listeSupport').innerHTML = response.data.liste;
            toasterMessage(response.data.message);
        })
        .catch(function(error){
            console.log(error);
        });
}

function toasterMessage(message)
{
    // préparation du toaster
    let option = {animation: true,autohide: true,delay: 3000,};
    // initialisation du toaster
    let toastHTMLElement = document.getElementById("toaster");
    let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
    toastBody.textContent = message;
    let toastElement = new bootstrap.Toast(toastHTMLElement, option);
    toastElement.show();
}

if(btnConfPurchase != null){
    btnConfPurchase.onclick = validCart;
}

// Fonction de rechargement des events
function allReloadEvent(){

}