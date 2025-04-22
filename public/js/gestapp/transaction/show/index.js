const modalCustomer = document.getElementById('modalCustomer');
const modalDelCustomer = document.getElementById('modalDelCustomer');
const modalAddcollaborateur = document.getElementById('modalAddColl');

let btnSubmitCustomer = document.getElementById('btnSubmitCustomer');
let btnDelCustommer = document.getElementById('btnDellCustomer');
const selectCustomer = "selectCustomer";

let btnAddDatePromise = document.getElementById('btnAddDatePromise');
let btnAddPromisePdf = document.getElementById('btnAddPromisePdf');
let btnAddPromisePdfbyColl = document.getElementById('btnAddPromisePdfbyColl');
let btnAddPromisePdfControl = document.getElementById('btnAddPromisePdfControl');
let btnEditPromisePdf = document.getElementById('btnEditPromisePdf');

let btnAddDateActe = document.getElementById('btnAddDateActe');
let btnAddActePdf = document.getElementById('btnAddActePdf');
let btnAddActePdfbyColl = document.getElementById('btnAddActePdfbyColl');
let btnAddActePdfControl = document.getElementById('btnAddActePdfControl');
let btnEditActePdf = document.getElementById('btnEditActePdf');

let btnAddTracfinPdf = document.getElementById('btnAddTracfinPdf');
let btnAddTracfinPdfbyColl = document.querySelector('#rowTracfinPdf #btnAddTracfinPdfbyColl');
let btnAddTracfinPdfControl = document.getElementById('btnAddTracfinPdfControl');
let btnEditTracfinPdf = document.getElementById('btnEditTracfinPdf');

let btnAddInvoicePdf = document.getElementById('btnAddInvoicePdf');
let btnAddInvoicePdfbyColl = document.getElementById('btnAddInvoicePdfbyColl');
let btnAddInvoicePdfControl = document.getElementById('btnAddInvoicePdfControl');
let btnEditInvoicePdf = document.getElementById('btnEditInvoicePdf');

let btnDocumentPdfError = document.getElementById('btnDocumentPdfError');
let btnHonorairePdf = document.querySelector('#rowHonorairesPdf #btnHonorairePdf');
let btnSubmitColl = document.getElementById('btnSubmitColl');

if(document.querySelector('.supprDocument') !== null){
    document.querySelectorAll('.supprDocument').forEach(function(link){
        link.addEventListener('click', supprDocument);
    });
}
if(document.querySelector('.btnDocumentPdfError') !== null){
    document.querySelectorAll('.btnDocumentPdfError').forEach(function(link){
        link.addEventListener('click', errorDocument);
    });
}
document.querySelectorAll('.supprDocument').forEach(function(link){
    link.addEventListener('click', supprDocument);
});
btnSubmitColl.addEventListener('click', submitCollaborator);

// Customer
btnSubmitCustomer.addEventListener('click', submitCustomer);

// Promise
if(btnAddDatePromise !== null){btnAddDatePromise.addEventListener('click', submitDatePromise);}
if(btnAddPromisePdf !== null){btnAddPromisePdf.addEventListener('click', submitPromisePdf);}
if(btnAddPromisePdfbyColl !== null){btnAddPromisePdfbyColl.addEventListener('click', submitPromisePdfbyColl);}
if(btnAddPromisePdfControl !== null){btnAddPromisePdfControl.addEventListener('click', submitPromisePdfControl);}
if(btnEditPromisePdf !== null){btnEditPromisePdf.addEventListener('click', editPromisePdf);}
// Acte
if(btnAddDateActe !== null){btnAddDateActe.addEventListener('click', submitDateActe);}
if(btnAddActePdf !== null){btnAddActePdf.addEventListener('click', submitActePdf);}
if(btnAddActePdfbyColl !== null){btnAddActePdfbyColl.addEventListener('click', submitActePdfbyColl);}
if(btnAddActePdfControl !== null){btnAddActePdfControl.addEventListener('click', submitActePdfControl);}
if(btnEditActePdf !== null){btnEditActePdf.addEventListener('click', editActePdf);}
// Tracfin
if(btnAddTracfinPdf !== null){btnAddTracfinPdf.addEventListener('click', submitTracfinPdf);}
if(btnAddTracfinPdfbyColl !== null){btnAddTracfinPdfbyColl.addEventListener('click', submitTracfinPdfbyColl);}
if(btnAddTracfinPdfControl !== null){btnAddTracfinPdfControl.addEventListener('click', submitTracfinPdfControl);}
if(btnEditTracfinPdf !== null){btnEditTracfinPdf.addEventListener('click', editTracfinPdf);}
// Facture
if(btnAddInvoicePdf !== null){btnAddInvoicePdf.addEventListener('click', submitInvoicePdf);}
if(btnAddInvoicePdfbyColl !== null){btnAddInvoicePdfbyColl.addEventListener('click', submitInvoicePdfbyColl);}
if(btnAddInvoicePdfControl !== null){btnAddInvoicePdfControl.addEventListener('click', submitInvoicePdfControl);}
if(btnEditInvoicePdf !== null){btnEditInvoicePdf.addEventListener('click', editInvoicePdf);}
// Généralité
if(btnDocumentPdfError !== null){btnDocumentPdfError.addEventListener('click', errorDocument);}
if(btnHonorairePdf !== null){btnHonorairePdf.addEventListener('click', submitHonoraires);}

