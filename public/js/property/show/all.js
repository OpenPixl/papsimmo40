// ---------------------------------------
// STEP 0 :
// Mise en place des options de chargement
// ---------------------------------------

// Preparation "Liste dynamique" - Récupération de l'id dans l'url
const urlCourante = document.location.href.replace(/\/$/, "");
const idProperty = urlCourante.substring (urlCourante.lastIndexOf( "/" ) + 1 );
let city = document.getElementById('City')
let family = document.getElementById('property_step1_family')
let rubric = document.getElementById('property_step1_rubric')
let rubricss = document.getElementById('property_step1_rubricss')
let FamValue = family.value
let RubValue = rubric.value
let RubssValue = rubricss.value

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

// Boutons de validation si le bien n'est plus en cours de création
const nextStepInformations2 = document.getElementById("stepInformations2");
const stepOptions2 = document.getElementById("stepOptions2");
const nextStepPublication2 = document.getElementById("stepPublication2");

// Div de chaque panels
const Informations = document.getElementById("Informations");
const Customers = document.getElementById("Customers");
const Estimate = document.getElementById("Estimate");
const Options = document.getElementById("Options");
const Gallery = document.getElementById("Gallery");
const Publication = document.getElementById("Publication");

// Autres modules
//const AddAvenant = document.getElementById('addPriceAvenant')
const DetailPrice = document.getElementById('detailPrice')
const AddAvenant = document.getElementById('AddAvenant')
const page = document.location.href;
const idproperty = page.substring(page.lastIndexOf("/") + 1);
if (AddAvenant !== null) {
    AddAvenant.style.display = "none"
}
//Choix du type de Mandat
const isWithoutExclusivity = document.getElementById('property_step1_isWithoutExclusivity')
const isSemiExclusivity = document.getElementById('property_step1_isSemiExclusivity')
const isWithExclusivity= document.getElementById('property_step1_isWithExclusivity')

// Désactivation des onglets d'accueil de la page d'édition
if(navCustomers !== null){
    navCustomers.className = "nav-link disabled";
}
if(navCustomers !== null){
    navEstimate.className = "nav-link disabled";
}
if(navCustomers !== null){
    navOptions.className = "nav-link disabled";
}
if(navCustomers !== null){
    navGallery.className = "nav-link disabled";
}
if(navCustomers !== null){
    navPublication.className = "nav-link disabled";
}
// Masquage des boutons de navigation sous le formulaire
if(nextStepCustomers !== null){
    nextStepCustomers.className += " d-none";
}
if(nextStepEstimate !== null){
    nextStepEstimate.className += " d-none";
}
if(nextStepOptions !== null){
    nextStepOptions.className += " d-none";
}
if(nextStepGallery !== null){
    nextStepGallery.className += " d-none";
}
if(nextStepPublication !== null){
    nextStepPublication.className += " d-none";
}

// autres mises en place
// AddAvenant.style.display = 'none'
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

// ---------------------------------------
// STEP 1 : Enregistrement des informations initiales du bien sur clic du boutons next
// ---------------------------------------

// PARTIE Envoie et traitement du formulaire
// ---------------------------------------
// fonction édition du mandat
const AddMandat = document.getElementById('AddMandat')
const btnAddMandat = document.getElementById('btnAddMandat')
if(AddMandat !== null ){
    const modalAddMandat = new bootstrap.Modal(document.getElementById('modalAddMandat'),{focus:true,keyboard: false})
    AddMandat.addEventListener('click', function(event){
        event.preventDefault()
        modalAddMandat.show()
    })
}
if(btnAddMandat !== null ){
    btnAddMandat.addEventListener('click', function(event){
        event.preventDefault()
        let form = document.getElementById('FormAddMandat')
        let url = form.action
        let data = new FormData(form)
        axios
            .post(url, data)
            .then(function(response){
                window.location.reload();
                // initialisation du toaster
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
                toastBody.textContent = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, option);
                toastElement.show();

            })
            .catch(function (error) {
                console.log(error)
            })
    })
}

// initialisation du focus sur le champ refMandat
const focus = document.getElementById('property_step1_refMandat');

// Evènement sur l'exclusivité d'un mandat lors d'un clic "onChange"
if(isWithoutExclusivity.checked ) {
    isSemiExclusivity.onclick = function (event) {
        isWithoutExclusivity.checked = false
        isWithExclusivity.checked = false
    }
    isWithExclusivity.onclick = function (event) {
        isWithoutExclusivity.checked = false
        isSemiExclusivity.checked = false
    }
}
else if (isSemiExclusivity.checked) {
    isWithoutExclusivity.onclick = function (event) {
        isSemiExclusivity.checked = false
        isWithExclusivity.checked = false
    }
    isWithExclusivity.onclick = function (event) {
        isSemiExclusivity.checked = false
        isWithoutExclusivity.checked = false
    }
}
else if(isWithExclusivity.checked) {
    isSemiExclusivity.onclick = function (event) {
        isWithExclusivity.checked = false
        isWithoutExclusivity.checked = false
    }
    isWithoutExclusivity.onclick = function (event) {
        isWithExclusivity.checked = false
        isSemiExclusivity.checked = false
    }
}

// Récupération et envoie des données du formulaire
const FormAddInformation = document.getElementById('FormAddInformation')
FormAddInformation.addEventListener('submit', function (event) {
    event.preventDefault()
    let urladdInformation = FormAddInformation.action
    let dataaddInformation = new FormData(FormAddInformation)
    axios
        .post(urladdInformation, dataaddInformation)
        .then(function (response) {
            // modification des classes pour la navigation entre panels
            nextStepCustomers.className = "btn btn-sm btn-outline-primary";
            if(navInformations !== null){
                navInformations.className = "nav-link disabled";
            }
            if(navCustomers !== null){
                navCustomers.className = "nav-link active";
            }
            // initialisation du toaster
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
            toastBody.textContent = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000 });
            toastElement.show();
            Informations.className = "tab-pane";
            Customers.className += " active";
        })
        .catch(function (error) {
            console.log(error);
        })
})

// PARTIE Définition / Type de bien
// ---------------------------------------
// Ouverture de la modal
const modalPropertyDefinition = document.getElementById('modalPropertyDefinition')
const PropertyDefinition = new bootstrap.Modal(modalPropertyDefinition, { keyboard: true })
const addPropertyDefinition = document.getElementById('addPropertyDefinition')
if(addPropertyDefinition !== null){
    addPropertyDefinition.addEventListener('click', function(event){
        event.preventDefault()
        PropertyDefinition.show()
    })
}


// PARTIE Orientation du bien - Envoi du formulaire
let FormPropertyDefinition = document.getElementById('FormPropertyDefinition')
FormPropertyDefinition.addEventListener('submit', function (event) {
    event.preventDefault()
    let url_property_definition = '/gestapp/choice/property/definition/new2'
    let data_property_definition = new FormData(FormPropertyDefinition)
    axios
        .post(url_property_definition, data_property_definition)
        .then(function(response)
        {
            const selectpropertyDef = document.getElementById('property_step1_propertyDefinition');
            const optionspropertyDef = document.createElement("option");
            optionspropertyDef.text = response.data.propertyDef;
            optionspropertyDef.setAttribute("data-data", response.data.propertyDef)
            optionspropertyDef.setAttribute("value", response.data.valuepropertyDef)
            optionspropertyDef.text = response.data.propertyDef;
            selectpropertyDef.add(optionspropertyDef)
            tspropertyOrient.addOption({data: response.data.propertyDef, value:response.data.valuepropertyDef})
            tspropertyOrient.addItem(response.data.propertyDef)
            PropertyDefinition.hide()
        })
        .catch(function(error){
            console.log(error);
        })
})

