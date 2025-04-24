// Déclaration des variables globales
let btnUpdateForm = document.getElementById('btnUpdateProperty');
const modal = document.getElementById('modal');
const modalBs = new bootstrap.Modal(document.getElementById('modal'));

// comportement à adopter à la fermeture de la modal des customers
modal.addEventListener('hidden.bs.modal', event => {
    let form = modal.querySelector('#formCustomer_add');
    let btnSubmit = modal.querySelector('.modal-footer #btnSubmitCustomer');
    if(form && !btnSubmit){
        //let idCustomer = document.getElementById('idCustomer').value;
        //axios.post('/gestapp/customer/'+ idCustomer +'/delontransaction');
    }
    modal.querySelector('.modal-dialog').classList.remove('modal-lg');
    modal.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">\n" +
        "<div class=\"spinner-border text-primary\" role=\"status\">\n" +
        "<span class=\"visually-hidden\">Loading...</span>\n" +
        "</div>\n" +
        "</div>";
});

function openModalXL(event){
    event.preventDefault();
    let a = event.currentTarget;
    let recipient = a.getAttribute('data-bs-data');
    let url = a.href;
    let [crud, contentTitle, id] = recipient.split('-');
    modalBs.show();
    modal.querySelector('.modal-title').textContent = contentTitle;
    if(crud === 'ADDCUSTOMER' || crud === 'EDITCUSTOMER'){
        modal.querySelector('.modal-dialog').classList.add('modal-xl');
        // Charger le contenu dans la modal
        axios
            .get(url)
            .then(response => {
                modal.querySelector('.modal-body').innerHTML = response.data.form;
                let btnAddResp = modal.querySelector('.modal-body #btnAddResp');
                if(btnAddResp){
                    btnAddResp.addEventListener('click', addResponsable);
                }
                let btnSupprResps = document.querySelectorAll('.btnSupprResp');
                if(btnSupprResps){
                    btnSupprResps.forEach(function(click){
                        click.addEventListener('click', delResponsable);
                    });
                }

                const typeClient = document.getElementById('customer_typeClient');
                if(typeClient.value === "professionnel"){
                    document.getElementById("box_professionnel").classList.remove('d-none');
                    document.getElementById("box_particulier").classList.add('d-none');
                }

                typeClient.addEventListener('change', function(event){
                    if(typeClient.value === "professionnel"){
                        document.getElementById("box_professionnel").classList.remove('d-none');
                        document.getElementById("box_professionnel").classList.add('animate__animated', 'animate__fadeIn');
                    }else{
                        document.getElementById("box_professionnel").classList.add('d-none');
                        document.getElementById("box_professionnel").classList.remove('animate__animated', 'animate__fadeIn');
                    }
                    if(typeClient.value === "particulier"){
                        document.getElementById("box_particulier").classList.remove('d-none');
                        document.getElementById("box_particulier").classList.add('animate__animated', 'animate__fadeIn');
                    }else{
                        document.getElementById("box_particulier").classList.add('d-none');
                        document.getElementById("box_particulier").classList.remove('animate__animated', 'animate__fadeIn');
                    }
                });
                // block pour interagir sur la civilité
                if(document.querySelector('input[name=customer\\[civility\\]]:checked').value > 1){
                    console.log("ok");
                    document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
                }
                const radioCustomerButtons = document.querySelectorAll('input[name=customer\\[civility\\]]');
                radioCustomerButtons.forEach(function(radio) {
                    radio.addEventListener("change", function() {
                        if (parseInt(this.value) === 2) {
                            document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
                        } else if (parseInt(this.value) === 1){
                            document.getElementById('customer_maidenName').parentElement.classList.add('d-none');
                        }
                    });
                });
                if (document.querySelector('input[name=customer_resp\\[civility\\]]:checked').value > 1){
                    document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
                }
                const radioRespButtons = document.querySelectorAll('input[name=customer_resp\\[civility\\]]');
                radioRespButtons.forEach(function(radio) {
                    radio.addEventListener("change", function() {
                        if (parseInt(this.value) === 2) {
                            document.getElementById('customer_resp_maidenName').parentElement.classList.remove('d-none');
                        } else if (parseInt(this.value) === 1){
                            document.getElementById('customer_resp_maidenName').parentElement.classList.add('d-none');
                        }
                    });
                });
                // Variables liés aux modifications des champs du bloc adresse.
                let customer_commune = document.getElementById('customer_city');
                let customer_zipcode = document.getElementById('customer_zipcode');
                let customer_selectcity = document.getElementById('customer_selectcity');
                let customer_addresseInput = document.getElementById('customer_adress');
                let customer_proCity = document.getElementById('customer_proCity');
                let customer_proZipcode = document.getElementById('customer_proZipcode');
                let customer_proSelectcity = document.getElementById('customer_proSelectcity');
                if(customer_commune && customer_addresseInput){
                    customer_zipcode.addEventListener('input', function(event){
                        zipcode_api(customer_zipcode, customer_commune, customer_selectcity);
                    });
                    customer_selectcity.addEventListener('change', function (event){
                        change_selectcity(customer_zipcode, customer_commune, customer_selectcity);
                    });
                    customer_proZipcode.addEventListener('input', function(event){
                        zipcode_api(customer_proZipcode, customer_proCity, customer_proSelectcity);
                    });
                    customer_proSelectcity.addEventListener('change', function (event){
                        change_selectcity(customer_proZipcode, customer_proCity, customer_proSelectcity);
                    });
                }
                initializeTinyMCE(); // Réinitialiser TinyMCE si nécessaire
            })
            .catch(function(error){
                console.log('Erreur lors du chargement de la modal', error);
            });
    }else if(crud === "DELCUSTOMER"){
        let cardBody = a.closest('.card-body');
        if (cardBody) {
            // Sélectionner le <h4> dans cet élément parent
            const h4Element = cardBody.querySelector('h4.card-title');
            if (h4Element) {
                // Récupérer le contenu du <h4>
                const h4Content = h4Element.textContent;
                modal.querySelector('.modal-body').innerHTML = "<p class='mb-0'>Attention, vous êtes sur le point de supprimer la fiche du client : <br><b>" + h4Content + "</b><br>Etes-vous sur de votre choix ?</p>";
                modal.querySelector('.modal-footer a').textContent = 'Supprimer';
                modal.querySelector('.modal-footer a').href = url;
            }
        }

    }else if(crud === 'ADDMANDAT'){
        axios
            .get(url)
            .then(function(response){
                modal.querySelector('.modal-body').innerHTML = response.data.form;
                // Mettre en place la logique de validation de numéro de Mandat
                let addMandat = document.getElementById('add_mandat_refMandat');
                let ArrayMandats = [];
                axios
                    .get('/gestapp/property/getlistmandats')
                    .then(function(response){
                        // alimenter le tableau des ref existantes
                        let ListMandats = response.data.listmandats;
                        ListMandats.forEach((element) => {
                            ArrayMandats.push(parseInt(element));
                        });
                    })
                ;
                addMandat.addEventListener('input', function(event){
                    let newmandat = parseInt(addMandat.value);
                    let flag = 0;
                    for(let i=0; i<ArrayMandats.length; i++) {
                        if(newmandat === ArrayMandats[i]) {
                            flag = 1;
                        }
                    }
                    if(flag === 1){
                        addMandat.classList.remove("is-valid");
                        addMandat.classList.add("is-invalid");
                        document.getElementById('refmandat_error').classList.add('alert alert-warning');
                        document.getElementById('refmandat_error').innerHTML = 'Corrigez ce numéro, il est présent dans la liste des biens <b>Paps immo</b>.';

                    }else{
                        addMandat.classList.remove("is-invalid");
                        addMandat.classList.add("is-valid");
                        document.getElementById('refmandat_error').classList.add('alert alert-success');
                        document.getElementById('refmandat_error').textContent = "Numéro de mandat valide.";
                    }
                });
                initializeTinyMCE();
            })
            .catch(function(error){
                console.log('Erreur lors du chargement de la modal', error);
            });
    }else if(crud === 'ADDAVENANT'){
        axios
            .get(url)
            .then(function(response){
                modal.querySelector('.modal-body').innerHTML = response.data.form;
                calculatePrices(document.getElementById('avenant_price'),document.getElementById('avenant_honoraires'), document.getElementById('avenant_priceFai'));
                initializeTinyMCE();
            })
            .catch(function(error){
                console.log('Erreur lors du chargement de la modal', error);
            });
    }else{
        axios
            .get(url)
            .then(function(response){
                modal.querySelector('.modal-body').innerHTML = response.data.form;
            })
            .catch(function(error){
                alert(error.detail);
            })
        ;
    }

}

