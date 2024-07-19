// PARTIE Ajout d'un vendeur en Javascript
// Créé par Xavier BURKE - OpenPixl
// -----------------------------------------

// Modales
const modalCustomer = new bootstrap.Modal(document.getElementById('modalCustomer'));
const modalCustomerBs = document.getElementById('modalCustomer');
// Boutons
let btnAddCustomer = document.getElementById('btnAddCustomer');
// input
const typeClient = document.getElementById('customer2_typeClient');
const civility = document.querySelectorAll('input[name="customer[civility]"]');
const maidenName = document.getElementById('customer_maidenName');

btnAddCustomer.addEventListener('click', showModalCustomer);
document.querySelectorAll('.btnShownCustomer').forEach(function(link){
    link.addEventListener('click', showModalCustomer);
});

if(modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer') !== null){
    modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer').addEventListener('click', submitCustomer);
}
// comportement à adopter à la fermeture de la modal des customers
modalCustomerBs.addEventListener('hidden.bs.modal', event => {
    modalCustomerBs.querySelector('.modal-dialog').classList.remove('modal-xl');
    modalCustomerBs.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">\n" +
        "<div class=\"spinner-border text-primary\" role=\"status\">\n" +
        "<span class=\"visually-hidden\">Loading...</span>\n" +
        "</div>\n" +
        "</div>";
    modalCustomerBs.querySelector('.modal-footer').innerHTML =
        '<a id="btnSubmitCustomer" type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Ajouter</a>' +
        '<button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>';
});

function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}

