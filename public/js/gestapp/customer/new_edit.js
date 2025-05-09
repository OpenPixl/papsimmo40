const typeClient = document.getElementById('customer_typeClient');
const btnAddCustomer = document.getElementById('btnAddCustomer');
const btnAddResp = document.getElementById('btnAddResp');

const customer_commune = document.getElementById('customer_city');
const customer_zipcode = document.getElementById('customer_zipcode');
const customer_SelectCity = document.getElementById('customer_selectcity');
const customer_addresseInput = document.getElementById('customer_adress');
const customer_proCity = document.getElementById('customer_proCity');
const customer_proZipcode = document.getElementById('customer_proZipcode');
const customer_proSelectcity = document.getElementById('customer_proSelectcity');

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

function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}

// PARTIE Code postal et Ville - API customer
// ---------------------------------------
customer_zipcode.addEventListener('input', function(event){
    zipcode_api(customer_zipcode, customer_commune, customer_SelectCity);
});
customer_SelectCity.addEventListener('change', function (event){
    change_selectcity(customer_SelectCity);
});
// PARTIE Code postal et Ville - API customerPro
// ---------------------------------------
customer_proZipcode.addEventListener('input', function(event){
    zipcode_api(customer_proZipcode, customer_proCity, customer_proSelectcity);
});
customer_SelectCity.addEventListener('change', function (event){
    change_selectcity(customer_SelectCity);
});

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

function submitCustomer(event){
    event.preventDefault();
    let form = document.getElementById('FormEditCustomer');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            reloadEvent();
            toasterMessage(response.data.message);
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function addResponsable(event){
    event.preventDefault();
    let form = document.getElementById('AddRespStructure');
    let action = form.action;
    let data = new FormData(form);
    console.log(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('liste_respcustomer').innerHTML = response.data.listeResp;
            toasterMessage(response.data.message);
            form.reset();
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function dellResponsable(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('liste_respcustomer').innerHTML = response.data.listeResp;
            toasterMessage(response.data.message);
            console.log(response.data);
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function zipcode_api(zipcode, commune, select_city){
    if(zipcode.value.length === 5)
    {
        let coord = zipcode.value;
        axios
            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
            .then(function(response){
                let features = response.data;
                removeOptions(select_city);
                features.forEach((element) => {
                    let name = element['codePostal']+" - "+element['nomCommune'];
                    let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                    select_city.options.add(OptSelectCity);
                });
                if (select_city.options.length === 1){
                    let value = select_city.value.split(' ');
                    zipcode.value = value[0];
                    commune.value = value[2].toUpperCase();
                }else{
                    let value = select_city.value.split(' ');
                    zipcode.value = value[0];
                    commune.value = value[2].toUpperCase();
                }
            });
    }
}

function change_selectcity(selectCity){
    let value = selectCity.value.split(' ');
    zipcode.value = value[0];
    commune.value = value[2].toUpperCase();
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

function reloadEvent(){
    btnAddCustomer.addEventListener('click', submitCustomer);
    btnAddResp.addEventListener('click', addResponsable);
    let btnSupprResps = document.querySelectorAll('.btnSupprResp');
    btnSupprResps.forEach(function(click){
        click.addEventListener('click', dellResponsable);
    });
}

reloadEvent();
