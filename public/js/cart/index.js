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

modalSupport.addEventListener('show.bs.modal', openModalSupport);

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
                allAddEvent();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }
}

// Fonction de rechargement des events
function allAddEvent(){
    modalSupport.addEventListener('show.bs.modal', openModalSupport);
}