// Fonction pour activer le lien de navigation et charger le formulaire au chargement du DOM
function loadFormContent(navLink) {
    // Récupère l'URL de l'élément actif
    let activeUrl = navLink.getAttribute('href');
    let activeDataTarget = navLink.getAttribute('data-bs-target');
    let nodeForm = document.querySelector(activeDataTarget + ' #content-form');
    let nodeFormName = nodeForm.closest('.tab-pane').id;
    // Réinitialise le contenu actuel du nodeForm
    if (nodeForm) {
        nodeForm.innerHTML =
            '<div class="text-center p-5">' +
            '<div class="spinner-border" role="status">' +
            '<span class="visually-hidden">Loading...</span>' +
            '</div>' +
            '</div>'
        ;
    }
    // Charge le nouveau contenu
    if (activeUrl && nodeForm) {
        axios
            .get(activeUrl)
            .then(function(response) {
                nodeForm.innerHTML = response.data.form;
                if(nodeFormName === 'Informations'){
                    const radioCustomerButtons = document.querySelectorAll('input[name=informations\\[typeMandat\\]]');
                    radioCustomerButtons.forEach(function(radio) {
                        radio.addEventListener("change", function() {
                            if (parseInt(this.value) === 'avec_exclusivité_vente_interactive') {
                                document.getElementById('block_ExcluVI').classList.remove('d-none');
                            } else {
                                document.getElementById('block_ExcluVI').classList.add('d-none');
                            }
                        });
                    });
                    // variables liées au blac adress du bien
                    let informations_commune = document.getElementById('informations_city');
                    let informations_zipcode = document.getElementById('informations_zipcode');
                    let informations_selectcity = document.getElementById('selectcity');

                    // Liste de choix sur la destination du bien
                    let family = document.getElementById('informations_family');
                    let rubric = document.getElementById('informations_rubric');
                    let rubricss = document.getElementById('informations_rubricss');
                    let FamValue = family.value;
                    let RubValue = rubric.value;
                    let RubcssValue = rubricss.value;
                    let RubUrl = '/gestapp/choice/property/family/rubric/';
                    let RubcssUrl = '/gestapp/choice/property/family/rubricss/';

                    calculateChars(document.getElementById('informations_name'),100,document.getElementById('charCount'));

                    informations_zipcode.addEventListener('input', function(event){
                        zipcode_api(informations_zipcode, informations_commune, informations_selectcity);
                    });
                    informations_selectcity.addEventListener('change', function (event){
                        change_selectcity(informations_zipcode, informations_commune, informations_selectcity);
                    });
                    SelectChoice(rubric,FamValue,RubUrl,RubValue);
                    SelectChoice(rubricss,RubValue,RubcssUrl,RubcssValue);
                    family.addEventListener('change', function(event){
                        selectChoiceOnChange(family,rubric,RubUrl,RubValue);
                    });
                    rubric.addEventListener('change', function(event){
                        selectChoiceOnChange(rubric,rubricss,RubcssUrl,RubcssValue);
                    });

                }
                if(nodeFormName === 'Customers'){
                    let searchCustomersRechercher = document.getElementById('search_customers_rechercher');
                    let inputSearchCustomer = document.getElementById('search_customer_property_firstName');
                    searchCustomersRechercher.addEventListener('click', submitSearchCustomer);
                    inputSearchCustomer.addEventListener('input', submitSearchCustomer);
                }
                if(nodeFormName === 'Estimate'){
                    const sales = document.getElementById('sale');
                    const rent = document.getElementById('rent');
                    const rentCommerce = document.getElementById('rentCommerce');

                    if(response.data.data[0] === 4 && response.data.data[1] === 8) {
                        let warranty = document.getElementById('warrantyDeposit');
                        sales.classList.add('d-none');
                        rent.classList.add('d-none');
                        if(warranty !== null){
                            document.getElementById('warrantyDeposit').remove();
                        }
                    }else if (response.data.data[0] === 5) {
                        let warranty = document.getElementById('warrantyDeposit');
                        sales.classList.add('d-none');
                        rentCommerce.classList.add('d-none');
                        if(warranty !== null){
                            document.getElementById('warrantyDeposit').remove();
                        }
                    }else{
                        rent.classList.add('d-none');
                        rentCommerce.classList.add('d-none');
                    }

                    const tsdiagChoice = new TomSelect("#property_step2_diagChoice",TsSimple);
                    const diagChoice = document.getElementById('property_step2_diagChoice');
                    // Affichage des inputs DPE et GPE dès le chargement de la page
                    if(diagChoice.value === 'non_obligatoire' || diagChoice.value === 'vierge' ){
                        document.getElementById('block_dpeAt').className += " d-none";
                        document.getElementById('block_diagDpeResult').className += " d-none";
                        document.getElementById('block_diagGesResult').className += " d-none";
                        document.getElementById('block_estimation').className += " d-none";
                    }
                    // Affichage des inputs DPE et GPE sur le changement de valeur
                    const changeTsDiag = function(){
                        let value = tsdiagChoice.getValue();
                        if(value === 'obligatoire'){
                            document.getElementById('block_dpeAt').className = "row mb-1 mt-1 g-1";
                            document.getElementById('block_diagDpeResult').className = "row mb-1 mt-1 g-1";
                            document.getElementById('block_diagGesResult').className = "row mb-1 mt-1 g-1";
                            document.getElementById('block_estimation').className = "row mb-1 mt-1 g-1";

                        }else if(value === 'non_obligatoire' || value === 'vierge' ){
                            document.getElementById('block_dpeAt').className += " d-none";
                            document.getElementById('block_diagDpeResult').className += " d-none";
                            document.getElementById('block_diagGesResult').className += " d-none";
                            document.getElementById('block_estimation').className = " d-none";
                        }
                    };
                    tsdiagChoice.on('change', changeTsDiag );
                    calculatePrices(document.getElementById('property_step2_price'),document.getElementById('property_step2_honoraires'), document.getElementById('property_step2_priceFai'));

                }
                if(nodeFormName === 'Options'){
                    initializeTomSelect('.oneChoice', TsSimple);
                    initializeTomSelect('.multiChoice', TsMulti);
                }
                if(nodeFormName === 'Gallery'){

                    const btnPhotos = document.getElementById('btnPhotos');
                    btnPhotos.addEventListener('click', submitPhotos);

                    const btnGenQrCode = document.getElementById('btnGenQrCode');
                    if(btnGenQrCode){
                        btnGenQrCode.addEventListener('click', genQrcode);
                    }

                    const btndellPhotos = document.querySelectorAll('#listephoto .delphoto');
                    btndellPhotos.forEach(function(link){
                        link.addEventListener('click', delPhoto);
                    });
                    const sortablePhoto = document.getElementById("listephoto");
                    new Sortable(sortablePhoto, {
                        animation:150,
                        // Called by any change to the list (add / update / remove)
                        onSort: function(event){
                            onSortPhoto(sortablePhoto);
                        }
                    });

                    let btnAddVideo = document.getElementById('btnAddVideo');
                    if(btnAddVideo){
                        btnAddVideo.addEventListener('click', submitVideos);
                    }
                    const btndelVideo = document.querySelector('.delVideo');
                    if(btndelVideo){
                        btndelVideo.addEventListener('click', delVideo);
                    }
                }
                if(nodeFormName === 'Publication'){
                    const switchAllPublication = document.getElementById('AllPublications');
                    switchAllPublication.addEventListener('change', AllCheckedPublication);
                }
                let linkOpenModal = document.querySelectorAll('a.openModal');
                linkOpenModal.forEach(function(link){
                    link.addEventListener('click', openModalXL);
                });
                initializeTinyMCE();
            })
            .catch(function(error) {
                console.log('error', error);
            })
        ;
    }
}