if(nextStepInformations2 !== null){
    nextStepInformations2.addEventListener('click', function (event){
        event.preventDefault()
        let urladdInformation = FormAddInformation.action
        let dataaddInformation = new FormData(FormAddInformation)
        axios
            .post(urladdInformation, dataaddInformation)
            .then(function (response) {
                // préparation du toaster
                var option = {
                    animation: true,
                    autohide: true,
                    delay: 3000,
                };
                // initialisation du toaster
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
                toastBody.textContent = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, option);
                toastElement.show();
            })
            .catch(function (error) {
                console.log(error);
            })
    })
}

// PARTIE Destination du bien
// ---------------------------------------
function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}

// Alimentation du Select Transaction
let rubricsData = []                                // Accueillera les valeurs des rubriques de biens
let rubricssData = []                               // Accueillera les valeurs des sous-rubriques de biens

// Remplissage du Select des rubriques si bien déjà enregistré.
if(rubric.value >= 1){
    axios
        .get('/gestapp/choice/property/family/rubric/' + FamValue)
        .then(function(response){
            let rubricsValues = response.data.rubrics
            // suppression des options du select
            removeOptions(rubric);
            let test = []
            rubricsValues.forEach((element) => {
                if (element.id === parseInt(RubValue)){
                    let newOption = new Option (element.name, element.id, false, true);
                    rubric.options.add (newOption);
                }else{
                    let newOption = new Option (element.name, element.id);
                    rubric.options.add (newOption);
                }
            })
        })
}
// Remplissage du Select des sous-rubriques si bien déjà enregistré.
if(rubricss.value >= 1){
    axios
        .get('/gestapp/choice/property/family/rubricss/' + RubValue)
        .then(function(response){
            let rubricsValues = response.data.rubricss
            // suppression des options du select
            removeOptions(rubricss);
            rubricsValues.forEach((element) => {
                if (element.id === parseInt(RubssValue)){
                    let newOption = new Option (element.name, element.id, false, true);
                    rubricss.options.add (newOption);
                }else{
                    let newOption = new Option (element.name, element.id);
                    rubricss.options.add (newOption);
                }
            })
        })
}

// Opération sur changement du select Transaction
family.addEventListener('change', function(event){
    let FamValue = parseInt(this.value)
    axios
        .get('/gestapp/choice/property/family/rubric/' + FamValue)
        .then(function(response){
            let rubricsValues = response.data.rubrics
            // suppression des options du select
            removeOptions(rubric);
            rubricsValues.forEach((element) => {
                if (element.id === parseInt(RubValue)){
                    let newOption = new Option (element.name, element.id, false, true);
                    rubric.options.add (newOption);
                }else{
                    let newOption = new Option (element.name, element.id);
                    rubric.options.add (newOption);
                }
            })
        })
    document.getElementById('rowRubric').removeAttribute('style')
})

// Opération sur changement du select Rubrique
rubric.addEventListener('change', function(event){
    let RubValue = parseInt(this.value)
    console.log(RubValue)
    axios
        .get('/gestapp/choice/property/family/rubricss/' + RubValue)
        .then(function(response){
            let rubricsValues = response.data.rubricss
            console.log(rubricsValues)
            // suppression des options du select
            removeOptions(rubricss);
            if(rubricsValues.length >= 1){
                document.getElementById('rowRubricss').removeAttribute('style')
            }else{
                document.getElementById('rowRubricss').setAttribute("style", 'display:none;')
            }
            rubricsValues.forEach((element) => {
                if (element.id === parseInt(RubssValue)){
                    let newOption = new Option (element.name, element.id, false, true);
                    rubricss.options.add (newOption);
                }else{
                    let newOption = new Option (element.name, element.id);
                    rubricss.options.add (newOption);
                }
            })
        })
})

// PARTIE Code postal et Ville - API
// ---------------------------------------
let commune = document.getElementById('property_step1_city')
let zipcode = document.getElementById('property_step1_zipcode')
let SelectCity = document.getElementById('selectcity')
let addresseInput = document.getElementById('property_step1_adress')
zipcode.addEventListener('input', function(event){
    if(zipcode.value.length === 5)
    {
        let coord = this.value
        axios
            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
            .then(function(response){
                let features = response.data
                removeOptions(SelectCity);
                features.forEach((element) => {
                    let name = element['codePostal']+" - "+element['nomCommune']
                    let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                    SelectCity.options.add(OptSelectCity);
                })
                if (SelectCity.options.length === 1){
                    let value = SelectCity.value.split(' ')
                    zipcode.value = value[0]
                    commune.value = value[2].toUpperCase()
                }else{
                    let value = SelectCity.value.split(' ')
                    zipcode.value = value[0]
                    commune.value = value[2].toUpperCase()
                }
            })
    }
})
SelectCity.addEventListener('change', function (event){
    let value = this.value.split(' ')
    console.log(value)
    zipcode.value = value[0]
    commune.value = value[2].toUpperCase()
})

// PARTIE Codepostal sur création & modification du client
// PARTIE Code postal et Ville - API
// ---------------------------------------
let commune2 = document.getElementById('customer2_city')
let zipcode2 = document.getElementById('customer2_zipcode')
let SelectCity2 = document.getElementById('selectcity2')
let addresseInput2 = document.getElementById('pcustomer2_adress')
if(zipcode2 !== null){
    zipcode2.addEventListener('input', function(event){
        if(zipcode2.value.length === 5)
        {
            let coord = this.value
            axios
                .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
                .then(function(response){
                    let features = response.data
                    removeOptions(SelectCity);
                    features.forEach((element) => {
                        let name = element['codePostal']+" - "+element['nomCommune']
                        let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                        SelectCity2.options.add(OptSelectCity);
                    })
                    if (SelectCity2.options.length === 1){
                        let value = SelectCity2.value.split(' ')
                        zipcode2.value = value[0]
                        commune2.value = value[2].toUpperCase()
                    }else{
                        let value = SelectCity2.value.split(' ')
                        zipcode2.value = value[0]
                        commune2.value = value[2].toUpperCase()
                    }
                })
        }
    })
    SelectCity2.addEventListener('change', function (event){
        let value = this.value.split(' ')
        console.log(value)
        zipcode2.value = value[0]
        commune2.value = value[2].toUpperCase()
    })
}

// Partie map
let carte = L.map('carte').setView([43.8909, -0.5009], 15);
L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributeurs',
    minZoom: 10,
    maxZoom: 16
}).addTo(carte);

// partie obtenir les coordonnées GPS
//let findGeoCoord = document.getElementById('findGeoCoord')
//findGeoCoord.addEventListener('click', getCoords)

