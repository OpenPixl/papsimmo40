// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickr", {
    "locale": "fr",
    enableTime: false,
    allowInput: true,
    altFormat: "j F Y",
    dateFormat: "d/m/Y",
});
// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickrtime", {
    "locale": "fr",
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

let modalSupport = document.getElementById('modalSupport');
let btnSubmitSupport = document.getElementById('btnSubmitSupport');
let btnConfPurchase = document.getElementById('btnConfPurchase');
let btnSupprProduct = document.getElementById('btnSupprProduct');

modalSupport.addEventListener('show.bs.modal', openModalSupport);
if(btnSubmitSupport !== null){
    btnSubmitSupport.addEventListener('click', submitSupport);
}

function submitSupport(){
    let form = document.querySelector('.modal-body form');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('listeSupport').innerHTML = response.data.liste;
            modalSupport.querySelector('.modal-body').innerHTML =
                "<div class=\"d-flex justify-content-center\">\n" +
                "<div class=\"spinner-border text-primary\" role=\"status\">\n" +
                "<span class=\"visually-hidden\">Loading...</span>\n" +
                "</div>\n" +
                "</div>";
            allAddEvent();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function openModalSupport(event){
    // Button that triggered the modal
    let a = event.relatedTarget;
    // extraction de.s variable.s
    let recipient = a.getAttribute('data-bs-crud');
    let crud = recipient.split('-')[0];
    let contentTitle = recipient.split('-')[1];
    let id = recipient.split('-')[2];
    let namePage = recipient.split('-')[3];
    if(crud === 'ADD'){
        let url = a.href;
        let modalHeaderH5 = modalSupport.querySelector('.modal-title');
        let modalBody = modalSupport.querySelector('.modal-body');
        modalHeaderH5.textContent = contentTitle;
        axios
            .get(url)
            .then(function (response){
                modalBody.innerHTML = response.data.formView;
                event.preventDefault();
                allAddEvent();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }else if(crud === 'EDIT'){
        let url = a.href;
        let modalHeaderH5 = modalSupport.querySelector('.modal-title');
        let modalBody = modalSupport.querySelector('.modal-body');
        modalHeaderH5.textContent = contentTitle;
        axios
            .get(url)
            .then(function (response){
                modalBody.innerHTML = response.data.formView;
                event.preventDefault();
                allAddEvent();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }else if(crud === 'ADD_CAT'){
        let url = a.href;
        let modalHeaderH5 = modalSupport.querySelector('.modal-title');
        let modalBody = modalSupport.querySelector('.modal-body');
        modalHeaderH5.textContent = contentTitle;
        axios
            .get(url)
            .then(function (response){
                modalBody.innerHTML = response.data.formView;
                allAddEvent();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }else if(crud === 'SHOW'){
        let url = a.href;
        let modalHeaderH5 = modalSupport.querySelector('.modal-title');
        let modalBody = modalSupport.querySelector('.modal-body');
        modalHeaderH5.textContent = contentTitle;
        modalSupport.querySelector('.modal-footer button.submit').id = "AddCart";
        axios
            .get(url)
            .then(function (response){
                modalBody.innerHTML = response.data.showItem;
                document.getElementById('btnSupprProduct').addEventListener('click', supprProduct);
                allAddEvent();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }
}

function supprProduct(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('listeSupport').innerHTML = response.data.liste;
            modalSupport.querySelector('.modal-body').innerHTML =
                "<div class=\"d-flex justify-content-center\">\n" +
                "<div class=\"spinner-border text-primary\" role=\"status\">\n" +
                "<span class=\"visually-hidden\">Loading...</span>\n" +
                "</div>\n" +
                "</div>";
            allAddEvent();
        })
        .catch();
}

// Fonction de rechargement des events
function allAddEvent(){
    modalSupport.addEventListener('show.bs.modal', openModalSupport);
    if(btnSubmitSupport !== null){
        btnSubmitSupport.addEventListener('click', submitSupport);
    }
}