// Fonction pour gérer le clic sur un lien de navigation
function handleNavLinkClick(event) {
    event.preventDefault();
    const clickedNavLink = event.target.closest('.nav-link');
    if (clickedNavLink) {
        clickedNavLink.classList.add('active');
        loadFormContent(clickedNavLink);}
}

// Fonction pour initialiser les écouteurs d'événements
function initializeNavLinks() {
    // Ajoute un écouteur d'événement sur chaque lien de navigation
    const navLinks = document.querySelectorAll('#admin-tab .nav-link');
    navLinks.forEach(function (navLink) {
        navLink.addEventListener('click', handleNavLinkClick);
    });

    // Charge le formulaire pour le lien actif par défaut au chargement du DOM
    const defaultActiveNavLink = document.querySelector('#admin-tab .nav-link.active');
    if (defaultActiveNavLink) {
        loadFormContent(defaultActiveNavLink);
    }

    const btnUpdateProperty = document.getElementById('btnUpdateProperty');
    if (btnUpdateProperty) {
        btnUpdateProperty.addEventListener('click', submitNodeForm);
    }

    const btnNewProperty = document.getElementById('btnNewProperty');
    if (btnNewProperty) {
        btnNewProperty.addEventListener('click', newPro_submitNodeForm);
    }

    const btndellPhotos = document.querySelectorAll('a.delphoto');
    btndellPhotos.forEach(function (link) {
        link.addEventListener('click', delPhoto);
    });

    const btnModalSubmit = document.getElementById('btnModalSubmit');
    btnModalSubmit.addEventListener('click', submitModalForm);
}

