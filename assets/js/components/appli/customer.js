export function typeClient(){
    const typeClient = document.getElementById('customer_typeClient');
    if (typeClient.value === "professionnel") {
        document.getElementById("box_professionnel").classList.remove('d-none');
        document.getElementById("box_particulier").classList.add('d-none');
    }

    typeClient.addEventListener('change', function (event) {
        if (typeClient.value === "professionnel") {
            document.getElementById("box_professionnel").classList.remove('d-none');
            document.getElementById("box_professionnel").classList.add('animate__animated', 'animate__fadeIn');
        } else {
            document.getElementById("box_professionnel").classList.add('d-none');
            document.getElementById("box_professionnel").classList.remove('animate__animated', 'animate__fadeIn');
        }
        if (typeClient.value === "particulier" || typeClient.value === "dirigeant") {
            document.getElementById("box_particulier").classList.remove('d-none');
            document.getElementById("box_particulier").classList.add('animate__animated', 'animate__fadeIn');
        } else {
            document.getElementById("box_particulier").classList.add('d-none');
            document.getElementById("box_particulier").classList.remove('animate__animated', 'animate__fadeIn');
        }
    });
}

export function civilityChoice(){
    // block pour interagir sur la civilité
    if (document.querySelector('input[name=customer\\[civility\\]]:checked').value === '2') {
        document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
    }
    const radioCustomerButtons = document.querySelectorAll('input[name=customer\\[civility\\]]');
    radioCustomerButtons.forEach(function (radio) {
        radio.addEventListener("change", function () {
            if (parseInt(this.value) === 2) {
                document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
            } else if (parseInt(this.value) === 1 || parseInt(this.value) === 3) {
                document.getElementById('customer_maidenName').parentElement.classList.add('d-none');
            }
        });
    });
    if (document.querySelector('input[name=customer_resp\\[civility\\]]:checked').value === '2') {
        document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
    }
    const radioRespButtons = document.querySelectorAll('input[name=customer_resp\\[civility\\]]');
    radioRespButtons.forEach(function (radio) {
        radio.addEventListener("change", function () {
            if (parseInt(this.value) === 2) {
                document.getElementById('customer_resp_maidenName').parentElement.classList.remove('d-none');
            } else if (parseInt(this.value) === 1) {
                document.getElementById('customer_resp_maidenName').parentElement.classList.add('d-none');
            }
        });
    });
}