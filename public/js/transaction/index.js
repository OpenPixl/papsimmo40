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

function submitCustomer(event){
    event.preventDefault;
    let form = document.getElementById('FormEditCustomer');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('buyers').innerHTML = response.data.liste;
            btnSubmitCustomer.addEventListener('click', submitCustomer);
        });
}

btnSubmitCustomer.addEventListener('click', submitCustomer);