function newPro_submitNodeForm(){
    submitNodeForm();
    // Sélectionne tous les éléments <li> dans la barre de navigation
    let navItems = document.querySelectorAll('.nav-tabs li');

    // Trouve l'élément <li> actuellement actif (qui n'a pas la classe 'disabled')
    let currentActiveItem = null;
    for (let item of navItems) {
        if (!item.classList.contains('notActive')) {
            currentActiveItem = item;
            break;
        }
    }

    // Si un élément actif est trouvé, on passe au suivant
    if (currentActiveItem) {
        const nextItem = currentActiveItem.nextElementSibling;

        if (nextItem) {
            console.log(nextItem);
            nextItem.classList.remove('notActive');
            currentActiveItem.classList.add('notActive');
            nextItem.querySelector('a').classList.remove('disabled');
            nextItem.querySelector('a').classList.add('active');
            currentActiveItem.querySelector('a').classList.remove('active');
            currentActiveItem.querySelector('a').classList.add('disabled');

            const currentPaneId = currentActiveItem.querySelector('a').getAttribute('data-bs-target');
            const nextPaneId = nextItem.querySelector('a').getAttribute('data-bs-target');

            document.querySelector(currentPaneId).classList.remove('active', 'show');
            document.querySelector(currentPaneId).querySelector('#content-form').innerHTML = "<div class=\"text-center p-5\"><div class=\"spinner-border\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div></div>";
            document.querySelector(nextPaneId).classList.add('active', 'show');
            loadFormContent(nextItem.querySelector('a'));
        }else{
            console.log('il n\'existe pas');
        }
    }
}

