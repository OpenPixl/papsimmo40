// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickr", {
    "locale": "fr",
    enableTime: false,
    allowInput: true,
    altFormat: "j F Y",
    dateFormat: "d/m/Y",
});
// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickrtime", {
    "locale": "fr",
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

let btnConfPurchase = document.getElementById('btnConfPurchase');


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

function toasterMessage(message)
{
    // préparation du toaster
    let option = {
        animation: true,
        autohide: true,
        delay: 3000,
    };
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