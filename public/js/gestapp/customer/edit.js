const typeClient = document.getElementById('customer_typeClient');
const btnAddCustomer = document.getElementById('btnAddCustomer');
const btnAddResp = document.getElementById('btnAddResp');

let civiCustomer = document.querySelector('input[name=customer\\[civility\\]]:checked').value;
if (civiCustomer > 1){
    document.getElementById('customer_maidenName').classList.remove('d-none');
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

let civiResponsable = document.querySelector('input[name=customer_resp\\[civility\\]]:checked').value;
if (civiResponsable > 1){
    document.getElementById('customer_maidenName').classList.remove('d-none');
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

// PARTIE Code postal et Ville - API
// ---------------------------------------
let commune = document.getElementById('customer_city');
let zipcode = document.getElementById('customer_zipcode');
let SelectCity = document.getElementById('selectcity');
let addresseInput = document.getElementById('customer_adress');

zipcode.addEventListener('input', function(event){
    if(zipcode.value.length === 5)
    {
        let coord = this.value;
        axios
            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
            .then(function(response){
                let features = response.data;
                removeOptions(SelectCity);
                features.forEach((element) => {
                    let name = element['codePostal']+" - "+element['nomCommune'];
                    let OptSelectCity = new Option (name.toUpperCase(), name.toUpperCase(), false, true);
                    SelectCity.options.add(OptSelectCity);
                });
                if (SelectCity.options.length === 1){
                    let value = SelectCity.value.split(' ');
                    zipcode.value = value[0];
                    commune.value = value[2].toUpperCase();
                }else{
                    let value = SelectCity.value.split(' ');
                    zipcode.value = value[0];
                    commune.value = value[2].toUpperCase();
                }
            });
    }
});
SelectCity.addEventListener('change', function (event){
    let value = this.value.split(' ');
    console.log(value);
    zipcode.value = value[0];
    commune.value = value[2].toUpperCase();
});

if(typeClient.value === "professionnel"){
    document.getElementById("box_professionnel").classList.remove('d-none');
    document.getElementById("box_particulier").classList.add('d-none');
}

typeClient.addEventListener('change', function(event){
    if(typeClient.value === "professionnel"){
        document.getElementById("box_professionnel").classList.remove('d-none');
        document.getElementById("box_professionnel").classList.add('animate__animated', 'animate__fadeIn');
        document.getElementById('kbis').classList.remove('d-none');
        document.getElementById('kbis').classList.add('animate__animated', 'animate__fadeIn');
    }else{
        document.getElementById("box_professionnel").classList.add('d-none');
        document.getElementById('kbis').classList.add('d-none');
        document.getElementById("box_professionnel").classList.remove('animate__animated', 'animate__fadeIn');
        document.getElementById('kbis').classList.remove('animate__animated', 'animate__fadeIn');
    }
    if(typeClient.value === "particulier"){
        document.getElementById("box_particulier").classList.remove('d-none');
        document.getElementById('kbis').classList.remove('d-none');
        document.getElementById("box_particulier").classList.add('animate__animated', 'animate__fadeIn');
        document.getElementById('kbis').classList.add('animate__animated', 'animate__fadeIn');
    }else{
        document.getElementById("box_particulier").classList.add('d-none');
        document.getElementById('kbis').classList.add('d-none');
        document.getElementById("box_particulier").classList.remove('animate__animated', 'animate__fadeIn');
        document.getElementById('kbis').classList.remove('animate__animated', 'animate__fadeIn');
    }
});
btnAddResp.addEventListener('click', function(event){
    event.preventDefault();
    let civility = document.getElementById('').value;
    let form = document.getElementById('AddRespStructure');
    let action = form.action;
    let data = new FormData(form);
    console.log(data);
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
}