//    event.preventDefault()
//    let url = this.href;
//    let adress = document.getElementById('property_step1_adress').value.replaceAll(' ','+')
//    let zipcode = document.getElementById('property_step1_zipcode').value
//    let commune = document.getElementById('property_step1_city').value
//    let queryString = adress + zipcode +'+'+ commune
//    let arrayCoord = []
//    axios
//        .get('https://api-adresse.data.gouv.fr/search/?q='+queryString+'=housenumber&limit=1')
//        .then(function(response){
//            arrayCoordX = response.data.features[0].geometry['coordinates'][0]
//            arrayCoordY = response.data.features[0].geometry['coordinates'][1]
//            arrayCoord.push(arrayCoordY)
//            arrayCoord.push(arrayCoordX)
//            axios
//                .post(url, {'arrayCoord': arrayCoord})
//                .then(function(response){
//                })
//        })
//}

// ---------------------------------------
// STEP 2 : Enregistrement du ou des acheteurs
// ---------------------------------------

// Action sur le clic du bouton nextstep
if(nextStepCustomers !== null){
    nextStepCustomers.onclick = function (event) {
        event.preventDefault()
        // visibilité des éléments
        nextStepCustomers.className += " d-none";
        nextStepEstimate.className = "btn btn-sm btn-outline-primary";
        if(navCustomers !== null){
            navCustomers.className = "nav-link disabled";
        }
        if(navEstimate !== null){
            navEstimate.className = "nav-link active";
        }
        Customers.className = "tab-pane";
        Estimate.className += " active";
    }
}

// PARTIE Ajout d'un vendeur en Javascript
// -----------------------------------------
// Préparation de la Modal d'ajout d'un vendeur à la fiche
const modalCustomer = document.getElementById('modalCustomer')

modalCustomer.addEventListener('show.bs.modal', function (event) {
    // Button that triggered the modal
    let a = event.relatedTarget
    // extraction de la variable
    let recipient = a.getAttribute('data-bs-data')
    let url = a.href
    let crud = recipient.split('-')[0]
    let contentTitle = recipient.split('-')[1]
    let id = recipient.split('-')[2]
    let namePage = recipient.split('-')[3]
    if(crud === 'ADD'){
        modalCustomer.querySelector('.modal-title').textContent = contentTitle
        axios
            .get(url)
            .then(function(response){
                modalCustomer.querySelector('.modal-body').innerHTML = response.data.formView
                // PARTIE Codepostal sur création & modification du client
                // PARTIE Code postal et Ville - API
                // ---------------------------------------
                let commune2 = document.getElementById('customer2_city')
                let zipcode2 = document.getElementById('customer2_zipcode')
                let SelectCity2 = document.getElementById('selectcity2')
                let addresseInput2 = document.getElementById('pcustomer2_adress')
                zipcode2.addEventListener('input', function(event){
                    if(zipcode2.value.length === 5)
                    {
                        let coord = this.value
                        axios
                            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
                            .then(function(response){
                                let features = response.data
                                removeOptions(SelectCity);
                                features.forEach((element) => {
                                    let name = element['codePostal']+" - "+element['nomCommune']
                                    let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                                    SelectCity2.options.add(OptSelectCity);
                                })
                                if (SelectCity2.options.length === 1){
                                    let value = SelectCity2.value.split(' ')
                                    zipcode2.value = value[0]
                                    commune2.value = value[2].toUpperCase()
                                }else{
                                    let value = SelectCity2.value.split(' ')
                                    zipcode2.value = value[0]
                                    commune2.value = value[2].toUpperCase()
                                }
                            })
                    }
                })
                SelectCity2.addEventListener('change', function (event){
                    let value = this.value.split(' ')
                    console.log(value)
                    zipcode2.value = value[0]
                    commune2.value = value[2].toUpperCase()
                })

            })
    }else if(crud === 'EDIT'){
        modalCustomer.querySelector('.modal-title').textContent = contentTitle
        axios
            .get(url)
            .then(function(response){
                modalCustomer.querySelector('.modal-body').innerHTML = response.data.formView
                // PARTIE Codepostal sur création & modification du client
                // PARTIE Code postal et Ville - API
                // ---------------------------------------
                let commune2 = document.getElementById('customer2_city')
                let zipcode2 = document.getElementById('customer2_zipcode')
                let SelectCity2 = document.getElementById('selectcity2')
                let addresseInput2 = document.getElementById('pcustomer2_adress')
                if(zipcode2 !== null){
                    zipcode2.addEventListener('input', function(event){
                        if(zipcode2.value.length === 5)
                        {
                            let coord = this.value
                            axios
                                .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
                                .then(function(response){
                                    let features = response.data
                                    removeOptions(SelectCity);
                                    features.forEach((element) => {
                                        let name = element['codePostal']+" - "+element['nomCommune']
                                        let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                                        SelectCity2.options.add(OptSelectCity);
                                    })
                                    if (SelectCity2.options.length === 1){
                                        let value = SelectCity2.value.split(' ')
                                        zipcode2.value = value[0]
                                        commune2.value = value[2].toUpperCase()
                                    }else{
                                        let value = SelectCity2.value.split(' ')
                                        zipcode2.value = value[0]
                                        commune2.value = value[2].toUpperCase()
                                    }
                                })
                        }
                    })
                    SelectCity2.addEventListener('change', function (event){
                        let value = this.value.split(' ')
                        console.log(value)
                        zipcode2.value = value[0]
                        commune2.value = value[2].toUpperCase()
                    })
                }
            })
    }

})