// Fonction pour initialiser TinyMCE
function initializeTinyMCE() {
    const maxChars = 1500;
    tinymce.remove(); // Supprime les instances existantes
    tinymce.init({
        selector: 'textarea.tinymce',
        setup: function(editor) {
            editor.on('input', function() {
                const content = editor.getContent({ format: 'text' });
                if (content.length > maxChars) {
                    const truncatedContent = content.substring(0, maxChars);
                    editor.setContent(truncatedContent);
                    alert(`La limite de ${maxChars} caractères a été atteinte.`);
                }
            });

            editor.on('keydown', function(event) {
                const content = editor.getContent({ format: 'text' });
                if (content.length >= maxChars && event.key !== "Backspace" && event.key !== "Delete") {
                    event.preventDefault();
                    alert(`La limite de ${maxChars} caractères a été atteinte.`);
                }
            });
        },
        plugins: 'image table lists visualchars wordcount',
        toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image',
        images_file_types: 'jpg,svg,webp',
        language: 'fr_FR',
        language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
        entity_encoding: "raw",
        encoding: "html",
        paste_as_text: true,
        valid_elements: 'p,br,b,i,u,strong,em,ul,ol,li', // Exemple : limiter les balises autorisées
        valid_children: '+body[p,br,b,i,u,strong,em,ul,ol,li]', // Exemple : limiter les enfants autorisés
    });

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
}