function showModalCustomer(event){
    // Button that triggered the modal
    let a = event.currentTarget;
    // extraction de la variable
    let recipient = a.getAttribute('data-bs-data');
    let url = this.href;
    let crud = recipient.split('-')[0];
    let contentTitle = recipient.split('-')[1];
    let id = recipient.split('-')[2];
    if(crud === 'ADD'){
        modalCustomerBs.querySelector('.modal-dialog').classList.add('modal-xl');
        modalCustomerBs.querySelector('.modal-title').textContent = contentTitle;
        modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer').innerHTML = 'Ajouter le nouveau propriétaire';
        axios
            .get(url)
            .then(function(response){
                modalCustomerBs.querySelector('.modal-body').innerHTML = response.data.formView;
                let typeClient = modalCustomerBs.querySelector('.modal-body #customer2_typeClient');
                if(typeClient !== null){
                    if(typeClient.value === "professionnel"){
                        document.getElementById("rowStructure").classList.remove('d-none');
                        document.getElementById('kbis').classList.remove('d-none');
                    }
                    typeClient.addEventListener('change', function(event){
                        if(typeClient.value === "professionnel"){
                            document.getElementById("rowStructure").classList.remove('d-none');
                            document.getElementById('kbis').classList.remove('d-none');
                            document.getElementById("rowStructure").classList.add('animate__animated', 'animate__fadeIn');
                            document.getElementById('kbis').classList.add('animate__animated', 'animate__fadeIn');
                        }else{
                            document.getElementById("rowStructure").classList.add('d-none');
                            document.getElementById('kbis').classList.add('d-none');
                            document.getElementById("rowStructure").classList.remove('animate__animated', 'animate__fadeIn');
                            document.getElementById('kbis').classList.remove('animate__animated', 'animate__fadeIn');
                        }
                    });
                }
                // -- visuel sur le nom de jeune fille --
                let valcivility = document.querySelector('input[name=customer2\\[civility\\]]:checked').value;
                console.log(valcivility);
                if (valcivility > 1){
                    document.getElementById('customer2_maidenName').classList.remove('d-none');
                }
                const radioButtons = document.querySelectorAll('input[name=customer2\\[civility\\]]');
                console.log(radioButtons);
                radioButtons.forEach(function(radio) {
                    radio.addEventListener("change", function() {
                        if (parseInt(this.value) === 2) {
                            document.getElementById('customer2_maidenName').classList.remove('d-none');
                        } else if (parseInt(this.value) === 1){
                            document.getElementById('customer2_maidenName').classList.add('d-none');
                        }
                    });
                });
                loadEventStep2();
                // PARTIE Codepostal sur création & modification du client
                // PARTIE Code postal et Ville - API
                // ---------------------------------------
                let commune2 = modalCustomerBs.querySelector('.modal-body #customer2_city');
                let zipcode2 = modalCustomerBs.querySelector('.modal-body #customer2_zipcode');
                let SelectCity = modalCustomerBs.querySelector('.modal-body #selectcity');
                zipcode2.addEventListener('input', function(event){
                    if(zipcode2.value.length === 5)
                    {
                        let coord = this.value;
                        axios
                            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
                            .then(function(response){
                                let features = response.data;
                                removeOptions(SelectCity);
                                features.forEach((element) => {
                                    let name = element['codePostal']+" - "+element['nomCommune'];
                                    let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                                    SelectCity.options.add(OptSelectCity);
                                });

                                if (SelectCity.options.length === 1){
                                    let value = SelectCity.value.split(' ');
                                    zipcode2.value = value[0];
                                    commune2.value = value[2].toUpperCase();
                                }else{
                                    let value = SelectCity.value.split(' ');
                                    zipcode2.value = value[0];
                                    commune2.value = value[2].toUpperCase();
                                }
                            });
                    }
                });
                SelectCity.addEventListener('change', function (event){
                    let value = this.value.split(' ');
                    zipcode2.value = value[0];
                    commune2.value = value[2].toUpperCase();
                });
            });
    }
    else if(crud === 'EDIT'){
        modalCustomerBs.querySelector('.modal-dialog').classList.add('modal-xl');
        modalCustomerBs.querySelector('.modal-title').textContent = contentTitle;
        modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer').innerHTML = 'Modifier la fiche du propriétaire';
        axios
            .get(url)
            .then(function(response){
                modalCustomerBs.querySelector('.modal-body').innerHTML = response.data.formView;
                // -- visuel sur le nom de jeune fille --
                let valcivility = document.querySelector('input[name=customer2\\[civility\\]]:checked').value;
                if (valcivility > 1){
                    document.getElementById('customer2_maidenName').classList.remove('d-none');
                }
                const radioButtons = document.querySelectorAll('input[name=customer2\\[civility\\]]');
                radioButtons.forEach(function(radio) {
                    radio.addEventListener("change", function() {
                        if (parseInt(this.value) === 2) {
                            document.getElementById('customer2_maidenName').classList.remove('d-none');
                        } else if (parseInt(this.value) === 1){
                            document.getElementById('customer2_maidenName').classList.add('d-none');
                        }
                    });
                });
                let typeClient = modalCustomerBs.querySelector('.modal-body #customer2_typeClient');
                if(typeClient !== null){
                    if(typeClient.value === "professionnel"){
                        document.getElementById("rowStructure").classList.remove('d-none');
                        document.getElementById('kbis').classList.remove('d-none');
                    }
                    typeClient.addEventListener('change', function(event){
                        if(typeClient.value === "professionnel"){
                            document.getElementById("rowStructure").classList.remove('d-none');
                            document.getElementById('kbis').classList.remove('d-none');
                            document.getElementById("rowStructure").classList.add('animate__animated', 'animate__fadeIn');
                            document.getElementById('kbis').classList.add('animate__animated', 'animate__fadeIn');
                        }else{
                            document.getElementById("rowStructure").classList.add('d-none');
                            document.getElementById('kbis').classList.add('d-none');
                            document.getElementById("rowStructure").classList.remove('animate__animated', 'animate__fadeIn');
                            document.getElementById('kbis').classList.remove('animate__animated', 'animate__fadeIn');
                        }
                    });
                }
                loadEventStep2();
                let commune2 = modalCustomerBs.querySelector('.modal-body #customer2_city');
                let zipcode2 = modalCustomerBs.querySelector('.modal-body #customer2_zipcode');
                let SelectCity = modalCustomerBs.querySelector('.modal-body #selectcity');
                zipcode2.addEventListener('input', function(event){
                    if(zipcode2.value.length === 5)
                    {
                        let coord = this.value;
                        axios
                            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
                            .then(function(response){
                                let features = response.data;
                                removeOptions(SelectCity);
                                features.forEach((element) => {
                                    let name = element['codePostal']+" - "+element['nomCommune'];
                                    let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                                    SelectCity.options.add(OptSelectCity);
                                });

                                if (SelectCity.options.length === 1){
                                    let value = SelectCity.value.split(' ');
                                    zipcode2.value = value[0];
                                    commune2.value = value[2].toUpperCase();
                                }else{
                                    let value = SelectCity.value.split(' ');
                                    zipcode2.value = value[0];
                                    commune2.value = value[2].toUpperCase();
                                }
                            });
                    }
                });
                SelectCity.addEventListener('change', function (event){
                    let value = this.value.split(' ');
                    zipcode2.value = value[0];
                    commune2.value = value[2].toUpperCase();
                });
            });
    }
    else if(crud === 'DEL'){
        modalCustomerBs.querySelector('.modal-dialog').classList.remove('modal-xl');
        modalCustomerBs.querySelector('.modal-title').textContent = contentTitle;
        modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer').innerHTML = 'Supprimer le propriétaire';
        modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer').href = url;
        modalCustomerBs.querySelector('.modal-body').innerHTML = "<p><b>Attention :</b><br>"+
            "Vous êtez sur le point de détacher ce propriétaiure de ce bien. <br> Pour continuer, cliquez sur le bouton \"supprimer\".</p>";
        loadEventStep2();
    }
}

function submitCustomer(event){
    event.preventDefault();
    let form = document.getElementById('FormEditCustomer');
    if (form !== null){
        let data = new FormData(form);
        let action = form.action;
        axios
            .post(action, data)
            .then(function(response){
                document.getElementById('listeCustomers').innerHTML = response.data.liste;
                loadEventStep2();
                toasterMessage(response.data.message);
            })
            .catch(function(error){
                console.log(error);
            })
        ;

    }
    else {
        let url = this.href;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('listeCustomers').innerHTML = response.data.liste;
                toasterMessage(response.data.message);
                loadEventStep2();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }

}

civility.forEach(function(radio) {
    radio.addEventListener("change", function () {
        if (this.value === "2") {
            maidenName.style.display = "block"; // Afficher le champ input
        } else {
            maidenName.style.display = "none"; // Masquer le champ input
        }
    });
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

function loadEventStep2(){
    document.querySelectorAll('.btnShownCustomer').forEach(function(link){
        link.addEventListener('click', showModalCustomer);
    });
    document.querySelectorAll('.btnEditCustomer').forEach(function(link){
        link.addEventListener('click', showModalCustomer);
    });
    if(modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer') !== null){
        modalCustomerBs.querySelector('.modal-footer #btnSubmitCustomer').addEventListener('click', submitCustomer);
    }
}

