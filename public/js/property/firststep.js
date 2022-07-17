// récupération des id des panels
const navInformations = document.getElementById("navInformations");
const navCustomers = document.getElementById("navCustomers");
const navEstimate = document.getElementById("navEstimate");
const navOptions = document.getElementById("navOptions");
const navGallery = document.getElementById("navGallery");
const navPublication = document.getElementById("navPublication");

// récupération des boutons de navigation entre panel
const stepBack = document.getElementById("stepBack");
const nextStepInformations = document.getElementById("stepInformations");
const nextStepCustomers = document.getElementById("stepCustomers");
const nextStepEstimate = document.getElementById("stepEstimate");
const nextStepOptions = document.getElementById("stepOptions");
const nextStepGallery = document.getElementById("stepGallery");
const nextStepPublication = document.getElementById("stepPublication");

// Div de chaque panels
const Informations = document.getElementById("Informations");
const Customers = document.getElementById("Customers");
const Estimate = document.getElementById("Estimate");
const Options = document.getElementById("Options");
const Gallery = document.getElementById("Gallery");
const Publication = document.getElementById("Publication");

// ---------------------------------------
//
// STEP 0 :
// Mise en place des styles de démarrage
//
// ---------------------------------------

// Désactivation des onglets d'accueil de la page d'édition
navCustomers.className = "nav-link disabled";
navEstimate.className = "nav-link disabled";
navOptions.className = "nav-link disabled";
navGallery.className = "nav-link disabled";
navPublication.className = "nav-link disabled";
// Masquage des boutons de navigation sous le formulaire
nextStepCustomers.className += " d-none";
nextStepEstimate.className += " d-none";
nextStepOptions.className += " d-none";
nextStepGallery.className += " d-none";
nextStepPublication.className += " d-none";
// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickr", {
    "locale": "fr",
    enableTime: true,
    allowInput:true,
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
// Module éditeur
ClassicEditor
    .create(document.querySelector('#property_step1_annonce'), {
        height: 50
    })
    .catch(error => {
        console.error(error);
    });

// ---------------------------------------
// STEP 1 :
// enregistrement des informations initiales du bien sur clic du boutons next
//
// ---------------------------------------

// initialisation du focus sur le champs refMandat
const focus = document.getElementById('property_step1_refMandat');
focus.focus()

// Récupération et envoie des données du formulaire
let FormAddInformation = document.getElementById('FormAddInformation')
FormAddInformation.addEventListener('submit', function (event) {
    event.preventDefault()
    let urladdInformation = FormAddInformation.action
    let dataaddInformation = new FormData(FormAddInformation)
    axios
        .post(urladdInformation, dataaddInformation)
        .then(function(response)
        {
            // modification des classes pour la navigation entre panels
            nextStepCustomers.className = "btn btn-sm btn-outline-primary";
            navInformations.className = "nav-link disabled";
            navCustomers.className = "nav-link active";
            Informations.className = "tab-pane";
            Customers.className += " active";
        })
        .catch(function(error){
            console.log(error);
        })
})