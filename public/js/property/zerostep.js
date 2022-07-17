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