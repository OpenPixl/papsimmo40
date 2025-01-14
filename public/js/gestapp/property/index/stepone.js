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
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('list').innerHTML = response.data.list;
            reloadEvent();
        })
        .catch(function(error){
            console.log();
        });
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
