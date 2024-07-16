const modalCustomer = document.getElementById('modalCustomer');
const modalDelCustomer = document.getElementById('modalDelCustomer');
const modalAddcollaborateur = document.getElementById('modalAddColl');

let btnSubmitCustomer = document.getElementById('btnSubmitCustomer');
let btnDelCustommer = document.getElementById('btnDellCustomer');

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
if(zipcode2 !== null) {
    zipcode2.addEventListener('input', zipcodeGen);
    SelectCity2.addEventListener('change', function (event){
        let value = this.value.split(' ');
        zipcode2.value = value[0];
        commune2.value = value[2].toUpperCase();
    });
}

function zipcodeGen(event){
    if (zipcode2.value.length === 5) {
        let coord = this.value;
        axios
            .get('https://apicarto.ign.fr/api/codes-postaux/communes/' + coord)
            .then(function (response) {
                let features = response.data;
                removeOptions(SelectCity2);
                features.forEach((element) => {
                    let name = element.codePostal + " - " + element.nomCommune;
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
    if(crud === "ADD"){
        let modalHeaderH5 = modalCustomer.querySelector('.modal-title');
        let modalBody = modalCustomer.querySelector('.modal-body');
        modalHeaderH5.textContent = contentTitle;
        let url = button.href;
        axios
            .get(url)
            .then(function(response){
                modalBody.innerHTML = response.data.formView;
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
                let typeClient = modalCustomer.querySelector('.modal-body #customer2_typeClient');
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
                let commune2 = document.getElementById('customer2_city');
                let zipcode2 = document.getElementById('customer2_zipcode');
                let SelectCity2 = document.getElementById('selectcity');
                zipcode2.addEventListener('input', function (event) {
                    if (zipcode2.value.length === 5) {
                        let coord = this.value;
                        axios
                            .get('https://apicarto.ign.fr/api/codes-postaux/communes/' + coord)
                            .then(function (response) {
                                let features = response.data;
                                removeOptions(SelectCity2);
                                features.forEach((element) => {
                                    let name = element.codePostal + " - " + element.nomCommune;
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
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "EDIT"){
        let url = button.href;
        let modalHeaderH5 = modalCustomer.querySelector('.modal-title');
        let modalBody = modalCustomer.querySelector('.modal-body');
        modalHeaderH5.textContent = contentTitle;
        axios
            .get(url)
            .then(function(response){
                modalBody.innerHTML = response.data.formView;
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
                let typeClient = modalCustomer.querySelector('.modal-body #customer2_typeClient');
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
                let commune2 = document.getElementById('customer2_city');
                let zipcode2 = document.getElementById('customer2_zipcode');
                let SelectCity2 = document.getElementById('selectcity');
                zipcode2.addEventListener('input', function (event) {
                    if (zipcode2.value.length === 5) {
                        let coord = this.value;
                        axios
                            .get('https://apicarto.ign.fr/api/codes-postaux/communes/' + coord)
                            .then(function (response) {
                                let features = response.data;
                                removeOptions(SelectCity2);
                                features.forEach((element) => {
                                    let name = element.codePostal + " - " + element.nomCommune;
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
            });
    }
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
    let id = recipient.split('-')[2];
    if(crud === "ADD"){
        let modalHeaderH5 = modalAddcollaborateur.querySelector('.modal-title');
        let modalBody = modalAddcollaborateur.querySelector('.modal-body');
        let submitFooter = modalAddcollaborateur.querySelector('.modal-footer #btnSubmitColl');
        modalHeaderH5.textContent = contentTitle;
        submitFooter.textContent = "Ajouter au projet";
        submitFooter.href = url;
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
    }
});

if(document.querySelector(".supprCollInv") !== null){
    document.querySelector(".supprCollInv").addEventListener('click', supprInvoiceColl);
}

btnDelCustommer.addEventListener('click', dellCustomer);

function submitCustomer(event){
    event.preventDefault;
    let form = document.getElementById('FormEditCustomer');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            console.log(response.data);
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
    }else{
        let form = document.getElementById('FormAddcollaborator');
        let data = new FormData(form);
        let action = form.action;
        axios
            .post(action, data)
            .then(function(response){
                document.getElementById('listCollaborator').innerHTML = response.data.listCollaborator;
                document.getElementById('rowInvoicesPdf').innerHTML = response.data.row;
            })
            .catch(function (error){
                console.log(error);
            })
        ;
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