function submitSearchCustomer(event){
    event.preventDefault();
    let form = document.getElementById('formProperty_searchCustomer');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('listeSearchCustomers').innerHTML = response.data.liste;
            toasterMessage(response.data.message);
            let linkAddCustomer = document.querySelectorAll('a.addcustomersearch');
            linkAddCustomer.forEach(function(link){
                link.addEventListener('click', submitAddSearchCustomer);
            });
        })
        .catch(function(error){
            alert(error);
        })
    ;
}

function SelectChoice(listSelect, parentValue, url,ChoiceValue){
    if(listSelect.value >= 1){
        axios
            .get(url + parentValue)
            .then(
                function(response){
                    let SelectChoicevalues = response.data.values;
                    removeOptions(listSelect);
                    SelectChoicevalues.forEach((element)=>{
                        if (element.id === parseInt(ChoiceValue)){
                            let newOption = new Option (element.name, element.id, false, true);
                            listSelect.options.add(newOption);
                        }else{
                            let newOption = new Option (element.name, element.id);
                            listSelect.options.add(newOption);
                        }
                    });
                }
            )
            .catch(function(error){
                console.log(error);
            })
        ;
    }
}

function selectChoiceOnChange(parentSelect, listSelect, url, ChoiceValue){
    let parentValue = parseInt(parentSelect.value);
    axios
        .get(url + parentValue)
        .then(
            function(response){
                let SelectChoicevalues = response.data.values;
                console.log(SelectChoicevalues);
                removeOptions(listSelect);
                SelectChoicevalues.forEach((element)=>{
                    if (element.id === parseInt(ChoiceValue)){
                        let newOption = new Option (element.name, element.id, false, true);
                        listSelect.options.add(newOption);
                    }else{
                        let newOption = new Option (element.name, element.id);
                        listSelect.options.add(newOption);
                    }
                });
            }
        )
        .catch(function(error){
            console.log(error);
        })
    ;

}

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

function change_selectcity(zipcode, commune, select){
    let regex = /^(.+) \((\d+)\)$/;
    let select_value = select.options[select.selectedIndex].text;
    const match = select_value.match(regex);
    zipcode.value = match[2];
    commune.value = match[1].toUpperCase();
}

function calculatePrices(price, honoraires, priceFAI){
    price.addEventListener('change', function () {
        let priceValue = parseInt(price.value);
        let honorairesValue = parseInt(honoraires.value);
        priceFAI.value = priceValue + honorairesValue;
    });
    honoraires.addEventListener('change', function () {
        let priceValue = parseInt(price.value);
        let honorairesValue = parseInt(honoraires.value);
        priceFAI.value = priceValue + honorairesValue;
    });
}