// PARTIE Codepostal sur création & modification du client
// ---------------------------------------
let commune = document.getElementById('customer_city');
let zipcode = document.getElementById('customer_zipcode');
let selectcity = document.getElementById('customer_selectcity');
let proCity = document.getElementById('customer_proCity');
let proZipcode = document.getElementById('customer_proZipcode');
let proSelectcity = document.getElementById('customer_proSelectcity');
if(zipcode !== null) {
    zipcode.addEventListener('input', function(event){
        zipcode_api(zipcode, commune, selectcity);
    });
    selectcity.addEventListener('change', function (event){
        change_selectcity(zipcode, commune, selectcity);
    });
    proZipcode.addEventListener('input', function(event){
        zipcode_api(proZipcode, proCity, selectcity);
    });
    proSelectcity.addEventListener('change', function (event){
        change_selectcity(proZipcode, proCity, proSelectcity);
    });
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

function tomSelect(selectId){
    new TomSelect(selectId,{
        plugins: ['remove_button'],
        create: true,
        valueField: 'value',      // Champ contenant la valeur de l'option
        labelField: 'text',       // Champ contenant le texte affiché
        searchField: 'text',      // Champ utilisé pour la recherche
        load: function(query, callback) {
            var url = "/gestapp/customer/getCustomer";  // URL du contrôleur Symfony
            fetch(url)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error("Erreur lors de la récupération des options.");
                    }
                    return response.json();
                })
                .then(function(json) {
                    console.log("Données reçues:", json); // Débogage
                    callback(json);
                })
                .catch(function(error) {
                    console.error("Erreur:", error);  // Débogage des erreurs
                    callback();
                });
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
}

// ------------------------------------------------------------------------------------------
// Actions sur le modal de gestion des clients
// ------------------------------------------------------------------------------------------
modalCustomer.addEventListener('show.bs.modal', function (event){
    // Button that triggered the modal
    let button = event.relatedTarget;
    // extraction de la variable
    let recipient = button.getAttribute('data-bs-whatever');
    let crud = recipient.split('-')[0];
    let contentTitle = recipient.split('-')[1];
    let id = recipient.split('-')[2];
    if(crud === "ADD" || crud === "EDIT"){
        let modalHeaderH5 = modalCustomer.querySelector('.modal-title');
        let modalBody = modalCustomer.querySelector('.modal-body');
        modalHeaderH5.textContent = contentTitle;
        let url = button.href;
        axios
            .get(url)
            .then(function(response){
                modalBody.innerHTML = response.data.formView;
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

                let typeClient = modalCustomer.querySelector('.modal-body #customer_typeClient');
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
                let commune = document.getElementById('customer_city');
                let zipcode = document.getElementById('customer_zipcode');
                let selectcity = document.getElementById('customer_selectcity');
                let proCity = document.getElementById('customer_proCity');
                let proZipcode = document.getElementById('customer_proZipcode');
                let proSelectcity = document.getElementById('customer_proSelectcity');
                zipcode.addEventListener('input', function(event){
                    zipcode_api(zipcode, commune, selectcity);
                });
                selectcity.addEventListener('change', function (event){
                    change_selectcity(zipcode, commune, selectcity);
                });
                proZipcode.addEventListener('input', function(event){
                    zipcode_api(proZipcode, proCity, proSelectcity);
                });
                proSelectcity.addEventListener('change', function (event){
                    change_selectcity(proZipcode, proCity, proSelectcity);
                });
            })
            .catch(function(error){
                console.log(error);
            });
    }
});

// comportement à adopter à la fermeture de la modal des customers
modalCustomer.addEventListener('hidden.bs.modal', event => {
    let form = modalCustomer.querySelector('#formCustomer_add');
    let btnSubmit = modalCustomer.querySelector('.modal-footer #btnSubmitCustomer');
    if(form){
        if (event.target !== btnSubmit){
            let idCustomer = document.getElementById('idCustomer').value;
            axios
                .post('/gestapp/customer/'+ idCustomer +'/delontransaction')
                .then(function(response){
                    toasterMessage(response.data.message);
                })
            ;
        }
        
    }
    modalCustomer.querySelector('.modal-dialog').classList.remove('modal-lg');
    modalCustomer.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">\n" +
        "<div class=\"spinner-border text-primary\" role=\"status\">\n" +
        "<span class=\"visually-hidden\">Loading...</span>\n" +
        "</div>\n" +
        "</div>";

});
// ------------------------------------------------------------------------------------------
// Actions sur le modal de suppression des clients
// ------------------------------------------------------------------------------------------
modalDelCustomer.addEventListener('show.bs.modal', function (event) {
    // Button that triggered the modal
    let a = event.relatedTarget;
    let url = a.href;
    // extraction de la variable
    let aSubmit = modalDelCustomer.querySelector('#btnDellCustomer');
    aSubmit.href = url;
});

// ------------------------------------------------------------------------------------------
// Actions sur le modal d'ajout d'un collaborateur
// ------------------------------------------------------------------------------------------
modalAddcollaborateur.addEventListener('show.bs.modal', function (event) {
    // Button that triggered the modal
    let a = event.relatedTarget;
    // extraction de la variable
    let recipient = a.getAttribute('data-bs-whatever');
    let url = a.href;
    let crud = recipient.split('-')[0];
    let contentTitle = recipient.split('-')[1];
    let name = recipient.split('-')[2];
    if(crud === "ADD"){
        let modalHeaderH5 = modalAddcollaborateur.querySelector('.modal-title');
        let modalBody = modalAddcollaborateur.querySelector('.modal-body');
        let submitFooter = modalAddcollaborateur.querySelector('.modal-footer #btnSubmitColl');
        modalHeaderH5.textContent = contentTitle;
        submitFooter.textContent = "Ajouter au projet";
        submitFooter.href = url;
        submitFooter.setAttribute('data-bs-whatever', name);
        axios
            .get(url)
            .then(function(response){
                modalBody.innerHTML = response.data.formView;
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }else if(crud === "DEL"){
        let modalHeaderH5 = modalAddcollaborateur.querySelector('.modal-title');
        let modalBody = modalAddcollaborateur.querySelector('.modal-body');
        let submitFooter = modalAddcollaborateur.querySelector('.modal-footer #btnSubmitColl');
        modalHeaderH5.textContent = contentTitle;
        submitFooter.textContent = "Retirer du projet";
        submitFooter.href = url;
        submitFooter.classList.add('supprCollaborator');
        modalBody.innerHTML = "<p class=\'mb-0\'>Vous êtes sur le point de retirer ce collaborateur du projet.<br>Etes-vous sur de vouloir pour suivre la démarche.</p>";
    }
    else if(crud === "DELINV"){
        let modalHeaderH5 = modalAddcollaborateur.querySelector('.modal-title');
        let modalBody = modalAddcollaborateur.querySelector('.modal-body');
        let submitFooter = modalAddcollaborateur.querySelector('.modal-footer a');
        modalHeaderH5.textContent = contentTitle;
        submitFooter.textContent = "Supprimer la facture";
        submitFooter.href = url;
        submitFooter.removeAttribute('id');
        submitFooter.classList.add('supprCollInv');
        modalBody.innerHTML = "<p class=\'mb-0\'>Vous êtes sur le point de supprimer la facture que vous aviez déposée.<br>Etes-vous sur de vouloir pour suivre la démarche.</p>";
    }else if(crud === "DELAE"){
        let modalHeaderH5 = modalAddcollaborateur.querySelector('.modal-title');
        let modalBody = modalAddcollaborateur.querySelector('.modal-body');
        let submitFooter = modalAddcollaborateur.querySelector('.modal-footer #btnSubmitColl');
        modalHeaderH5.textContent = contentTitle;
        submitFooter.textContent = "Retirer du projet";
        submitFooter.href = url;
        submitFooter.classList.add('supprAgencyEmployed');
        modalBody.innerHTML = "<p class=\'mb-0\'>Vous êtes sur le point de retirer cette agent extérieur du projet.<br>Etes-vous sur de vouloir pour suivre la démarche.</p>";
    }
});

if(document.querySelector(".supprCollInv") !== null){
    document.querySelector(".supprCollInv").addEventListener('click', supprInvoiceColl);
}

btnDelCustommer.addEventListener('click', dellCustomer);

function submitCustomer(event){
    event.preventDefault;
    const listForm = ['formCustomer_add', 'formCustomer_edit'];
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
                    if(response.data.type === 1){
                        document.getElementById('blockSailers').innerHTML = response.data.liste;
                        document.getElementById('transactionstep2_dateAtPromise').classList.remove('d-none');
                        document.getElementById('btnAddDatePromise').classList.remove('d-none');
                        document.getElementById('rowEmptyPromiseDate').remove();
                    }else if(response.data.type === 2){
                        document.getElementById('blockBuyers').innerHTML = response.data.liste;
                        document.getElementById('transactionstep2_dateAtPromise').classList.remove('d-none');
                        document.getElementById('btnAddDatePromise').classList.remove('d-none');
                        document.getElementById('rowEmptyPromiseDate').remove();
                    }

                })
                .catch(function (error) {
                    console.log(error);
                })
            ;
        }
    }
}

