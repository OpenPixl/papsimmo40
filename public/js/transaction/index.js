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

const modalCustomer = document.getElementById('modalCustomer');
let btnSubmitCustomer = document.getElementById('btnSubmitCustomer');
let btnAddDatePromise = document.getElementById('btnAddDatePromise');
let btnAddPromisePdf = document.getElementById('btnAddPromisePdf');
let btnAddPromisePdfbyColl = document.getElementById('btnAddPromisePdfbyColl');
let btnAddPromisePdfControl = document.getElementById('btnAddPromisePdfControl');
let btnAddDateActe = document.getElementById('btnAddDateActe');
let btnAddActePdf = document.getElementById('btnAddActePdf');
let btnAddActePdfbyColl = document.getElementById('btnAddActePdfbyColl');
let btnAddActePdfControl = document.getElementById('btnAddActePdfControl');
let btnAddTracfinPdf = document.getElementById('btnAddTracfinPdf');
let btnAddTracfinPdfbyColl = document.getElementById('btnAddTracfinPdfbyColl');
let btnAddTracfinPdfControl = document.getElementById('btnAddTracfinPdfControl');

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
                let commune2 = document.getElementById('customer2_city');
                let zipcode2 = document.getElementById('customer2_zipcode');
                let SelectCity2 = document.getElementById('selectcity2');
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
                let commune2 = document.getElementById('customer2_city');
                let zipcode2 = document.getElementById('customer2_zipcode');
                let SelectCity2 = document.getElementById('selectcity2');
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
            });
    }
});

function submitCustomer(event){
    event.preventDefault;
    let form = document.getElementById('FormEditCustomer');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            if(response.data.type === 1){
                document.getElementById('blockSailers').innerHTML = response.data.liste;
            }else if(response.data.type === 2){
                document.getElementById('blockBuyers').innerHTML = response.data.liste;
            }
            document.getElementById('transactionstep2_dateAtPromise').classList.remove('d-none');
            document.getElementById('btnAddDatePromise').classList.remove('d-none');
            document.getElementById('rowEmptyPromiseDate').remove();
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitDatePromise(event){
    event.preventDefault();
    let form = document.getElementById('addDatePromiseForm');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('transactionstep3_promisePdfFilename').classList.remove('d-none');
            document.getElementById('btnAddPromisePdf').classList.remove('d-none');
            document.getElementById('rowEmptyPromisePdf').remove();
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
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('transaction_actedate_dateAtSale').classList.remove('d-none');
            document.getElementById('btnAddDateActe').classList.remove('d-none');
            document.getElementById('rowEmptyDateActe').remove();
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
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('rowPromisePdf').innerHTML = "" +
                "<tr class=\"align-middle\" id=\"rowPromisePdf\">" +
                "    <td class=\"table-light\">Compromis de vente :</td>" +
                "    <td><p class=\"alert alert-warning mb-0 p-1\"><i class=\"fa-duotone fa-hourglass-start\"></i>  Le document doit être vérifier par votre administrateur.</p></td>" +
                "</tr>";

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
            document.getElementById('transaction_actedate_dateAtSale').classList.remove('d-none');
            document.getElementById('btnAddDateActe').classList.remove('d-none');
            document.getElementById('rowEmptyDateActe').remove();
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitDateActe(event){
    event.preventDefault();
    let form = document.getElementById('addDateActeForm');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('transaction_actepdf_actePdfFilename').classList.remove('d-none');
            document.getElementById('btnAddActePdf').classList.remove('d-none');
            document.getElementById('rowEmptyActePdf').remove();
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
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('transaction_tracfinpdf_tracfinPdfFilename').classList.remove('d-none');
            document.getElementById('btnAddTracfinPdf').classList.remove('d-none');
            document.getElementById('rowEmptyTracfinPdf').remove();
        })
        .catch(function (error) {
            console.log(error);
        });
}

function submitActePdfbyColl(){
    let form = document.getElementById('transactionstep3');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('rowActePdf').innerHTML = "" +
                "<tr class=\"align-middle\" id=\"rowPromisePdf\">" +
                "    <td class=\"table-light\">Acte :</td>" +
                "    <td><p class=\"alert alert-warning mb-0 p-1\"><i class=\"fa-duotone fa-hourglass-start\"></i>  Le document doit être vérifier par votre administrateur.</p></td>" +
                "</tr>";

        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitActePdfControl(){
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('transaction_actedate_dateAtSale').classList.remove('d-none');
            document.getElementById('btnAddDateActe').classList.remove('d-none');
            document.getElementById('rowEmptyDateActe').remove();
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitTracfinPdf(event){
    let form = document.getElementById('transactiontracfinpdf');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
        })
        .catch(function (error) {
            console.log(error);
        });
}
function submitTracfinPdfbyColl(){
    let form = document.getElementById('transactionstep3');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('rowtracfinPdf').innerHTML = "" +
                "<tr class=\"align-middle\" id=\"rowPromisePdf\">" +
                "    <td class=\"table-light\">tracfin :</td>" +
                "    <td><p class=\"alert alert-warning mb-0 p-1\"><i class=\"fa-duotone fa-hourglass-start\"></i>  Le document doit être vérifier par votre administrateur.</p></td>" +
                "</tr>";

        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

function submitTracfinPdfControl(){
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('transaction_actedate_dateAtSale').classList.remove('d-none');
            document.getElementById('btnAddDateActe').classList.remove('d-none');
            document.getElementById('rowEmptyDateActe').remove();
        })
        .catch(function (error) {
            console.log(error);
        })
    ;
}

btnSubmitCustomer.addEventListener('click', submitCustomer);
// Promise
if(btnAddDatePromise !== null){btnAddDatePromise.addEventListener('click', submitDatePromise);}
if(btnAddPromisePdf !== null){btnAddPromisePdf.addEventListener('click', submitPromisePdf);}
if(btnAddPromisePdfbyColl !== null){btnAddPromisePdfbyColl.addEventListener('click', submitPromisePdfbyColl);}
if(btnAddPromisePdfControl !== null){btnAddPromisePdfControl.addEventListener('click', submitPromisePdfControl);}
// Acte
if(btnAddDateActe !== null){btnAddDateActe.addEventListener('click', submitDateActe);}
if(btnAddActePdf !== null){btnAddActePdf.addEventListener('click', submitActePdf);}
if(btnAddActePdfbyColl !== null){btnAddActePdfbyColl.addEventListener('click', submitActePdfbyColl);}
if(btnAddActePdfControl !== null){btnAddActePdfControl.addEventListener('click', submitActePdfControl);}
// Tracfin
if(btnAddTracfinPdf !== null){btnAddTracfinPdf.addEventListener('click', submitTracfinPdf);}
if(btnAddTracfinPdfbyColl !== null){btnAddTracfinPdfbyColl.addEventListener('click', submitTracfinPdfbyColl);}
if(btnAddTracfinPdfControl !== null){btnAddTracfinPdfControl.addEventListener('click', submitTracfinPdfControl);}
