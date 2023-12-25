// PARTIE Ajout d'un vendeur en Javascript
// -----------------------------------------

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

const valueProperty = document.getElementById('valueProperty').value;

let validStep1 = document.getElementById('btnToStepTwo');
let validStep2 = document.getElementById('btnToStepTree');
let validStep3 = document.getElementById('btnToStepFour');
let validAdminStep3 = document.getElementById('btnWaitToStepFour');
let validStep4 = document.getElementById('btnToStepFive');
let validAdminStep4 = document.getElementById('btnWaitToStepFive');

let step1 = document.getElementById('stepOne');
let step2 = document.getElementById('stepTwo');
let step3 = document.getElementById('stepTree');
let step4 = document.getElementById('stepFour');
let step5 = document.getElementById('stepFive');

let icoStepOne = document.getElementById('icoStepOne');
let icoStepTwo = document.getElementById('icoStepTwo');
let icoStepTree = document.getElementById('icoStepTree');
let icoStepFour = document.getElementById('icoStepFour');
let icoStepFive = document.getElementById('icoStepFive');

const FormStep2 = document.getElementById('transactionstep2');
const FormStep3 = document.getElementById('transactionstep3');
const FormStep4 = document.getElementById('transactionstep4');
const FormStep5 = document.getElementById('transactionstep5');
const blocks = document.getElementById("blocks");
// effet sur les icones de la barre de suivi
icoStepOne.addEventListener('click', function(event){
    let blockchild = blocks.querySelector('.activ');
    if (blockchild !== null) {
        // 👇️ this runs
        console.log('✅ element has child with id of child-3');
        blockchild.classList.add('d-none');
        blockchild.classList.remove('activ');
        step1.classList.add('activ');
        step1.classList.remove('d-none');
    } else {
        console.log('⛔️ element does NOT have child with id');
    }
});
icoStepTwo.addEventListener('click', function(event){
    let blockchild = blocks.querySelector('.activ');
    if (blockchild !== null) {
        // 👇️ this runs
        console.log('✅ element has child with id of child-3');
        blockchild.classList.add('d-none');
        blockchild.classList.remove('activ');
        step2.classList.add('activ');
        step2.classList.remove('d-none');
    } else {
        console.log('⛔️ element does NOT have child with id');
    }
});
icoStepTree.addEventListener('click', function(event){
    let blockchild = blocks.querySelector('.activ');
    if (blockchild !== null) {
        // 👇️ this runs
        console.log('✅ element has child with id of child-3');
        blockchild.classList.add('d-none');
        blockchild.classList.remove('activ');
        step3.classList.add('activ');
        step3.classList.remove('d-none');
    } else {
        console.log('⛔️ element does NOT have child with id');
    }
});
icoStepFour.addEventListener('click', function(event){
    let blockchild = blocks.querySelector('.activ');
    if (blockchild !== null) {
        // 👇️ this runs
        console.log('✅ element has child with id of child-3');
        blockchild.classList.add('d-none');
        blockchild.classList.remove('activ');
        step4.classList.add('activ');
        step4.classList.remove('d-none');
    } else {
        console.log('⛔️ element does NOT have child with id');
    }
});
icoStepFive.addEventListener('click', function(event){
    let blockchild = blocks.querySelector('.activ');
    if (blockchild !== null) {
        // 👇️ this runs
        console.log('✅ element has child with id of child-3');
        blockchild.classList.add('d-none');
        blockchild.classList.remove('activ');
        step5.classList.add('activ');
        step5.classList.remove('d-none');
    } else {
        console.log('⛔️ element does NOT have child with id');
    }
});
icoStepTwo.addEventListener('click', function(event){
    step3.classList.add('d-none');
    step2.classList.remove('d-none');
});

