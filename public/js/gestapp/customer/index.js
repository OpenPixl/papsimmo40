// Code d'ouverture de la modale de suppression d'une ligne
const SupprCustomer = document.getElementById('SupprCustomer');

SupprCustomer.addEventListener('show.bs.modal', function (event) {
    var a = event.relatedTarget;
    var recipient = a.getAttribute('data-bs-whatever');
    var modalTitle = SupprCustomer.querySelector('.modal-title');
    var modalText = SupprCustomer.querySelector('.modal-text');
    var modalBodyInput = SupprCustomer.querySelector('.modal-body input');
    var modalFootera = SupprCustomer.querySelector('.modal-footer .data-supprcustomer');
    modalTitle.textContent = "Suppression d'un article";
    modalText.innerHTML = "Vous êtes sur le point de supprimer le client sélectionné.<br><b>Etes-vous sur de vouloir continuer ?</b>";
    modalFootera.href = '/gestapp/customer/del/' + recipient;
});

// Code de suppression lors du clic sur le bouton de la modal "Suppr"
function onClickDelEvent(event){
    event.preventDefault();
    const url = document.getElementById('BtnSupprCustomer').href;
    axios
        .post(url)
        .then(function(response)
        {
            // rafraichissement du tableau
            const liste = document.getElementById('list').innerHTML = response.data.liste;
            toasterMessage(response.data.message);
        })
        .catch(function(error){
            console.log(error);
        });
}

const searchCustomerform = document.getElementById('searchCustomerform');
const searchCustomerInput = document.getElementById('searchCustomerInput');
let liste = document.getElementById('liste');

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
    // Ajout d'un event sur Bouton de suppression dans la fenêtre modale
    document.querySelectorAll('a.data-supprcustomer').forEach(function(link){
        link.addEventListener('click', onClickDelEvent);
    });
}

reloadEvent();