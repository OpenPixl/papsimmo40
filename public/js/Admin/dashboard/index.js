const btnSearchProperty = document.getElementsByClassName('submitFormSearch');
const resultPropertySearch = document.getElementById('resultPropertySearch');

function launchSearch(event){
    event.preventDefault;
    let form = document.getElementById('SearchFormProperty');
    let action = form.action;
    let data = new FormData(form);

    axios
        .post(action,data)
        .then(function (response) {
            document.getElementById('resultPropertySearch').innerHTML = response.data.list;
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        });
}

function findPaginatedSearchProperty(event){
    event.preventDefault();
    let form = document.getElementById('SearchFormProperty');
    let url = this.href;
    let data = new FormData(form);
    axios
        .post(url,data)
        .then(function (response) {
            document.getElementById('resultPropertySearch').innerHTML = response.data.list;
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        });
}

function reloadEvent(){
    document.getElementById('submitFormSearch').addEventListener('click', launchSearch);
    if(resultPropertySearch !== null){
        resultPropertySearch.querySelectorAll('.page-link').forEach((link) => {
            link.addEventListener('click', findPaginatedSearchProperty);
        });
    }
}

reloadEvent();
