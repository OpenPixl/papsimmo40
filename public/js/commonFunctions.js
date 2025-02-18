export function toasterMessage(message){
    // préparation du toaster
    let option = {animation: true,autohide: true,delay: 3000,};
    // initialisation du toaster
    let toastHTMLElement = document.getElementById("toaster");
    let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
    toastBody.textContent = message;
    let toastElement = new bootstrap.Toast(toastHTMLElement, option);
    toastElement.show();
}

function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}
// Fonction pour trouver les communes à partir du code postal
export function findCommunes(City, Zipcode, Select) {
    if (Zipcode.value.length === 5) {
        let coord = Zipcode.value;
        let xhr = new XMLHttpRequest();

        xhr.open('GET', 'https://apicarto.ign.fr/api/codes-postaux/communes/' + coord, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    let features = JSON.parse(xhr.responseText);
                    removeOptions(Select);

                    features.forEach((element) => {
                        let name = element['codePostal'] + " - " + element['nomCommune'];
                        let OptSelect = new Option(name.toUpperCase(), name.toUpperCase(), false, true);
                        Select.options.add(OptSelect);
                    });

                    if (Select.options.length === 1) {
                        let value = Select.value.split(' ');
                        Zipcode.value = value[0];
                        City.value = value[2].toUpperCase();
                    } else {
                        let value = Select.value.split(' ');
                        Zipcode.value = value[0];
                        City.value = value[2].toUpperCase();
                    }
                } else {
                    console.error('Erreur lors de la requête AJAX :', xhr.statusText);
                }
            }
        };

        xhr.send();
    }
}

export function validate_mandat(input, array, error_message){
    let newmandat = parseInt(input.value);
    let flag = 0;
    for(let i=0; i<array.length; i++) {
        if(newmandat === array[i]) {
            flag = 1;
        }
    }
    if(flag === 1){
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        error_message.innerHTML = 'Corrigez ce numéro, il est présent dans la liste des biens <b>Paps immo</b>.';

    }else{
        input.classList.remove("is-invalid");
        input.classList.add("is-valid");
        error_message.textContent = "Numéro de mandat valide.";
    }
}