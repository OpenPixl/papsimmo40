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

tinymce.init({
    selector: '.tinymce',
    plugins: 'image table lists',
    toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image table',
    images_file_types: 'jpg,svg,webp',
    language: 'fr_FR',
    language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
});

let modalSupport = document.getElementById('modalSupport');
let btnSubmitSupport = document.getElementById('btnSubmitSupport');
let btnConfPurchase = document.getElementById('btnConfPurchase');
let btnSupprProduct = document.getElementById('btnSupprProduct');


modalSupport.addEventListener('show.bs.modal', openModalSupport);
if(btnSubmitSupport !== null){
    btnSubmitSupport.addEventListener('click', submitSupport);
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
        let modalDialog = modalSupport.querySelector('.modal-dialog');
        modalHeaderH5.textContent = contentTitle;
        modalDialog.classList.remove("modal-lg");
        modalDialog.classList.add("modal-xl");
        axios
            .get(url)
            .then(function (response){
                modalBody.innerHTML = response.data.formView;
                tinymce.init({
                    selector: '.tinymce',
                    plugins: 'image table lists',
                    toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image table',
                    images_file_types: 'jpg,svg,webp',
                    language: 'fr_FR',
                    language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
                });
                event.preventDefault();
                allAddEvent();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }else if(crud === 'EDIT'){
        tinymce.init({
            selector: '.tinymce',
            plugins: 'image table lists',
            toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image table',
            images_file_types: 'jpg,svg,webp',
            language: 'fr_FR',
            language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
        });
        let url = a.href;
        let modalHeaderH5 = modalSupport.querySelector('.modal-title');
        let modalBody = modalSupport.querySelector('.modal-body');
        let modalDialog = modalSupport.querySelector('.modal-dialog');
        modalSupport.querySelector('.modal-footer button.submit').textContent = "Modifier";
        modalHeaderH5.textContent = contentTitle;
        modalDialog.classList.remove("modal-lg");
        modalDialog.classList.add("modal-xl");
        axios
            .get(url)
            .then(function (response){
                modalBody.innerHTML = response.data.formView;
                tinymce.init({
                    selector: '.tinymce',
                    plugins: 'image table lists',
                    toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image table',
                    images_file_types: 'jpg,svg,webp',
                    language: 'fr_FR',
                    language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
                });
                modalSupport.querySelector('.modal-footer button.submit').addEventListener("click", submitSupport);
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
        modalSupport.querySelector('.modal-footer button.submit').removeEventListener("click", submitSupport);
        axios
            .get(url)
            .then(function (response){
                modalBody.innerHTML = response.data.showItem;
                allAddEvent();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
        axios
            .get('/cart/product/modalfooter/'+ id )
            .then(function(response){
                modalSupport.querySelector('.modal-footer').innerHTML = response.data.footer;
                if(btnSupprProduct !== null){
                    document.getElementById('btnSupprProduct').addEventListener('click', supprProduct);

                }
                let btnIncrement = document.querySelector('.modal-footer a.js-increment');
                let btnDecrement = document.querySelector('.modal-footer a.js-decrement');
                btnIncrement.addEventListener('click', incrementCart);
                if(btnDecrement !== null){
                    btnDecrement.addEventListener('click', decrementCart);
                }
            })
        ;
    }
}
modalSupport.addEventListener('hide.bs.modal', event => {
    let modalDialog = modalSupport.querySelector('.modal-dialog');
    modalDialog.classList.remove("modal-xl");
    modalDialog.classList.add("modal-lg");
    modalSupport.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">" +
        "<div class=\"spinner-border text-primary\" role=\"status\">"+
        "<span class=\"visually-hidden\">Loading...</span>"+
        "</div>"+
        "</div>";
    modalSupport.querySelector('.modal-footer').innerHTML =
        "<button type=\"button\" class=\"btn btn-sm btn-secondary\" data-bs-dismiss=\"modal\">Annuler</button>\n" +
        "<button id=\"btnSubmitSupport\" type=\"submit\" class=\"btn btn-sm btn-primary submit\" data-bs-dismiss=\"modal\">Ajouter</button>";
});

function submitSupport() {
    let form = document.querySelector('.modal-body form');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function (response) {
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

function incrementCart(event){
    event.preventDefault();
    let url = this.href;
    axios
        .get(url)
        .then(function(response){
            modalSupport.querySelector('.modal-footer').innerHTML = response.data.footer;
            if(btnSupprProduct !== null){
                document.getElementById('btnSupprProduct').addEventListener('click', supprProduct);

            }
            let btnIncrement = document.querySelector('.modal-footer a.js-increment');
            let btnDecrement = document.querySelector('.modal-footer a.js-decrement');
            btnIncrement.addEventListener('click', incrementCart);
            if(btnDecrement !== null){
                btnDecrement.addEventListener('click', decrementCart);
            }
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}
function decrementCart(event){
    event.preventDefault();
    let url = this.href;
    axios
        .get(url)
        .then(function(response){
            modalSupport.querySelector('.modal-footer').innerHTML = response.data.footer;
            if(btnSupprProduct !== null){
                document.getElementById('btnSupprProduct').addEventListener('click', supprProduct);

            }
            let btnIncrement = document.querySelector('.modal-footer a.js-increment');
            let btnDecrement = document.querySelector('.modal-footer a.js-decrement');
            btnIncrement.addEventListener('click', incrementCart);
            if(btnDecrement !== null){
                btnDecrement.addEventListener('click', decrementCart);
            }
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function addCart(event){
    event.preventDefault();
}

// Fonction de rechargement des events
function allAddEvent(){
    modalSupport.addEventListener('show.bs.modal', openModalSupport);
    if(btnSubmitSupport !== null){
        btnSubmitSupport.addEventListener('click', submitSupport);
    }
}