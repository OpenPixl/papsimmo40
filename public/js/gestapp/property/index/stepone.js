import {validate_mandat} from '../../../commonFunctions';
const ulPagination = document.getElementsByClassName('pagination');

function SubmitFormSearch(event){
    event.preventDefault();
    let form = document.getElementById('SearchFormProperty');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('list').innerHTML = response.data.list;
            reloadEvent();
        })
        .catch(function(error){
            console.log();
        })
    ;
}

function paginatorPage(event, link){
    event.preventDefault();
    let fullUrl = this.href;  // URL complète récupérée
    let newUrl = new URL(fullUrl);
    let url = newUrl.pathname;  // "/gestapp/property"

    if ( url === '/admin/search/property/'){
        let form = document.getElementById('SearchFormProperty');
        let url = this.href;
        let data = new FormData(form);
        axios
            .post(fullUrl, data)
            .then(function(response){
                document.getElementById('list').innerHTML = response.data.list;
                reloadEvent();
            })
            .catch(function(error){
                console.log();
            })
        ;
    }else if(url === '/gestapp/property/'){
        axios
            .get(fullUrl)
            .then(function(response){
                document.getElementById('list').innerHTML = response.data.list;
                reloadEvent();
            })
            .catch(function(error){
                console.log();
            });
    }
}

function reloadEvent(){
    document.querySelectorAll('button.submitFormSearch').forEach(function(button){
        button.addEventListener('click', SubmitFormSearch);
    });

    if(ulPagination !== null){
        document.querySelectorAll('ul.pagination li.page-item a').forEach(function(link){
            link.addEventListener('click', paginatorPage);
        });
    }
}

reloadEvent();
