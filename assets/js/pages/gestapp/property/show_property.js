import axios from 'axios';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/bootstrap/toaster";
import {zipcode, removeOptions, change_selectcity, formatDate, calculateChars, SelectChoice, selectChoiceOnChange, initializeTinyMCE} from "../../../components/appli/common";
import {calculatePrices} from "./functions";
import {typeClient, civilityChoice} from "../../../components/appli/customer";

export function initShowPropertyPage() {

    let btnUpdateForm = document.getElementById('btnUpdateProperty');
    const modalEl = document.getElementById('modal');
    if (!modalEl) return;
    const modalBs = new bootstrap.Modal(modalEl);

    /** reset modal automatique après fermeture */
    modalEl.addEventListener('hidden.bs.modal', () => {
        const deleteUrl = modal.dataset.deleteUrl;
        if (deleteUrl) {
            axios.post(deleteUrl)
                .then(() => console.log('Entité temporaire supprimée'))
                .catch(err => console.error('Erreur lors de la suppression', err));

            // Nettoyage de la valeur
            delete modal.dataset.deleteUrl;
        }

        modalEl.querySelector('.modal-dialog').classList.remove('modal-lg', 'modal-xl');
        modalEl.querySelector('.modal-body').innerHTML = `
              <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading…</span>
                </div>
              </div>`;
        modalEl.querySelector('.modal-footer').innerHTML = '\n' +
            '<a href="#" type="button" class="btn btn-sm btn-primary btnModalSubmit">Ajouter</a>\n' +
            '<button type="button" class="btn btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>';

    });

    function declareEvent(){

        const defaultActiveNavLink = document.querySelector('#admin-tab .nav-link.active');
        const navLinks = document.querySelectorAll('#admin-tab .nav-link');
        const btnUpdateProperty = document.getElementById('btnUpdateProperty');
        const btnNewProperty = document.getElementById('btnNewProperty');
        const btndellPhotos = document.querySelectorAll('a.delphoto');
        const btnsModalSubmit = document.querySelectorAll('.btnModalSubmit');

        // Charge le formulaire pour le lien actif par défaut au chargement du DOM
        if (defaultActiveNavLink) {
            loadFormContent(defaultActiveNavLink);
        }
        navLinks.forEach(function (navLink) {
            navLink.addEventListener('click', handleNavLinkClick);
        });

        if (btnUpdateProperty) {
            btnUpdateProperty.addEventListener('click', submitNodeForm);
        }

        if (btnNewProperty) {
            btnNewProperty.addEventListener('click', newPro_submitNodeForm);
        }

        btndellPhotos.forEach(function (link) {
            link.addEventListener('click', delPhoto);
        });
        btnsModalSubmit.forEach(function (link) {
            link.addEventListener('click', submitModalForm);
        });
    }

    function handleNavLinkClick(event) {
        event.preventDefault();
        const clickedNavLink = event.target.closest('.nav-link');
        if (clickedNavLink) {
            clickedNavLink.classList.add('active');
            loadFormContent(clickedNavLink);}
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
                        // Selection de la ville par le code postal
                        informations_zipcode.addEventListener('input', function(event){
                            zipcode(informations_zipcode, informations_commune, informations_selectcity);
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
                        estimate(response);
                    }
                    if(nodeFormName === 'Options'){
                        if(document.querySelector('input[name=complement\\[coproperty\\]]:checked').value > 0){
                            document.getElementById('coproprietyTaxe').classList.remove('d-none');
                        }
                        const radioCopropertyButtons = document.querySelectorAll('input[name=complement\\[coproperty\\]]');
                        radioCopropertyButtons.forEach(function(radio) {
                            radio.addEventListener("change", function() {
                                if (parseInt(this.value) === 1) {
                                    document.getElementById('coproprietyTaxe').classList.remove('d-none');
                                } else if (parseInt(this.value) === 0){
                                    document.getElementById('coproprietyTaxe').classList.add('d-none');
                                }
                            });
                        });
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

                        document.getElementById('property_image_images').addEventListener('change', maxSizePhotos);


                        let btnAddVideo = document.getElementById('btnAddVideo');
                        if(btnAddVideo){
                            btnAddVideo.addEventListener('click', submitVideos);
                        }
                        const btndelVideo = document.querySelector('.delVideo');
                        if(btndelVideo){
                            btndelVideo.addEventListener('click', delVideo);
                        }
                    }
                    if(nodeFormName === 'Publications'){
                        const switchAllPublication = document.getElementById('AllPublications');
                        switchAllPublication.addEventListener('change', AllCheckedPublication);
                    }
                    let linkOpenModal = document.querySelectorAll('a.openModal');
                    linkOpenModal.forEach(function(link){
                        link.addEventListener('click', openModalXL);
                    });
                    initializeTinyMCE(1500);
                })
                .catch(function(error) {
                    console.log('error', error);
                })
            ;
        }
    }

    function openModalXL(e){
        e.preventDefault();
        let a = e.currentTarget;
        let url = a.href;
        const [crud, contentTitle, option] = a.dataset.bsData.split('-');

        modalEl.querySelector('.modal-title').textContent = contentTitle;
        if(crud === 'ADDCUSTOMER' || crud === 'EDITCUSTOMER'){
            modal.querySelector('.modal-dialog').classList.add('modal-xl');
            // Charger le contenu dans la modal
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter l\'acheteur';
                    confirmBtn.href = url;
                    modalEl.dataset.deleteUrl = data.deleteUrl;

                    typeClient();
                    civilityChoice();

                    // Variables liés aux modifications des champs du bloc adresse.
                    let customer_commune = document.getElementById('customer_city');
                    let customer_zipcode = document.getElementById('customer_zipcode');
                    let customer_selectcity = document.getElementById('customer_selectcity');
                    let customer_addresseInput = document.getElementById('customer_adress');
                    let customer_proCity = document.getElementById('customer_proCity');
                    let customer_proZipcode = document.getElementById('customer_proZipcode');
                    let customer_proSelectcity = document.getElementById('customer_proSelectcity');
                    if (customer_commune && customer_addresseInput) {
                        customer_zipcode.addEventListener('input', function (event) {
                            zipcode(customer_zipcode, customer_commune, customer_selectcity);
                        });
                        customer_selectcity.addEventListener('change', function (event) {
                            change_selectcity(customer_zipcode, customer_commune, customer_selectcity);
                        });
                        customer_proZipcode.addEventListener('input', function (event) {
                            zipcode(customer_proZipcode, customer_proCity, customer_proSelectcity);
                        });
                        customer_proSelectcity.addEventListener('change', function (event) {
                            change_selectcity(customer_proZipcode, customer_proCity, customer_proSelectcity);
                        });
                    }
                    declareEvent();
                })
                .catch(function(error){
                    console.log('Erreur lors du chargement de la modal', error);
                });
        }
        else if(crud === "DELCUSTOMER"){
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
                    declareEvent();
                }
            }

        }
        else if(crud === 'ADDMANDAT'){
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
                            document.getElementById('refmandatError').parentElement.classList.remove('d-none');
                            document.getElementById('refmandatError').classList.add('text-warning');
                            document.getElementById('refmandatError').innerHTML = "Corrigez ce numéro, il est présent dans la liste des biens <b>Paps immo</b>.";
                            document.getElementById('btnModalSubmit').classList.add('d-none');
                        }else{
                            addMandat.classList.remove("is-invalid");
                            addMandat.classList.add("is-valid");
                            document.getElementById('refmandatError').parentElement.classList.remove('d-none');
                            document.getElementById('refmandatError').classList.add('text-success');
                            document.getElementById('refmandatError').innerHTML = "Numéro de mandat valide.";
                        }
                    });
                    initializeTinyMCE(1500);
                })
                .catch(function(error){
                    console.log('Erreur lors du chargement de la modal', error);
                });
        }
        else if(crud === 'ADDAVENANT'){
            axios
                .get(url)
                .then(function(response){
                    modal.querySelector('.modal-body').innerHTML = response.data.form;
                    calculatePrices(document.getElementById('avenant_price'),document.getElementById('avenant_honoraires'), document.getElementById('avenant_priceFai'));
                    initializeTinyMCE(1500);
                })
                .catch(function(error){
                    console.log('Erreur lors du chargement de la modal', error);
                });
        }
        else{
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
        modalBs.show();
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
                    initializeTinyMCE(1500);
                    initializeTomSelect('.oneChoice', TsSimple);
                    initializeTomSelect('.multiChoice', TsMulti);
                    toasterMessage(response.data.message);
                    estimate(response);
                    let linkOpenModal = document.querySelectorAll('a.openModal');
                    linkOpenModal.forEach(function(link){
                        link.addEventListener('click', openModalXL);
                    });
                    declareEvent();
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
        delete modal.dataset.deleteUrl;
        const listForm = ['formCustomer_add', 'formCustomer_edit', 'formProperty_addmandat', 'formAvenant_add'];
        let modalContent = event.currentTarget.parentNode.parentElement;
        let form = modalContent.querySelector('form');
        if (form){
            let nameForm = form.id;
            let action = form.action;
            let data = new FormData(form);
            if(listForm.includes(nameForm)){
                axios
                    .post(action, data)
                    .then(function({data}) {
                        if(nameForm === 'formCustomer_add' || nameForm === 'formCustomer_edit'){
                            if(data.code === 422){
                                form.outerHTML = data.formView;
                                toasterMessage(data.message);
                                typeClient();
                                civilityChoice();

                                // Variables liés aux modifications des champs du bloc adresse.
                                let customer_commune = document.getElementById('customer_city');
                                let customer_zipcode = document.getElementById('customer_zipcode');
                                let customer_selectcity = document.getElementById('customer_selectcity');
                                let customer_addresseInput = document.getElementById('customer_adress');
                                let customer_proCity = document.getElementById('customer_proCity');
                                let customer_proZipcode = document.getElementById('customer_proZipcode');
                                let customer_proSelectcity = document.getElementById('customer_proSelectcity');
                                if (customer_commune && customer_addresseInput) {
                                    customer_zipcode.addEventListener('input', function (event) {
                                        zipcode(customer_zipcode, customer_commune, customer_selectcity);
                                    });
                                    customer_selectcity.addEventListener('change', function (event) {
                                        change_selectcity(customer_zipcode, customer_commune, customer_selectcity);
                                    });
                                    customer_proZipcode.addEventListener('input', function (event) {
                                        zipcode(customer_proZipcode, customer_proCity, customer_proSelectcity);
                                    });
                                    customer_proSelectcity.addEventListener('change', function (event) {
                                        change_selectcity(customer_proZipcode, customer_proCity, customer_proSelectcity);
                                    });
                                }
                            }
                            else{
                                const defaultActiveNavLink = document.querySelector('#admin-tab .nav-link.active');
                                if (defaultActiveNavLink) {
                                    loadFormContent(defaultActiveNavLink);
                                }
                                toasterMessage(data.message);
                                declareEvent();
                                modalBs.hide();
                            }
                        }
                        else if(nameForm === 'formAvenant_add'){
                            const defaultActiveNavLink = document.querySelector('#admin-tab .nav-link.active');
                            if (defaultActiveNavLink) {
                                loadFormContent(defaultActiveNavLink);
                            }
                            toasterMessage(data.message);
                            declareEvent();
                            modalBs.hide();
                        }
                    })
                    .catch(function(error){
                        console.log('error', error);
                    })
                ;
            }
        }else{
            let url = event.currentTarget.href;
            axios
                .post(url)
                .then(function(response){
                    document.getElementById('listeCustomers').innerHTML = response.data.liste;
                    declareEvent();
                    modalBs.hide();
                })
                .catch(function(error){
                    console.log(error);
                })
            ;
        }
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

    // Contruction des fonctions complémentaires
    // Onglets Informations / Mandat

    // Onglet Annonce

    // Onglet Vendeurs
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

    // Onglet Chiffres
    function estimate(response){
        let sales = document.getElementById('sale');
        let rent = document.getElementById('rent');
        let rentCommerce = document.getElementById('rentCommerce');

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

    // Onglet Complements

    // Onglet Galerie
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

    function delPhoto(event){
        event.preventDefault();
        let urldelphoto = this.href;
        axios
            .post(urldelphoto)
            .then(function(response){
                document.getElementById('listephoto').innerHTML = response.data.listephoto;
                declareEvent();
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

    function maxSizePhotos(event){
        const maxSizeMB = 20; // Limite en mégaoctets
        const maxSizeBytes = maxSizeMB * 1024 * 1024;
        const files = event.target.files;
        let totalSize = 0;

        for (let i = 0; i < files.length; i++) {
            totalSize += files[i].size;
        }
        console.log(totalSize);
        if (totalSize > maxSizeBytes) {
            alert('La taille totale des fichiers dépasse '+ maxSizeMB +' Mo. Veuillez réduire la sélection.');
            event.target.value = ''; // Réinitialise le champ file
        }

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
                })
                .catch()
            ;
            declareEvent();
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
                })
                .catch()
            ;
            declareEvent();
        }
    }

    // Onglets Publications
    function AllCheckedPublication(){
        const isChecked = this.checked;
        const switches = document.querySelectorAll('#formProperty_Publication .form-check-input[type="checkbox"]');
        switches.forEach((switchInput) => {
            // Ne pas changer l'état des switches désactivés
            if (!switchInput.disabled) {
                switchInput.checked = isChecked;  // Cocher ou décocher les autres switches non désactivés
            }
        });
    }

    declareEvent();
}