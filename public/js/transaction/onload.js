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
// Préparation de la Modal d'ajout d'un vendeur à la fiche
const modalAddCustomer = document.getElementById('modalCustomer');
const btnAddCustomer = document.getElementById('btnAddCustomer');

let validStep1 = document.getElementById('btnToStepTwo');
let validStep2 = document.getElementById('btnToStepTree');
let validStep3 = document.getElementById('btnToStepFour');
let validStep4 = document.getElementById('btnToStepFive');
let validStep5 = document.getElementById('btnToStepSix');

let step1 = document.getElementById('stepOne');
let step2 = document.getElementById('stepTwo');
let step3 = document.getElementById('stepTree');
let step4 = document.getElementById('stepFour');
let step5 = document.getElementById('stepFive');
let step6 = document.getElementById('stepSix');

let icoStepOne = document.getElementById('icoStepOne');
let icoStepTwo = document.getElementById('icoStepTwo');
let icoSteptree = document.getElementById('icoSteptree');
let icoStepFour = document.getElementById('icoStepFour');
let icoStepFive = document.getElementById('icoStepFive');

const FormStep2 = document.getElementById('transactionstep2');
const FormStep3 = document.getElementById('transactionstep3');
const FormStep4 = document.getElementById('transactionstep4');
const FormStep5 = document.getElementById('transactionstep5');
const Blocks = document.getElementById("blocks");
icoStepOne.addEventListener('click', function(event){
    step2.classList.add('d-none');
    step1.classList.remove('d-none');
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
            step2.classList.remove('d-none');
            step1.classList.add('d-none');
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
        })
        .catch(function (error) {
            console.log(error);
        });
});
// validation offre de prêt
validStep3.addEventListener('click', function(event){
    event.preventDefault();
    let actionForm = FormStep3.action;
    let dataForm = new FormData(FormStep3);
    axios
        .post(actionForm, dataForm)
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
            step5.classList.remove('d-none');
            step4.classList.add('d-none');
        })
        .catch(function (error) {
            console.log(error);
        });
});
// validation de la remise des clefs
validStep5.addEventListener('click', function(event){
    event.preventDefault();
    let actionForm = FormStep5.action;
    let dataForm = new FormData(FormStep5);
    axios
        .post(actionForm, dataForm)
        .then(function(response){
            step6.classList.remove('d-none');
            step5.classList.add('d-none');
        })
        .catch(function (error) {
            console.log(error);
        });
});

modalAddCustomer.addEventListener('show.bs.modal', function (event) {
    // Button that triggered the modal
    var button = event.relatedTarget;
    // extraction de la variable
    var recipient = button.getAttribute('data-bs-whatever');
    // mise à jour du lien de soumission du formulaire.
    var modalContent = modalAddCustomer.querySelector('.modal-footer');
    var modalSubmit = modalAddCustomer.querySelector('.modal-footer a');
    modalSubmit.href = '/gestapp/customer/addcustomerjson/2/' + recipient;
});
