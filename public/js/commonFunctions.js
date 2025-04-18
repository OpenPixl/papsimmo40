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
export function zipcode_api(zipcode, commune, select_city, ville, cp) {
    if (Zipcode.value.length === 5) {
        let coord = zipcode.value;
        axios
            .get('https://apicarto.ign.fr/api/codes-postaux/communes/'+ coord)
            .then(function(response){
                let features = response.data;
                removeOptions(select_city);
                features.forEach((element) => {
                    cp = element['codePostal'];
                    ville = element['nomCommune'];
                    console.log(cp, ville);
                    let OptSelectCity = new Option (ville.toUpperCase()+" ("+cp+")", ville.toUpperCase(), false, true);
                    select_city.options.add(OptSelectCity);
                });

                if (select_city.options.length === 1){
                    zipcode.value = cp;
                    commune.value = ville.toUpperCase();
                }else{
                    zipcode.value = cp;
                    commune.value = ville.toUpperCase();
                }
            })
        ;
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