function dellCustomer(event){
    event.preventDefault();
    let url = this.href;
    axios
        .get(url)
        .then(function(response){
            document.getElementById('blockBuyers').innerHTML = response.data.liste;
            btnDelCustommer.addEventListener('click', dellCustomer);
        })
        .catch(function(error){
            console.log(error);
        });
}

// ------------------------------------------------------------------------------------------
// Actions sur le dépôt de la promesse de vente
// ------------------------------------------------------------------------------------------
function submitDatePromise(event){
    event.preventDefault();
    let form = document.getElementById('addDatePromiseForm');
    let action = form.action;
    let data = new FormData(form);
    let dateAtPromise = document.getElementById('transactionstep2_dateAtPromise').value;
    if(!dateAtPromise){
        alert( "Aucune date n'a été renseignée ! veuillez compléter le champs" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('transactionstep3_promisePdfFilename').classList.remove('d-none');
            document.getElementById('btnAddPromisePdf').classList.remove('d-none');
            document.getElementById('rowEmptyPromisePdf').remove();
            allAddEvent();
            toasterMessage(response.data.message);

        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitPromisePdf(){
    let form = document.getElementById('transactionstep3');
    let action = form.action;
    let data = new FormData(form);
    let promisePdfFilename = document.getElementById('transactionstep3_promisePdfFilename').value;
    if(!promisePdfFilename){
        alert( "Veuillez charger un document" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowPromisePdf').innerHTML = response.data.rowpromise;
            document.getElementById('rowHonorairesPdf').innerHTML = response.data.rowhonoraires;
            document.getElementById('transaction_actedate_dateAtSale').classList.remove('d-none');
            document.getElementById('btnAddDateActe').classList.remove('d-none');
            document.getElementById('rowEmptyDateActe').remove();
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function editPromisePdf(){
    let form = document.getElementById('transactionstep3');
    let action = form.action;
    let data = new FormData(form);
    let promisePdfFilename = document.getElementById('transactionstep3_promisePdfFilename').value;
    if(!promisePdfFilename){
        alert( "Veuillez charger un document" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowPromisePdf').innerHTML = response.data.rowpromise;
            document.getElementById('rowHonorairesPdf').innerHTML = response.data.rowhonoraires;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitPromisePdfbyColl(){
    let form = document.getElementById('transactionstep3');
    let action = form.action;
    let data = new FormData(form);
    let promisePdfFilename = document.getElementById('transactionstep3_promisePdfFilename').value;
    if(!promisePdfFilename){
        alert( "Veuillez charger un document" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowPromisePdf').innerHTML = response.data.rowpromise;
            document.getElementById('rowHonorairesPdf').innerHTML = response.data.rowhonoraires;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitPromisePdfControl(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowPromisePdf').innerHTML = response.data.rowpromise;
            document.getElementById('rowHonorairesPdf').innerHTML = response.data.rowhonoraires;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

// ------------------------------------------------------------------------------------------
// Actions sur le dépôt de l'attestation de l'acte de vente
// ------------------------------------------------------------------------------------------
function submitDateActe(event){
    event.preventDefault();
    let form = document.getElementById('addDateActeForm');
    let action = form.action;
    let data = new FormData(form);
    let dateAtSale = document.getElementById('transaction_actedate_dateAtSale').value;
    if(!dateAtSale){
        alert( "Aucune date n'a été renseignée ! veuillez compléter le champs" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('transaction_actepdf_actePdfFilename').classList.remove('d-none');
            document.getElementById('btnAddActePdf').classList.remove('d-none');
            document.getElementById('rowEmptyActePdf').remove();
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitActePdf(event){
    let form = document.getElementById('transactionactepdf');
    let action = form.action;
    let data = new FormData(form);
    let actePdfFilename = document.getElementById('transaction_actepdf_actePdfFilename').value;
    if(!actePdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowActePdf').innerHTML = response.data.rowacte;
            document.getElementById('rowTracfinPdf').innerHTML = response.data.rowtracfin;
            document.getElementById('btnAddTracfinPdf').classList.remove('d-none');
            document.getElementById('rowEmptyTracfinPdf').remove();
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        });
}

function editActePdf(event){
    let form = document.getElementById('transactionactepdf');
    let action = form.action;
    let data = new FormData(form);
    let actePdfFilename = document.getElementById('transaction_actepdf_actePdfFilename').value;
    if(!actePdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            window.location.reload();
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        });
}

function submitActePdfbyColl(){
    let form = document.getElementById('transactionactepdf');
    let action = form.action;
    let data = new FormData(form);
    let actePdfFilename = document.getElementById('transaction_actepdf_actePdfFilename').value;
    if(!actePdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowActePdf').innerHTML = response.data.rowacte;
            document.getElementById('rowTracfinPdf').innerHTML = response.data.rowtracfin;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitActePdfControl(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowActePdf').innerHTML = response.data.row;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

// ------------------------------------------------------------------------------------------
// Actions sur le dépôt du TracFin
// ------------------------------------------------------------------------------------------
function submitTracfinPdf(event){
    let form = document.getElementById('transactiontracfinpdf');
    let action = form.action;
    let data = new FormData(form);
    let tracfinPdfFilename = document.getElementById('transaction_tracfinpdf_tracfinPdfFilename').value;
    if(!tracfinPdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowTracfinPdf').innerHTML = response.data.rowtracfin;
            document.getElementById('transaction_invoicepdf_invoicePdfFilename').classList.remove('d-none');
            document.getElementById('btnAddInvoicePdf').classList.remove('d-none');
            document.getElementById('rowEmptyInvoicePdf').remove();
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        });
}

function editTracfinPdf(event){
    let form = document.getElementById('transactiontracfinpdf');
    let action = form.action;
    let data = new FormData(form);
    let tracfinPdfFilename = document.getElementById('transaction_tracfinpdf_tracfinPdfFilename').value;
    if(!tracfinPdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            window.location.reload();
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        });
}

function submitTracfinPdfbyColl(){
    let form = document.getElementById('transactiontracfinpdf');
    let action = form.action;
    let data = new FormData(form);
    let tracfinPdfFilename = document.getElementById('transaction_tracfinpdf_tracfinPdfFilename').value;
    if(!tracfinPdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowTracfinPdf').innerHTML = response.data.rowtracfin;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitTracfinPdfControl(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            event.preventDefault();
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowTracfinPdf').innerHTML = response.data.rowtracfin;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

// ------------------------------------------------------------------------------------------
// Actions sur le dépôt honoraire
// ------------------------------------------------------------------------------------------

function submitInvoicePdf(event){
    let form = document.getElementById('transactioninvoicepdf');
    let action = form.action;
    let data = new FormData(form);
    let invoicePdfFilename = document.getElementById('transaction_invoicepdf_invoicePdfFilename').value;
    if(!invoicePdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowInvoicePdf').innerHTML = response.data.row;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        });
}

function editInvoicePdf(event){
    let form = document.getElementById('transactioninvoicepdf');
    let action = form.action;
    let data = new FormData(form);
    let invoicePdfFilename = document.getElementById('transaction_invoicepdf_invoicePdfFilename').value;
    if(!invoicePdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            window.location.reload();
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        });
}

function submitInvoicePdfbyColl(){
    let form = document.getElementById('transactioninvoicepdf');
    let action = form.action;
    let data = new FormData(form);
    let invoicePdfFilename = document.getElementById('transaction_invoicepdf_invoicePdfFilename').value;
    if(!invoicePdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowInvoicePdf').innerHTML = response.data.row;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitInvoicePdfControl(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            event.preventDefault();
            document.getElementById('progress_project').innerHTML = response.data.transState;
            document.getElementById('rowInvoicePdf').innerHTML = response.data.row;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

// ------------------------------------------------------------------------------------------
// Fonctions collaborateurs
// ------------------------------------------------------------------------------------------
function submitCollaborator(event){
    event.preventDefault();
    if(this.classList.contains('supprCollaborator')){
        let url = this.href;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('listCollaborator').innerHTML = response.data.listCollaborator;
                document.getElementById('rowInvoicesPdf').innerHTML = response.data.row;
            })
            .catch(function (error){
                console.log(error);
            })
        ;
    }else if(this.classList.contains('supprCollInv')){
        let url = this.href;
        axios
            .post(url)
            .then(function (response){
                document.getElementById('rowInvoicesPdf').innerHTML = response.data.row;
                // Toaster
                toasterMessage(response.data.message);
            })
            .catch(function (error){
                console.log(error);
            });
    }else if(this.classList.contains('supprAgencyEmployed')){
        let url = this.href;
        axios
            .post(url)
            .then(function (response){
                document.querySelector('#blockAgencyEmployed .card-body').innerHTML = response.data.view;
                toasterMessage(response.data.message);
            })
            .catch(function (error){
                console.log(error);
            });
    }else{
        let opt = this.getAttribute('data-bs-whatever');
        let name = opt.split('-')[0];
        let form = document.getElementById(name);
        let data = new FormData(form);
        let action = form.action;
        if(name === 'FormAddCollaborator'){
            axios
                .post(action, data)
                .then(function(response){
                    document.getElementById('listCollaborator').innerHTML = response.data.liste;
                    document.getElementById('rowInvoicesPdf').innerHTML = response.data.row;
                })
                .catch(function (error){
                    console.log(error);
                })
            ;
        }else{
            axios
                .post(action, data)
                .then(function(response){
                    document.querySelector('#blockAgencyEmployed .card-body').innerHTML = response.data.view;
                    toasterMessage(response.data.message);
                })
                .catch(function (error){
                    console.log(error);
                })
            ;
        }

    }
}
// ------------------------------------------------------------------------------------------
// Fonctions générique sur la page
// ------------------------------------------------------------------------------------------
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

function supprDocument(event){
    event.preventDefault();
    let url = this.href;
    let idRow = this.parentNode.parentNode.id;
    axios
        .post(url)
        .then(function(response){
            if(idRow === 'rowPromisePdf'){
                document.getElementById('rowPromisePdf').innerHTML = response.data.rowpromise;
            }
            else if(idRow === 'rowActePdf'){
                document.getElementById('rowActePdf').innerHTML = response.data.rowacte;
            }
            else if(idRow === 'rowTracfinPdf'){
                document.getElementById('rowTracfinPdf').innerHTML = response.data.rowtracfin;
            }
            else if(idRow === 'rowHonorairesPdf'){
                document.getElementById('rowHonorairesPdf').innerHTML = response.data.rowhonoraires;
            }else if(idRow === 'rowInvoicePdf'){
                document.getElementById('rowInvoicePdf').innerHTML = response.data.rowinvoice;
            }
            allAddEvent();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function errorDocument(event){
    event.preventDefault();
    let url = this.href;
    let idRow = this.parentNode.parentNode.id;
    axios
        .post(url)
        .then(function(response){
            if(idRow === 'rowPromisePdf'){
                document.getElementById('rowPromisePdf').innerHTML = response.data.rowpromise;
            }
            else if(idRow === 'rowActePdf'){
                document.getElementById('rowActePdf').innerHTML = response.data.rowacte;
            }
            else if(idRow === 'rowTracfinPdf'){
                document.getElementById('rowTracfinPdf').innerHTML = response.data.rowtracfin;
            }
            else if(idRow === 'rowHonorairesPdf'){
                document.getElementById('rowHonorairesPdf').innerHTML = response.data.rowhonoraires;
            }
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function(error){
            console.log(error);
        });
}

function submitHonoraires(event){
    event.preventDefault();
    let form = document.querySelector('#rowHonorairesPdf #transactionhonoraires');
    let action = form.action;
    let data = new FormData(form);
    let honorairesPdfFilename = document.querySelector('#rowHonorairesPdf #transaction_honoraires_honorairesPdfFilename').value;
    if(!honorairesPdfFilename){
        alert( "Veuillez charger un document !" );
        return false;
    }
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('rowHonorairesPdf').innerHTML = response.data.row;
            allAddEvent();
            toasterMessage(response.data.message);
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function allAddEvent(){
    // Customer
    btnSubmitCustomer.addEventListener('click', submitCustomer);
// Promise
    if(btnAddDatePromise !== null){btnAddDatePromise.addEventListener('click', submitDatePromise);}
    if(btnAddPromisePdf !== null){btnAddPromisePdf.addEventListener('click', submitPromisePdf);}
    if(btnAddPromisePdfbyColl !== null){btnAddPromisePdfbyColl.addEventListener('click', submitPromisePdfbyColl);}
    if(btnAddPromisePdfControl !== null){btnAddPromisePdfControl.addEventListener('click', submitPromisePdfControl);}
    if(btnEditPromisePdf !== null){btnEditPromisePdf.addEventListener('click', editPromisePdf);}
// Acte
    if(btnAddDateActe !== null){btnAddDateActe.addEventListener('click', submitDateActe);}
    if(btnAddActePdf !== null){btnAddActePdf.addEventListener('click', submitActePdf);}
    if(btnAddActePdfbyColl !== null){btnAddActePdfbyColl.addEventListener('click', submitActePdfbyColl);}
    if(btnAddActePdfControl !== null){btnAddActePdfControl.addEventListener('click', submitActePdfControl);}
    if(btnEditActePdf !== null){btnEditActePdf.addEventListener('click', editActePdf);}
// Tracfin
    if(btnAddTracfinPdf !== null){btnAddTracfinPdf.addEventListener('click', submitTracfinPdf);}
    if(document.querySelector('#rowTracfinPdf #btnAddTracfinPdfbyColl') !== null){document.querySelector('#rowTracfinPdf #btnAddTracfinPdfbyColl').addEventListener('click', submitTracfinPdfbyColl);}
    if(btnAddTracfinPdfControl !== null){btnAddTracfinPdfControl.addEventListener('click', submitTracfinPdfControl);}
    if(btnEditTracfinPdf !== null){btnEditTracfinPdf.addEventListener('click', editTracfinPdf);}
// Facture
    if(btnAddInvoicePdf !== null){btnAddInvoicePdf.addEventListener('click', submitInvoicePdf);}
    if(btnAddInvoicePdfbyColl !== null){btnAddInvoicePdfbyColl.addEventListener('click', submitInvoicePdfbyColl);}
    if(btnAddInvoicePdfControl !== null){btnAddInvoicePdfControl.addEventListener('click', submitInvoicePdfControl);}
    if(btnEditInvoicePdf !== null){btnEditInvoicePdf.addEventListener('click', editInvoicePdf);}
// Généralité
    if(document.querySelector('.supprDocument') !== null){
        document.querySelectorAll('.supprDocument').forEach(function(link){
            link.addEventListener('click', supprDocument);
        });
    }
    if(document.querySelector('.btnDocumentPdfError') !== null){
        document.querySelectorAll('.btnDocumentPdfError').forEach(function(link){
            link.addEventListener('click', errorDocument);
        });
    }
    if(btnDocumentPdfError !== null){btnDocumentPdfError.addEventListener('click', errorDocument);}
    if(document.querySelector('#rowHonorairesPdf #btnHonorairePdf') !== null){document.querySelector('#rowHonorairesPdf #btnHonorairePdf').addEventListener('click', submitHonoraires);}
}

allAddEvent();
//tomSelect('#selectCustomer');