function calculateChars(element, length, paragraphe){
    const inputElement = element;
    const maxLength = 100;
    const charCountElement = document.getElementById('charCount');
    const remainingChars = maxLength - inputElement.value.length;

    charCountElement.textContent = `${remainingChars} caractères restants`;

    inputElement.addEventListener('input', function() {
        const remainingChars = maxLength - this.value.length;
        charCountElement.textContent = `${remainingChars} caractères restants`;
        if (this.value.length > maxLength) {
            this.value = this.value.slice(0, maxLength);
            alert('Le texte est limité à $maxlength caractères.');
        }
    });
}

function delPhoto(event){
    event.preventDefault();
    let urldelphoto = this.href;
    axios
        .post(urldelphoto)
        .then(function(response){
            document.getElementById('listephoto').innerHTML = response.data.listephoto;
            initializeNavLinks();
        });
}

function onSortPhoto(sortablePhoto){
    let cols = sortablePhoto.children;
    for(let i = 0; i < cols.length; i++){
        let key = i + 1;
        let idphoto = cols[i].id;
        fixedPosition(idphoto, key);
    }
    //récupération du premier enfant
    let firstChild = cols[0];
    let card = firstChild.childNodes[1];
    card.className = "card text-white bg-primary mb-1";
    let cardBody = card.childNodes[5];
    cardBody.childNodes[1].textContent = 'Image de profil';
    // Récupération des autres enfants
    for(let i=1; i < cols.length; i++){
        let otherChild = cols[i];
        let card = otherChild.childNodes[1];
        card.className = "card text-dark bg-light mb-1";
        let cardBody = card.childNodes[5];
        cardBody.childNodes[1].textContent = 'Image de galerie';
    }

}

function fixedPosition(idphoto, key){
    let url = "/gestapp/photo/updatepositionphoto/"+ idphoto + "/" + key;
    axios
        .post(url)
        .then(function(response){
            toasterMessage(response.data.message);
        })
        .catch(function(error){
            alert(error);
        })
    ;
}

function genQrcode(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('QrcodeProperty').innerHTML = response.data.vueQr;
            toasterMessage(response.data.message);
            initializeNavLinks();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function AllCheckedPublication(){
    const isChecked = this.checked;
    const switches = document.querySelectorAll('#formProperty_Publication .form-check-input[type="checkbox"]');
    switches.forEach((switchInput) => {
        // Ne pas changer l'état des switches désactivés
        if (!switchInput.disabled) {
            switchInput.checked = isChecked;  // Cocher ou décocher les autres switches non désactivés
        }
    });
    //console.log(switches);
}

function submitNodeForm(){
    const listForm = ['formProperty_informations', 'formProperty_annonce', 'formProperty_chiffres', 'formProperty_complements', 'formProperty_Publication'];
    let activePanel = document.querySelectorAll('.tab-content .active #content-form');
    let nodeForm = activePanel[0];
    let form = nodeForm.querySelector('form');
    let nameForm = form.id;
    if(listForm.includes(nameForm)){
        tinymce.triggerSave();
        let action = form.action;
        let data = new FormData(form);
        axios
            .post(action, data)
            .then(function(response){
                nodeForm.innerHTML = response.data.form;
                initializeTinyMCE();
                initializeTomSelect('.oneChoice', TsSimple);
                initializeTomSelect('.multiChoice', TsMulti);
                toasterMessage(response.data.message);
            })
            .catch(function(error){
                console.log('error', error);
            })
        ;
    }else{
        console.log('pas d\'envoi possible');
    }
}

function submitModalForm(event){
    event.preventDefault();
    const listForm = ['formCustomer_add', 'formCustomer_edit', 'formProperty_addmandat', 'formAvenant_add'];
    let modalContent = event.currentTarget.parentNode.parentElement;
    let form = modalContent.querySelector('form');
    if (form){
        let nameForm = form.id;
        if(listForm.includes(nameForm)){
            let action = form.action;
            let data = new FormData(form);
            axios
                .post(action, data)
                .then(function(response){
                    const defaultActiveNavLink = document.querySelector('#admin-tab .nav-link.active');
                    if (defaultActiveNavLink) {
                        loadFormContent(defaultActiveNavLink);
                    }
                    toasterMessage(response.data.message);
                })
                .catch(function(error){
                    console.log('error', error);
                })
            ;
            initializeNavLinks();
        }
    }else{
        let url = event.currentTarget.href;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('listeCustomers').innerHTML = response.data.liste;
            })
            .catch(function(error){
                console.log(error);
            })
        ;
        initializeNavLinks();
    }
}

