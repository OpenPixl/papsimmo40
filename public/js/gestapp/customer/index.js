// Code d'ouverture de la modale de suppression d'une ligne
const modalCustomer = new bootstrap.Modal(document.getElementById('modalCustomer'));
const modalCustomerBs = document.getElementById('modalCustomer');

function showModalCustomer(event){
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let id = opt.split('-')[2];
    let url = this.href;
    console.log(url);
    modalCustomer.show();
    document.getElementById('modalCustomer').querySelector('.modal-dialog').classList.add('modal-xl');
    document.getElementById('modalCustomer').querySelector('.modal-title').textContent = contentTitle;
    if(crud === 'ADD'){
    }else if(crud === 'EDIT'){
    }else if(crud === 'DEL'){
        //reloadEvent();
        document.getElementById('modalCustomer').querySelector('.modal-dialog').classList.remove('modal-xl');
        document.getElementById('modalCustomer').querySelector('#btnModalSubmit').textContent = "Supprimer la fiche client";
        document.getElementById('modalCustomer').querySelector('#btnModalSubmit').href = url;
        document.getElementById('modalCustomer').querySelector('.modal-body').innerHTML = "Vous êtes sur le point de supprimmer une fiche client.";
        document.getElementById('modalCustomer').querySelector('#btnModalSubmit').addEventListener('click', submitLinkModal);
    }
}

// Code de suppression lors du clic sur le bouton de la modal "Suppr"
function submitLinkModal(event){
    event.preventDefault();
    const url = this.href;
    axios
        .post(url)
        .then(function(response)
        {
            const liste = document.getElementById('liste').innerHTML = response.data.liste;
            toasterMessage(response.data.message);
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        });
}

modalCustomerBs.addEventListener('hidden.bs.modal', function(){
    if(modalCustomerBs.querySelector('.modal-dialog').classList.contains('modal-xl')){
        modalCustomerBs.querySelector('.modal-dialog').classList.remove('modal-xl');
    }
    if(modalCustomerBs.querySelector('.modal-dialog #btnEditPrescriber')){
        modalCustomerBs.querySelector('.modal-dialog #btnEditPrescriber').id = "btnModalSubmit";
    }
    modalCustomerBs.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">"+
        "<div class=\"spinner-border text-primary\" role=\"status\">"+
        "<span class=\"visually-hidden\">Loading...</span>"+
        "</div>"+
        "</div>"
    ;
});

const searchCustomerform = document.getElementById('searchCustomerform');
const searchCustomerInput = document.getElementById('searchCustomerInput');

searchCustomerInput.addEventListener('input', function(event){
    if(searchCustomerInput.value.length >= 2){
        document.getElementById('liste').innerHTML = '';
        let action = searchCustomerform.action;
        let value = searchCustomerInput.value;
        axios
            .post(action, {'word': value})
            .then(function(response){
                document.getElementById('liste').innerHTML = response.data.liste;
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }
});

function toasterMessage(message){
    // préparation du toaster
    let option = {animation: true,autohide: true,delay: 3000,};
    // initialisation du toaster
    let toastHTMLElement = document.getElementById("toaster");
    let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
    toastBody.textContent = message;
    let toastElement = new bootstrap.Toast(toastHTMLElement, option);
    toastElement.show();
}

function reloadEvent(){
    let link_suppr_customer = document.querySelectorAll('a.suppr_customer');
    // Ajout d'un event sur Bouton de suppression dans la fenêtre modale
    link_suppr_customer.forEach(function(link){
        link.addEventListener('click', showModalCustomer);
    });
}

reloadEvent();