// validation étapes acheteurs
validStep1.addEventListener('click', function(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            let code = response.data.code;
            if(code == 200){
                step2.classList.remove('d-none');
                step1.classList.add('d-none');
                document.getElementById('transState').innerHTML = response.data.transState;
            }else {
                // initialisation du toaster
                let toastHTMLElement = document.getElementById("toaster");
                let message = response.data.message;
                let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
                toastBody.textContent = message;
                let toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000 });
                toastElement.show();
            }

        })
        .catch(function (error) {
            console.log(error);
        });
});
// validation étape promesse de vente
validStep2.addEventListener('click', function(event){
    event.preventDefault();
    let actionForm = FormStep2.action;
    let dataForm = new FormData(FormStep2);
    axios
        .post(actionForm, dataForm)
        .then(function(response){
            step3.classList.remove('d-none');
            step2.classList.add('d-none');
            document.getElementById('transState').innerHTML = response.data.transState;
        })
        .catch(function (error) {
            console.log(error);
        });
});
// validation Dépôt de doc compromis
validStep3.addEventListener('click', function(event){
    event.preventDefault();
    let actionForm = FormStep3.action;
    let dataForm = new FormData(FormStep3);
    axios
        .post(actionForm, dataForm)
        .then(function(response){
            let code = response.data.code;
            if(code === 200){
                document.getElementById('transState').innerHTML = response.data.transState;
                document.getElementById('stepTree').innerHTML = response.data.step;
            }else{
                // initialisation du toaster
                let toastHTMLElement = document.getElementById("toaster");
                let message = response.data.message;
                let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
                toastBody.textContent = message;
                let toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000 });
                toastElement.show();
            }

        })
        .catch(function (error) {
            console.log(error);
        });
});
validAdminStep3.addEventListener('click', function(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            step4.classList.remove('d-none');
            step3.classList.add('d-none');
        })
        .catch(function (error) {
            console.log(error);
        });
});
// validation de l'acte de vente
validStep4.addEventListener('click', function(event){
    event.preventDefault();
    let actionForm = FormStep4.action;
    let dataForm = new FormData(FormStep4);
    axios
        .post(actionForm, dataForm)
        .then(function(response){
            let btnValidStep = document.getElementById('btnToStepFive');
            let btnWaitStep = document.getElementById('btnWaitToStepFive');
            // Mettre en place le bouton de validation du compromis de vente
            btnWaitStep.classList.remove('d-none');
            btnValidStep.classList.add('d-none');
        })
        .catch(function (error) {
            console.log(error);
        });
});

validAdminStep4.addEventListener('click', function(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            step5.classList.remove('d-none');
            step4.classList.add('d-none');
        })
        .catch(function (error) {
            console.log(error);
        });
});

const modalCustomer = document.getElementById('modalCustomer');
let btnSubmitCustomer = document.getElementById('btnSubmitCustomer');
modalCustomer.addEventListener('show.bs.modal', function (event){
    // Button that triggered the modal
    let button = event.relatedTarget;
    // extraction de la variable
    let recipient = button.getAttribute('data-bs-whatever');
    let crud = recipient.split('-')[0];
    let contentTitle = recipient.split('-')[1];
    let id = recipient.split('-')[2];
    let form = modalCustomer.querySelector('.modalBodyForm');
    let modalSubmit = modalCustomer.querySelector('.modal-footer a');
});

function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}

// PARTIE Codepostal sur création & modification du client
// ---------------------------------------
let commune2 = document.getElementById('customer2_city');
let zipcode2 = document.getElementById('customer2_zipcode');
let SelectCity2 = document.getElementById('selectcity2');
let addresseInput2 = document.getElementById('customer2_adress');
if(zipcode2 !== null) {
    zipcode2.addEventListener('input', function (event) {
        if (zipcode2.value.length === 5) {
            let coord = this.value;
            axios
                .get('https://apicarto.ign.fr/api/codes-postaux/communes/' + coord)
                .then(function (response) {
                    let features = response.data;
                    removeOptions(SelectCity2);
                    features.forEach((element) => {
                        let name = element['codePostal'] + " - " + element['nomCommune'];
                        let OptSelectCity = new Option(name.toUpperCase(), name.toUpperCase(), false, true);
                        SelectCity2.options.add(OptSelectCity);
                    });
                    if (SelectCity2.options.length === 1) {
                        let value = SelectCity2.value.split(' ');
                        zipcode2.value = value[0];
                        commune2.value = value[2].toUpperCase();
                    } else {
                        let value = SelectCity2.value.split(' ');
                        zipcode2.value = value[0];
                        commune2.value = value[2].toUpperCase();
                    }
                });
        }
    });
    SelectCity2.addEventListener('change', function (event){
        let value = this.value.split(' ');
        zipcode2.value = value[0];
        commune2.value = value[2].toUpperCase();
    });
}



function submitCustomer(event){
    event.preventDefault;
    let formCustomer = document.getElementById('FormEditCustomer');
    let action = formCustomer.action;
    let data = new FormData(formCustomer);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('ListTransactCustomers').innerHTML = response.data.liste;
            btnSubmitCustomer.addEventListener('click', submitCustomer);
        });
}
btnSubmitCustomer.addEventListener('click', submitCustomer);