function submitAddSearchCustomer(event){
    event.preventDefault();
    let url = event.currentTarget.href;
    axios
        .post(url)
        .then(function (response){
            document.getElementById('listeCustomers').innerHTML = response.data.liste;
            let linkOpenModal = document.querySelectorAll('a.openModal');
            linkOpenModal.forEach(function(link){
                link.addEventListener('click', openModalXL);
            });
        })
        .catch(function (error){
            alert(error);
        })
    ;
}

function submitPhotos(event){
    event.preventDefault();
    let form = document.getElementById('formProperty_image');
    let action = form.action;
    let data = new FormData(form);
    let inputFilesPhotos = document.getElementById('property_image_images').value;
    if(!inputFilesPhotos){
        alert('Vous n\'avez pas charger de photos.');
    }else{
        axios
            .post(action, data)
            .then(function (response){
                document.getElementById('listephoto').innerHTML = response.data.liste;
                toasterMessage(response.data.message);
                initializeNavLinks();
            })
            .catch()
        ;
    }
}

function submitVideos(event){
    event.preventDefault();
    let form = document.getElementById('formProperty_Video');
    let action = form.action;
    let data = new FormData(form);
    let inputFilesVideos = document.getElementById('video_videoFile').value;
    if(!inputFilesVideos){
        alert('Vous n\'avez pas charger de vidéos.');
    }else{
        axios
            .post(action, data)
            .then(function(response){
                toasterMessage(response.data.message);
                initializeNavLinks();
            })
            .catch()
        ;
    }
}

function addResponsable(event){
    event.preventDefault();
    let form = document.getElementById('AddRespStructure');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('liste_respcustomer').innerHTML = response.data.listeResp;
            toasterMessage(response.data.message);
            reloadEventOnModal();
            form.reset();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function delResponsable(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('liste_respcustomer').innerHTML = response.data.listeResp;
            toasterMessage(response.data.message);
            reloadEventOnModal();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function delVideo(event){
    event.preventDefault();
    let url = event.currentTarget.href;
    axios
        .post(url)
        .then(function(response){
            toasterMessage(response.data.message);
            initializeNavLinks();
        })
        .catch(function(error){
            alert(error);
        })
    ;
}

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

const TsSimple = {
    //plugins: ['remove_button'],
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
};
const TsMulti = {
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
};

function initializeTomSelect(selector, options = {}) {
    document.querySelectorAll(selector).forEach(selectElement => {
        new TomSelect(selectElement, options);
    });
}

function reloadEventOnModal(){
    let btnAddResp = modal.querySelector('.modal-body #btnAddResp');
    if(btnAddResp){
        btnAddResp.addEventListener('click', addResponsable);
    }
    let btnSupprResps = document.querySelectorAll('.btnSupprResp');
    if(btnSupprResps){
        btnSupprResps.forEach(function(click){
            click.addEventListener('click', delResponsable);
        });
    }
    let linkOpenModal = document.querySelectorAll('a.openModal');
    linkOpenModal.forEach(function(link){
        link.addEventListener('click', openModalXL);
    });
    initializeTomSelect('.oneChoice', TsSimple);
    initializeTomSelect('.multiChoice', TsMulti);
}

// Initialisation après le chargement du DOM
document.addEventListener('DOMContentLoaded', initializeNavLinks);
initializeTinyMCE();
