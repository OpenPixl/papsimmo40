export function submitCustomer(event) {
    event.preventDefault;
    let formCustomer = document.getElementById('FormEditCustomer');
    let action = formCustomer.action;
    let data = new FormData(formCustomer);
    axios
        .post(action, data)
        .then(function(response) {
            document.getElementById('ListTransactCustomers').innerHTML = response.data.liste;
            btnSubmitCustomer.addEventListener('click', submitCustomer);
        });
}

export function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
        selectElement.remove(i);
    }
}

export function selectCommunes(event){
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
}