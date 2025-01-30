const btnSearchProperty = document.getElementsByClassName('submitFormSearch');

function launchSearch(event){
    event.preventDefault;
    let form = document.getElementById('SearchFormProperty');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action,data)
        .then(function (response) {
            document.getElementById('resultsSearch').innerHTML = response.data.list;
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        });
}

function reloadEvent(){
    document.getElementById('submitFormSearch').addEventListener('click', launchSearch);
}

reloadEvent();
