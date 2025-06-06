// Déclaration des variables
const btnsOpenModal = document.querySelectorAll(".openModal");
const btnSubmitModal = document.getElementById("btnModalSubmit");
const modal = document.getElementById('modal');
const modalBs = new bootstrap.Modal(modal);

// Déclaration d'évènement
btnsOpenModal.forEach(function(link){
    link.addEventListener('click', openModal);
});
btnSubmitModal.addEventListener('click', submitModal);

modal.addEventListener('hidden.bs.modal', event => {

    modal.querySelector('.modal-dialog').classList.remove('modal-lg');
    modal.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">\n" +
        "<div class=\"spinner-border text-primary\" role=\"status\">\n" +
        "<span class=\"visually-hidden\">Loading...</span>\n" +
        "</div>\n" +
        "</div>";
});

// fonctions
function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}

function zipcode_api(zipcode, commune, select){
    if(zipcode.value.length === 5)
    {
        let coord = zipcode.value;
        axios
            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
            .then(function(response){
                let features = response.data;
                removeOptions(select);
                let ville = '';
                let cp = '';
                features.forEach((element) => {
                    let name =  element.nomCommune + " (" + element.codePostal + ')';
                    ville = element.nomCommune;
                    cp = element.codePostal;
                    let OptSelect = new Option(name.toUpperCase(), name.toUpperCase(), false, true);
                    select.options.add(OptSelect);
                });

                if (select.options.length === 1) {
                    zipcode.value = cp;
                    commune.value = ville.toUpperCase();
                } else {
                    zipcode.value = cp;
                    commune.value = ville.toUpperCase();
                }
            })
            .catch(function(error){
                alert('pas de commune sur ce code postal');
            })
        ;
    }
}

function openModal(event){
    event.preventDefault();
    let a = event.currentTarget;
    let recipient = a.getAttribute('data-bs-data');
    let url = a.href;
    let [crud, contentTitle, id] = recipient.split('-');
    modalBs.show();

    modal.querySelector('.modal-title').textContent = contentTitle;
    if(crud === 'ADDBUYERS' || crud === "EDITBUYERS"){
        modal.querySelector('.modal-dialog').classList.add('modal-xl');
        axios
            .get(url)
            .then(function (response){
                modal.querySelector('.modal-body').innerHTML = response.data.formView;
            })
            .catch(function(error){
                console.log(error);
            });
    }
    else if(crud === 'Add_Date'){
        axios.get(url).then().catch();
    }
    else if(crud === 'Add_Document'){
        axios.get(url).then().catch();
    }
    else if(crud === 'Del_Buyers'){
        modal.querySelector('.modal-body').innerHTML = "<p class='mb-0'>Attention, vous êtes sur le point de supprimer cet acheteur de la vente. </p>";
        modal.querySelector('.modal-footer a').textContent = "Suppression";
        modal.querySelector('.modal-footer a').href = url;
        reloadEvent();
    }
}

function submitModal(event){
    event.preventDefault();
    let a = event.currentTarget;
    let url = a.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('Block_Buyers').innerHTML = response.data.view;
            toasterMessage(response.data.message);
        })
        .catch();
}

function toasterMessage(message){
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

function reloadEvent()
{
    const btnsSubmitModal = document.querySelectorAll(".btnModalSubmit");
    console.log(btnsSubmitModal);

}