// Evènement sur le bouton btnAddCustomers
const btnAddCustomer = document.getElementById('btnAddCustomer')
btnAddCustomer.onclick = function (event) {
    event.preventDefault()
    // récupération des élements nécéssaire à la création du client depuis le bouton
    let urlEditCustomer = this.href
    let FormEditCustomer = document.getElementById('FormEditCustomer')
    let dataEditCustomer = new FormData(FormEditCustomer)
    // envoie des données
    axios
        .post(urlEditCustomer, dataEditCustomer)
        .then(function (response) {
            const liste = document.getElementById('listeCustomers').innerHTML = response.data.liste;
            // modalAddCustomer.addEventListener('hide.bs.modal')
            // Rajouter les events
            // Ajout d'un évènement sur le click AddCustomerSearch
            document.querySelectorAll('a.addcustomersearch').forEach(function (link) {
                link.addEventListener('click', AddCustomerSearch);
            })
            // Ajout d'un évènement sur le click supprcustomer
            document.querySelectorAll('a.supprcustomer').forEach(function (link) {
                link.addEventListener('click', SupprCustomer);
            })
            // Ajout d'un évènement sur le click du selecteur EditCustomer
            document.querySelectorAll('a.editCustomer').forEach(function (link) {
                link.addEventListener('click', EditCustomer);
            })
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
}

function submitCustomer(event){
    event.preventDefault()
}

// fonction d'édition de la fiche Client
function EditCustomer(event){
    event.preventDefault()
    let urlEditCustomer = this.href
    let FormEditCustomer = document.querySelector('.formEditCustomer')
    let dataEditCustomer = new FormData(FormEditCustomer)
    axios
        .post(urlEditCustomer, dataEditCustomer)
        .then(function(response){
            const liste = document.getElementById('listeCustomers').innerHTML = response.data.liste
            // Ajout d'un évènement sur le click AddCustomerSearch
            document.querySelectorAll('a.addcustomersearch').forEach(function (link) {
                link.addEventListener('click', AddCustomerSearch);
            })
            // Ajout d'un évènement sur le click supprcustomer
            document.querySelectorAll('a.supprcustomer').forEach(function (link) {
                link.addEventListener('click', SupprCustomer);
            })
            // Ajout d'un évènement sur le click du selecteur EditCustomer
            document.querySelectorAll('a.editCustomer').forEach(function (link) {
                link.addEventListener('click', EditCustomer);
            })

        })
        .catch(function (error) {
            console.log(error);
        })

}

// Fonction de suppression d'un client
function SupprCustomer(event) {
    event.preventDefault()
    let url = this.href
    axios
        .post(url)
        .then(function (response) {
            const liste = document.getElementById('listeCustomers').innerHTML = response.data.liste;
            // Ajout d'un évènement sur le click AddCustomerSearch
            document.querySelectorAll('a.addcustomersearch').forEach(function (link) {
                link.addEventListener('click', AddCustomerSearch);
            })
            // Ajout d'un évènement sur le click supprcustomer
            document.querySelectorAll('a.supprcustomer').forEach(function (link) {
                link.addEventListener('click', SupprCustomer);
            })
            // Ajout d'un évènement sur le click du selecteur EditCustomer
            document.querySelectorAll('a.editCustomer').forEach(function (link) {
                link.addEventListener('click', EditCustomer);
            })
        })
}

// Ajout d'un évènement sur le click AddCustomerSearch
document.querySelectorAll('a.addcustomersearch').forEach(function (link) {
    link.addEventListener('click', AddCustomerSearch);
})
// Ajout d'un évènement sur le click supprcustomer
document.querySelectorAll('a.supprcustomer').forEach(function (link) {
    link.addEventListener('click', SupprCustomer);
})
// Ajout d'un évènement sur le click du selecteur EditCustomer
document.querySelectorAll('a.editCustomer').forEach(function (link) {
    link.addEventListener('click', EditCustomer);
})

// fonction d'ajout depuis l'évènement AddCustomerSearch
function AddCustomerSearch(event) {
    event.preventDefault()
    let url = this.href
    axios
        .post(url)
        .then(function (response) {
            document.getElementById('listeCustomers').innerHTML = response.data.liste
            // Ajout d'un évènement sur le click AddCustomerSearch
            document.querySelectorAll('a.addcustomersearch').forEach(function (link) {
                link.addEventListener('click', AddCustomerSearch);
            })
            // Ajout d'un évènement sur le click supprcustomer
            document.querySelectorAll('a.supprcustomer').forEach(function (link) {
                link.addEventListener('click', SupprCustomer);
            })
            // Ajout d'un évènement sur le click du selecteur EditCustomer
            document.querySelectorAll('a.editCustomer').forEach(function (link) {
                link.addEventListener('click', EditCustomer);
            })
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
}

// fonction de recherche "Customer" depuis le formulaire
document.getElementById('search_customers_rechercher').addEventListener('click', function (event) {
    event.preventDefault()
    let urlSearchCustomer = this.href
    let formsearchCustomer = new FormData()
    formsearchCustomer.append('search_customers[word]', document.getElementById('search_customers_word').value)
    formsearchCustomer.append('search_customers[_token]', document.getElementById('search_customers__token').value)
    axios
        .post(urlSearchCustomer, formsearchCustomer)
        .then(function (response) {
            document.getElementById('listeSearchCustomers').innerHTML = response.data.liste
            // Ajout d'un évènement sur le click AddCustomerSearch
            document.querySelectorAll('a.addcustomersearch').forEach(function (link) {
                link.addEventListener('click', AddCustomerSearch);
            })
            // Ajout d'un évènement sur le click supprcustomer
            document.querySelectorAll('a.supprcustomer').forEach(function (link) {
                link.addEventListener('click', SupprCustomer);
            })
            // Ajout d'un évènement sur le click du selecteur EditCustomer
            document.querySelectorAll('a.editCustomer').forEach(function (link) {
                link.addEventListener('click', EditCustomer);
            })
        })
        .catch(function (error) {
            // handle error
            console.log(error);
        })
})
if(document.getElementById('previousCustomer') !== null){
    document.getElementById('previousCustomer').addEventListener('click', function (event) {
        event.preventDefault()
        // visibilité des éléments
        nextStepCustomers.className += " d-none";
        nextStepInformations.className = "btn btn-sm btn-outline-primary";
        if(navCustomers !== null){
            navCustomers.className = "nav-link disabled";
        }
        if(navInformations !== null){
            navInformations.className = "nav-link active";
        }
        Customers.className = "tab-pane";
        Informations.className += " active";
    })
}

// Ajout d'un évènement sur le click AddCustomerSearch
document.querySelectorAll('a.addcustomersearch').forEach(function (link) {
    link.addEventListener('click', AddCustomerSearch);
})
// Ajout d'un évènement sur le click supprcustomer
document.querySelectorAll('a.supprcustomer').forEach(function (link) {
    link.addEventListener('click', SupprCustomer);
})
// Ajout d'un évènement sur le click du selecteur EditCustomer
document.querySelectorAll('a.editCustomer').forEach(function (link) {
    link.addEventListener('click', EditCustomer);
})

// ---------------------------------------
// STEP 3 : Enregistrement des chiffres
//
// ---------------------------------------

// Affichage du bloc sale ou rent selon l'orientation du bien
// ---------------------------------------
const sales = document.getElementById('sale')
const rent = document.getElementById('rent')
const rentCommerce = document.getElementById('rentCommerce')

if(family.value == 4 && rubric.value == 8) {
    let warranty = document.getElementById('warrantyDeposit')
    sales.classList.add('d-none')
    rent.classList.add('d-none')
    if(warranty !== null){
        document.getElementById('warrantyDeposit').remove()
    }
}else if (family.value == 5) {
    let warranty = document.getElementById('warrantyDeposit')
    sales.classList.add('d-none')
    rentCommerce.classList.add('d-none')
    if(warranty !== null){
        document.getElementById('warrantyDeposit').remove()
    }            }else{
    rent.classList.add('d-none')
    rentCommerce.classList.add('d-none')
}

// Calcul des taux et honoraires de PAPS
// ---------------------------------------
// Modification des tarifs selon le prix vendeur et les honoraires
document.getElementById('property_step2_price').addEventListener('change', function () {
    let price = parseInt(document.getElementById("property_step2_price").value)
    let honoraire = parseInt(document.getElementById("property_step2_honoraires").value)
    document.getElementById('property_step2_priceFai').value = price + honoraire
})
document.getElementById('property_step2_honoraires').addEventListener('change', function () {
    var price = parseInt(document.getElementById("property_step2_price").value)
    var honoraire = parseInt(document.getElementById("property_step2_honoraires").value)
    document.getElementById('property_step2_priceFai').value = price + honoraire
})

const property_avenant_price = document.getElementById('property_avenant_price')
if (property_avenant_price !== null) {
    property_avenant_price.addEventListener('change', function () {
        let price = parseInt(property_avenant_price.value)
        let honoraire = parseInt(document.getElementById("property_avenant_honoraires").value)
        document.getElementById('property_avenant_priceFai').value = price + honoraire
    })
}
const property_avenant_honoraires = document.getElementById('property_avenant_honoraires')
if (property_avenant_honoraires !== null) {
    property_avenant_honoraires.addEventListener('change', function () {
        let price = parseInt(property_avenant_price.value)
        let honoraire = parseInt(property_avenant_honoraires.value)
        document.getElementById('property_avenant_priceFai').value = price + honoraire
    })
}


// Logique Modal & Forms
// ---------------------------------------
// Avenant sur le tarif
if (document.getElementById('addAvenant') !== null){
    document.getElementById('addAvenant').addEventListener('click', function (event) {
        event.preventDefault()
        DetailPrice.style.display = 'none'
        AddAvenant.style.display = 'block'
    })
}
if (document.getElementById('BtnAvenantForm') !== null) {
    document.getElementById('BtnAvenantForm').addEventListener('click', function (event) {
        event.preventDefault()
        const property_avenant_price = document.getElementById('property_avenant_price').value
        const property_avenant_honoraires = document.getElementById('property_avenant_honoraires').value
        const property_avenant_priceFai = document.getElementById('property_avenant_priceFai').value
        const property_avenant__token = document.getElementById('property_avenant__token').value

        let FormAvenant = new FormData()
        FormAvenant.append('property_avenant[price]', property_avenant_price)
        FormAvenant.append('property_avenant[honoraires]', property_avenant_honoraires)
        FormAvenant.append('property_avenant[priceFai]', property_avenant_priceFai)
        FormAvenant.append('property_avenant[_token]', property_avenant__token)

        let urlAddavenant = '/gestapp/property/addAvenant/' + idproperty
        axios
            .post(urlAddavenant, FormAvenant)
            .then(function (response) {
                DetailPrice.style.display = 'block'
                AddAvenant.style.display = 'none'
                document.getElementById('property_step2_price').value = (property_avenant_price)
                document.getElementById('property_step2_honoraires').value = (property_avenant_honoraires)
                document.getElementById('property_step2_priceFai').value = (property_avenant_priceFai)
                // initialisation du toaster
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
                toastBody.textContent = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, {
                    animation: true,
                    autohide: true,
                    delay: 3000,
                });
                toastElement.show();
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
    })
}

// Validation
// ---------------------------------------
// Envoie du formulaire
const FormAddEstimate = document.getElementById('FormAddEstimate')
if(nextStepEstimate !== null ){
    nextStepEstimate.addEventListener('click', function(event){
        event.preventDefault()
        let property_step2_price = document.getElementById('property_step2_price')
        let property_step2_honoraires = document.getElementById('property_step2_honoraires')
        let property_step2_priceFai = document.getElementById('property_step2_priceFai')
        property_step2_price.removeAttribute('disabled')
        property_step2_honoraires.removeAttribute('disabled')
        property_step2_priceFai.removeAttribute('disabled')
        let urlAddEstimate = FormAddEstimate.action
        let dataAddEstimate = new FormData(FormAddEstimate)
        axios
            .post(urlAddEstimate, dataAddEstimate)
            .then(function(response)
            {
                // modification des classes pour la navigation entre panels
                property_step2_price.setAttribute('disabled', true)
                property_step2_honoraires.setAttribute('disabled', true)
                property_step2_priceFai.setAttribute('disabled', true)

                nextStepCustomers.className = " d-none"
                nextStepOptions.className = "btn btn-sm btn-outline-primary"
                if(navEstimate !== null){
                    navEstimate.className = "nav-link disabled";
                }
                if(navOptions !== null){
                    navOptions.className = "nav-link active";
                }
                Estimate.className = "tab-pane"
                Options.className += " active"
            })
            .catch(function(error){
                console.log(error);
            })
    })
}else{
    document.getElementById('stepEstimate2').addEventListener('click', function(event){
        event.preventDefault()
        let property_step2_price = document.getElementById('property_step2_price')
        let property_step2_honoraires = document.getElementById('property_step2_honoraires')
        let property_step2_priceFai = document.getElementById('property_step2_priceFai')
        property_step2_price.removeAttribute('disabled')
        property_step2_honoraires.removeAttribute('disabled')
        property_step2_priceFai.removeAttribute('disabled')
        let urlAddEstimate = FormAddEstimate.action
        let dataAddEstimate = new FormData(FormAddEstimate)
        axios
            .post(urlAddEstimate, dataAddEstimate)
            .then(function(response)
            {
                // préparation du toaster
                var option = {
                    animation: true,
                    autohide: true,
                    delay: 3000,
                };
                // initialisation du toaster
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
                toastBody.textContent = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, option);
                toastElement.show();
            })
            .catch(function(error){
                console.log(error);
            })
    })
}

document.getElementById('previousEstimate').addEventListener('click', function(event){
    event.preventDefault()
    // modification des classes pour la navigation entre panels
    nextStepEstimate.className = " d-none"
    nextStepCustomers.className = "btn btn-sm btn-outline-primary"
    if(navEstimate !== null){
        navEstimate.className = "nav-link disabled"
    }
    if(navEstimate !== null){
        navCustomers.className = "nav-link active"
    }
    Estimate.className = "tab-pane"
    Customers.className += " active"
})

// PARTIE Choix de diag
// ---------------------------------------
// Select
const tsdiagChoice = new TomSelect("#property_step2_diagChoice",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
const diagChoice = document.getElementById('property_step2_diagChoice')
// Affichage des inputs DPE et GPE dès le chargement de la page
if(diagChoice.value === 'non_obligatoire' || diagChoice.value === 'vierge' ){
    document.getElementById('block_dpeAt').className += " d-none";
    document.getElementById('block_diagResult').className += " d-none";
}
// Affichage des inputs DPE et GPE sur le changement de valeur
const changeTsDiag = function(){
    let value = tsdiagChoice.getValue()
    if(value === 'obligatoire'){
        console.log(value)
        document.getElementById('block_dpeAt').className = "row mb-1 mt-1 g-1";
        document.getElementById('block_diagResult').className = "row mb-1 mt-1 g-1";
    }else if(value === 'non_obligatoire' || value === 'vierge' ){
        console.log(value)
        document.getElementById('block_dpeAt').className += " d-none";
        document.getElementById('block_diagResult').className += " d-none";
    }
}
tsdiagChoice.on('change', changeTsDiag )


// Cadastre
// ---------------------------------------
// Ajout d'une zone cadastrale
// ouverture de la modale d'ajout
const modalAddCadaster = document.getElementById('addCadaster')
modalAddCadaster.addEventListener('show.bs.modal', function (event) {
    // Button that triggered the modal
    let button = event.relatedTarget
    // extraction de la variable
    let recipient = button.getAttribute('data-bs-whatever')
})
// création de la fonction d'ajout
const btnAddCadaster = document.getElementById('btnAddCadaster')
btnAddCadaster.onclick = function(event) {
    let formAddCadaster = document.getElementById('formAddCadaster')
    let urlAddCadaster = formAddCadaster.action
    let dataAddCadaster = new FormData(formAddCadaster)
    axios
        .post(urlAddCadaster, dataAddCadaster)
        .then(function(response){
            const listeCadaster = document.getElementById('listeCadaster').innerHTML = response.data.listeCadaster
            const surfaceLand = document.getElementById('property_step2_surfaceLand').value = response.data.m2
            // préparation du toaster
            var option = {
                animation: true,
                autohide: true,
                delay: 3000,
            };
            // initialisation du toaster
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
            toastBody.textContent = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, option);
            toastElement.show();
            // Ajout d'un évènement sur le click AddCustomerSearch
            document.querySelectorAll('a.supprCadaster').forEach(function (link) {
                link.addEventListener('click', supprCadaster);
            })
        })
        .catch(function(error){
            console.log(error)
        })
}

// ---------------------------------------
// Edition d'une zone cadastrale
// ---------------------------------------
// Suppression d'une zone cadastrale
function supprCadaster(event) {
    event.preventDefault()
    let urlSupprCadaster = this.href
    axios
        .post(urlSupprCadaster)
        .then(function(response){
            const listeCadaster = document.getElementById('listeCadaster').innerHTML = response.data.listeCadaster
            const surfaceLand = document.getElementById('property_step2_surfaceLand').value = response.data.m2
            // préparation du toaster
            var option = {
                animation: true,
                autohide: true,
                delay: 3000,
            };
            // initialisation du toaster
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
            toastBody.textContent = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, option);
            toastElement.show();

            // Ajout d'un évènement sur le click AddCustomerSearch
            document.querySelectorAll('a.supprCadaster').forEach(function (link) {
                link.addEventListener('click', supprCadaster);
            })
        })
        .catch(function(error){
            console.log(error)
        })
}
// Ajout d'un évènement sur le click AddCustomerSearch
document.querySelectorAll('a.supprCadaster').forEach(function (link) {
    link.addEventListener('click', supprCadaster);
})


// ---------------------------------------
// STEP 4 : Enregistrement des options
//
// ---------------------------------------

const FormEditOptions = document.getElementById('FormEditOptions')
FormEditOptions.addEventListener('submit', function (event) {
    event.preventDefault()
    let urlEditOptions = FormEditOptions.action
    let dataEditOptions = new FormData(FormEditOptions)
    axios
        .post(urlEditOptions, dataEditOptions)
        .then(function(response)
        {
            // modification des classes pour la navigation entre panels
            nextStepOptions.className = " d-none"
            nextStepGallery.className = "btn btn-sm btn-outline-primary"
            if(navOptions !== null){
                navOptions.className = "nav-link disabled";
            }
            if(navGallery !== null){
                navGallery.className = "nav-link disabled";
            }
            Options.className = "tab-pane"
            Gallery.className += " active"
        })
        .catch(function(error){
            console.log(error);
        })
})
if(stepOptions2 !== null){
    stepOptions2.addEventListener('click', function(event){
        event.preventDefault()
        let urlEditOptions = FormEditOptions.action
        let dataEditOptions = new FormData(FormEditOptions)
        axios
            .post(urlEditOptions, dataEditOptions)
            .then(function(response)
            {
                // préparation du toaster
                var option = {
                    animation : true,
                    autohide: true,
                    delay : 3000,
                };
                // initialisation du toaster
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
                toastBody.textContent = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, option);
                toastElement.show();
            })
            .catch(function(error){
                console.log(error);
            })
    })
}
if(document.getElementById('previousOptions') !== null){
    document.getElementById('previousOptions').addEventListener('click', function(event){
        event.preventDefault()
        // modification des classes pour la navigation entre panels
        nextStepOptions.className = " d-none"
        nextStepEstimate.className = "btn btn-sm btn-outline-primary"
        if(navOptions !== null){
            navOptions.className = "nav-link disabled"
        }
        if(navOptions !== null){
            navEstimate.className = "nav-link active"
        }
        navEstimate.className = "nav-link active"
        Options.className = "tab-pane"
        Estimate.className += " active"
    })
}


// PARTIE Bannière sur image
// ---------------------------------------
// I. Mise en place du Select2
const TsPropertyBanner = new TomSelect("#complement_banner",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// II. Préparation de la modale
const modalPropertyBanner = document.getElementById('modalPropertyBanner')
const PropertyBanner = new bootstrap.Modal(modalPropertyBanner, { keyboard: true })
const addPropertyBanner = document.getElementById('addPropertyBanner')
addPropertyBanner.addEventListener('click', function(event){
    event.preventDefault()
    PropertyBanner.show()
})
// III. Envoi du formulaire ajout de la bannière
let property_banner = document.getElementById('FormPropertyBanner')
property_banner.addEventListener('submit', function (event) {
    event.preventDefault()
    let url_property_banner = '/gestapp/choice/property/banner/new'
    let data_property_banner = new FormData(property_banner)
    axios
        .post(url_property_banner, data_property_banner)
        .then(function(response)
        {
            const selectbanner = document.getElementById('complement_banner');
            const optionbanner = document.createElement("option")
            optionbanner.setAttribute("value", response.data.valuebanner)
            optionbanner.setAttribute("data-data", response.data.banner)
            optionbanner.text = response.data.banner;
            selectbanner.add(optionbanner)
            TsPropertyBanner.addOption({data: response.data.banner, value:response.data.valuebanner})
            TsPropertyBanner.addItem(response.data.cat)
            HouseCat.hide()
        })
        .catch(function(error){
            console.log(error);
        })
})
// IV. Modal listant les bannieres disponibles
const modalListPropertyBanner = document.getElementById('ListBanner')
const ListPropertyBanner = new bootstrap.Modal(modalListPropertyBanner, { keyboard: true })
const btnListBanner = document.getElementById('btnListBanner')
btnListBanner.addEventListener('click', function(event){
    event.preventDefault()
    ListPropertyBanner.show()
    axios
        .get('/gestapp/choice/property/banner/index')
        .then(function(response){
            document.getElementById('modalListBanner').innerHTML = response.data.list
        })
})

// PARTIE Catégorie du bien
// ---------------------------------------
// Select
const tsPropertyCat = new TomSelect("#complement_denomination",{
    plugins: ['remove_button', 'change_listener'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// Ouverture de la modale
const modalHouseCat = document.getElementById('modalHouseCat')
const HouseCat = new bootstrap.Modal(modalHouseCat, { keyboard: true })
const addHouseCat = document.getElementById('addHouseCat')
addHouseCat.addEventListener('click', function(event){
    event.preventDefault()
    HouseCat.show()
})
// Envoi du formulaire
let property_cat = document.getElementById('FormHouseCat')
property_cat.addEventListener('submit', function (event) {
    event.preventDefault()
    let url_property_cat = '/gestapp/choice/denomination/new2'
    let data_property_cat = new FormData(property_cat)
    axios
        .post(url_property_cat, data_property_cat)
        .then(function(response)
        {
            const selectcat = document.getElementById('complement_denomination');
            const optioncat = document.createElement("option")
            optioncat.setAttribute("value", response.data.valuecat)
            optioncat.setAttribute("data-data", response.data.cat)
            optioncat.text = response.data.cat;
            selectcat.add(optioncat)
            tsPropertyCat.addOption({data: response.data.cat, value:response.data.valuecat})
            tsPropertyCat.addItem(response.data.cat)
            HouseCat.hide()
        })
        .catch(function(error){
            console.log(error);
        })
})

// PARTIE Etat du bien
// ---------------------------------------
// SELECT
const tspropertyState = new TomSelect("#complement_propertyState",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// Ouverture de la modale
const modalHouseState = document.getElementById('modalHouseState')
const HouseState = new bootstrap.Modal(modalHouseState, { keyboard: true })
const addHouseState = document.getElementById('addHouseState')
addHouseState.addEventListener('click', function(event){
    event.preventDefault()
    HouseState.show()
})
// Envoi du formulaire
let property_state = document.getElementById('FormHouseState')
property_state.addEventListener('submit', function (event) {
    event.preventDefault()
    let url_property_state = '/gestapp/choice/property/state/new2'
    let data_property_state = new FormData(property_state)
    axios
        .post(url_property_state, data_property_state)
        .then(function(response)
        {
            const selectstate = document.getElementById('complement_propertyState');
            const optionstate = document.createElement("option")
            optionstate.setAttribute("data-data", response.data.state)
            optionstate.setAttribute("value", response.data.valuestate)
            optionstate.text = response.data.state;
            selectstate.add(optionstate)
            tspropertyState.addOption({data: response.data.state, value:response.data.valuestate})
            tspropertyState.addItem(response.data.state)
            HouseState.hide()
        })
        .catch(function(error){
            console.log(error);
        })
})

// PARTIE Energie du bien
// ---------------------------------------
// Select
const tspropertyEnergy = new TomSelect("#complement_propertyEnergy",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// PARTIE Energy du bien - Ouverture de la modal
const modalHouseEnergy = document.getElementById('modalHouseEnergy')
const HouseEnergy = new bootstrap.Modal(modalHouseEnergy, { keyboard: true })
const addHouseEnergy = document.getElementById('addHouseEnergy')
addHouseEnergy.addEventListener('click', function(event){
    event.preventDefault()
    HouseEnergy.show()
})
// PARTIE Energy du bien - Envoi du formulaire
let FormHouseEnergy = document.getElementById('FormHouseEnergy')
FormHouseEnergy.addEventListener('submit', function (event) {
    event.preventDefault()
    let url_property_energy = '/gestapp/choice/property/energy/new2'
    let data_property_energy = new FormData(FormHouseEnergy)
    axios
        .post(url_property_energy, data_property_energy)
        .then(function(response)
        {
            const selectenj = document.getElementById('complement_propertyEnergy');
            const optionsenj = document.createElement("option");
            optionsenj.setAttribute("data-data", response.data.energy)
            optionsenj.setAttribute("value", response.data.valueenergy)
            optionsenj.text = response.data.energy;
            selectenj.add(optionsenj)
            tspropertyEnergy.addOption({data: response.data.energy, value:response.data.valueenergy})
            tspropertyEnergy.addItem(response.data.energy)
            HouseEnergy.hide()
        })
        .catch(function(error){
            console.log(error);
        })
})

// PARTIE Orientation du bien
// ---------------------------------------
// Select
const tspropertyOrient = new TomSelect("#complement_propertyOrientation",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// Ouverture de la modal
const modalHouseOrientation = document.getElementById('modalHouseOrientation')
const HouseOrientation = new bootstrap.Modal(modalHouseOrientation, { keyboard: true })
const addHouseOrientation = document.getElementById('addHouseOrientation')
addHouseOrientation.addEventListener('click', function(event){
    event.preventDefault()
    HouseOrientation.show()
})
// PARTIE Orientation du bien - Envoi du formulaire
let FormHouseOrientation = document.getElementById('FormHouseOrientation')
FormHouseOrientation.addEventListener('submit', function (event) {
    event.preventDefault()
    let url_property_orientation = '/gestapp/choice/property/orientation/new2'
    let data_property_orientation = new FormData(FormHouseOrientation)
    axios
        .post(url_property_orientation, data_property_orientation)
        .then(function(response)
        {
            const selectorient = document.getElementById('complement_propertyOrientation');
            const optionsorient = document.createElement("option");
            optionsorient.text = response.data.orientation;
            optionsorient.setAttribute("data-data", response.data.orient)
            optionsorient.setAttribute("value", response.data.valueorient)
            optionsorient.text = response.data.orient;
            selectorient.add(optionsorient)
            tspropertyOrient.addOption({data: response.data.orient, value:response.data.valueorient})
            tspropertyOrient.addItem(response.data.orient)
            HouseOrientation.hide()
        })
        .catch(function(error){
            console.log(error);
        })
})

// PARTIE disponibilité du bien
// ---------------------------------------
// Select
new TomSelect("#complement_disponibility",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});

// PARTIE location du bien
// ---------------------------------------
// Select
new TomSelect("#complement_location",{
    placeholder: "A définir",
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});

// PARTIE Typologie du bien
// ---------------------------------------
// Apparition de l'input de charge en copro sur changement du choiceType
const coproprietyTaxe = document.getElementById('coproprietyTaxe')
const complement_coproperty_0 = document.getElementById('complement_coproperty_0')
const complement_coproperty_1 = document.getElementById('complement_coproperty_1')
if(complement_coproperty_1.checked){
    coproprietyTaxe.classList.remove("d-none")
}
complement_coproperty_1.addEventListener('click', function (event) {
    coproprietyTaxe.classList.remove("d-none")
})
complement_coproperty_0.addEventListener('click', function (event) {
    coproprietyTaxe.classList.add("d-none")
})


// PARTIE Equipement du bien
// ---------------------------------------
// Select
const tspropertyEquip = new TomSelect("#complement_propertyEquipment",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// Ouverture de la modal
const modalHouseEquipment = document.getElementById('modalHouseEquipment')
const HouseEquipment= new bootstrap.Modal(modalHouseEquipment, { keyboard: true })
const addHouseEquipment = document.getElementById('addHouseEquipment')
addHouseEquipment.addEventListener('click', function(event){
    event.preventDefault()
    HouseEquipment.show()
})
// Envoi du formulaire
let FormHouseEquipment = document.getElementById('FormHouseEquipment')
FormHouseEquipment.addEventListener('submit', function (event){
    event.preventDefault()
    let urlpropertyEquip = FormHouseEquipment.action
    let datapropertyEquip = new FormData(FormHouseEquipment)
    axios
        .post(urlpropertyEquip, datapropertyEquip)
        .then(function(response)
        {
            const selectorequip = document.getElementById('complement_propertyOrientation');
            const optionsequip = document.createElement("option");
            optionsequip.text = response.data.orientation;
            optionsequip.setAttribute("data-data", response.data.equip)
            optionsequip.setAttribute("value", response.data.valueequip)
            optionsequip.text = response.data.equip;
            selectorequip.add(optionsequip)
            tspropertyEquip.addOption({data: response.data.equip, value:response.data.valueequip})
            tspropertyEquip.addItem(response.data.equip)
            HouseEquipment.hide()
        })
        .catch(function(error){
            console.log(error);
        })
})

// PARTIE Typologie du bien
// ---------------------------------------
// Select
const tspropertyTypo = new TomSelect("#complement_propertyTypology",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// Ouverture de la modal
const modalHouseTypology = document.getElementById('modalHouseTypology')
const HouseTypology = new bootstrap.Modal(modalHouseTypology, { keyboard: true })
const addHouseTypology = document.getElementById('addHouseTypology')
addHouseTypology.addEventListener('click', function(event){
    event.preventDefault()
    HouseTypology.show()
})
// Envoi du formulaire
let FormHouseTypology = document.getElementById('FormHouseTypology')
FormHouseTypology.addEventListener('submit', function (event) {
    event.preventDefault()
    let urlpropertyTypology = FormHouseTypology.action
    let datapropertyTypology = new FormData(FormHouseTypology)
    axios
        .post(urlpropertyTypology,datapropertyTypology)
        .then(function(response){
            const selectorTypology = document.getElementById('complement_propertyTypology');
            const optionsTypology = document.createElement("option");
            optionsTypology.text = response.data.orientation;
            optionsTypology.setAttribute("data-data", response.data.typo)
            optionsTypology.setAttribute("value", response.data.valuetypo)
            optionsTypology.text = response.data.typo;
            selectorTypology.add(optionsTypology)
            tspropertyTypo.addOption({data: response.data.typo, value:response.data.valuetypo})
            tspropertyTypo.addItem(response.data.typo)
            HouseTypology.hide()
        })
})


// PARTIE Autres options du bien
// ---------------------------------------
// Select
const tspropertyOther = new TomSelect("#complement_propertyOtheroption",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// Ouverture de la modal
const modalHouseOther = document.getElementById('modalHouseOther')
const HouseOther = new bootstrap.Modal(modalHouseOther, { keyboard: true })
const addHouseOtheroption = document.getElementById('addHouseOtheroption')
addHouseOtheroption.addEventListener('click', function(event){
    event.preventDefault()
    HouseOther.show()
})
// Envoi du formulaire
let FormOtherOption = document.getElementById('FormOtherOption')
FormOtherOption.addEventListener('submit', function (event) {
    event.preventDefault()
    let urlpropertyOther = FormOtherOption.action
    let datapropertyOther = new FormData(FormOtherOption)
    axios
        .post(urlpropertyOther,datapropertyOther)
        .then(function(response){
            const selectorOther = document.getElementById('complement_propertyOtheroption');
            const optionsOther = document.createElement("option");
            optionsOther.text = response.data.orientation;
            optionsOther.setAttribute("data-data", response.data.other)
            optionsOther.setAttribute("value", response.data.valueother)
            optionsOther.text = response.data.other;
            selectorOther.add(optionsOther)
            tspropertyOther.addOption({data: response.data.other, value:response.data.valueother})
            tspropertyOther.addItem(response.data.other)
            HouseOther.hide()
        })
})


// ---------------------------------------
// STEP 5 : Enregistrement des images du bien
//
// ---------------------------------------

if(nextStepGallery !== null){
    nextStepGallery.onclick = function (event) {
        event.preventDefault()
        nextStepGallery.className += " d-none";
        nextStepPublication.className = "btn btn-sm btn-outline-primary";
        if(navGallery !== null){
            navGallery.className = "nav-link disabled";
        }
        if(navPublication !== null){
            navPublication.className = "nav-link disabled";
        }
        Gallery.className = "tab-pane";
        Publication.className += " active";
    }
}
// Ajout d'une image sur un bien
let form = document.getElementById('addPhoto')
form.addEventListener('submit', function (event) {
    event.preventDefault()
    let url = form.action
    let data = new FormData(form)
    axios
        .post(url, data)
        .then(function(response)
        {
            // rafraichissement du tableau
            document.getElementById('listephoto').innerHTML = response.data.listephoto;
            // rechargement des events
            document.querySelectorAll('a.delphoto').forEach(function (link){
                link.addEventListener('click', delphoto);
            })
        })
        .catch(function(error){
            console.log(error);
        })
})
// Suppression d'une image
function delphoto(event){
    event.preventDefault()
    let urldelphoto = this.href
    axios
        .post(urldelphoto)
        .then(function(response){
            document.getElementById('listephoto').innerHTML = response.data.listephoto;
            document.querySelectorAll('a.delphoto').forEach(function (link){
                link.addEventListener('click', delphoto);
            })
        })
}
document.querySelectorAll('a.delphoto').forEach(function (link){
    link.addEventListener('click', delphoto);
})
document.getElementById('previousGallery').addEventListener('click', function(event){
    event.preventDefault()
    // modification des classes pour la navigation entre panels
    nextStepGallery.className = " d-none"
    nextStepOptions.className = "btn btn-sm btn-outline-primary"
    if(navGallery !== null){
        navGallery.className = "nav-link disabled";
    }
    if(navOptions !== null){
        navOptions.className = "nav-link disabled";
    }
    Gallery.className = "tab-pane"
    Options.className += " active"
})

// ------------------------------------
// Génération du Sortable - Déplacement des photos et ordres d'affichages
//-------------------------------------
const sortablePhoto = document.getElementById("listephoto")
new Sortable(sortablePhoto, {
    animation:150,
    // Called by any change to the list (add / update / remove)
    onSort: function (event) {
        let cols = sortablePhoto.children
        // on boucle sur le résultat des enfants pour envoyer au controller la modification du positionnement des photos
        for(i = 0; i < cols.length; i++){
            let idcol = cols[i].id
            let key = i
            let url = "/gestapp/photo/updatepositionphoto/"+ idcol + "/" + key
            axios
                .post(url)
                .then(function(response){
                    // préparation du toaster bootstrap
                    var option = {
                        animation: true,
                        autohide: true,
                        delay: 3000,
                    };
                    // initialisation du toaster bootstrap
                    var toastHTMLElement = document.getElementById("toaster");
                    var message = response.data.message;
                    var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
                    toastBody.textContent = message;
                    var toastElement = new bootstrap.Toast(toastHTMLElement, option);
                    toastElement.show();
                })
                .catch(function(error){
                    console.log(error);
                })
        }
        //récupération du premier enfant
        let firstChild = cols[0]
        let card = firstChild.childNodes[1]
        card.className = "card text-white bg-primary mb-1"
        let cardBody = card.childNodes[5]
        cardBody.childNodes[1].textContent = 'Image de profil'
        // Récupération des autres enfants
        for(i=1; i < cols.length; i++){
            let otherChild = cols[i]
            let card = otherChild.childNodes[1]
            card.className = "card text-dark bg-light mb-1"
            let cardBody = card.childNodes[5]
            cardBody.childNodes[1].textContent = 'Image de galerie'
        }
    },
})


// ---------------------------------------
// STEP 6 :
// Enregistrement des options de publication du bien
//
// ---------------------------------------

let FormPublication = document.getElementById('FormPublication')
FormPublication.addEventListener('submit', function (event) {
    let url_publication = FormPublication.action
    let data_publication = new FormData(FormPublication)
    axios
        .post(url_publication, data_publication)
        .catch(function(error){
            console.log(error);
        })
})
if(document.getElementById('previousPublication') !== null){
    document.getElementById('previousPublication').addEventListener('click', function(event){
        event.preventDefault()
        // modification des classes pour la navigation entre panels
        nextStepPublication.className = " d-none"
        nextStepGallery.className = "btn btn-sm btn-outline-primary"
        if(navPublication !== null){
            navPublication.className = "nav-link disabled";
        }
        if(navGallery !== null){
            navGallery.className = "nav-link active";
        }
        Publication.className = "tab-pane"
        Gallery.className += " active"
    })
}
// Enregistrement des informations si propriété déja validée une fois
if(nextStepPublication2 !== null){
    nextStepPublication2.addEventListener('click', function (event){
        event.preventDefault()
        let urladdPublication = FormPublication.action
        let dataaddPublication = new FormData(FormPublication)
        axios
            .post(urladdPublication, dataaddPublication)
            .then(function (response) {
                // préparation du toaster
                var option = {
                    animation: true,
                    autohide: true,
                    delay: 3000,
                };
                // initialisation du toaster
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body') // selection de l'élément possédant le message
                toastBody.textContent = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, option);
                toastElement.show();
            })
            .catch(function (error) {
                console.log(error);
            })
    })
}
}