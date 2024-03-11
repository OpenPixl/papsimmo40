// ---------------------------------------------------------
// Mise en place du code pour archiver un bien
// si une date de fin de mandat est donné par le client
// ---------------------------------------------------------

const dateEndMandat = document.getElementById('dateEndMandat');
const modalDateEndMandat = document.getElementById('modalDateEndMandat');
const btnModalDateEndMandat = document.getElementById('btnModalDateEndMandat');
const submitDateEndMandat = document.getElementById('submitDateEndMandat');
const disDateEndMandat = document.getElementById('btnDisDateEndMandat');

modalDateEndMandat.addEventListener('show.bs.modal', function (event) {
    let button = event.relatedTarget;
    let recipient = button.getAttribute('data-bs-whatever');
    axios
        .get('/gestapp/property/add_dateendmandat/' + recipient)
        .then(function(response){
            document.getElementById('modalBodyDateEndMandat').innerHTML = response.data.form.content;
        })
    ;
});

// Mise en plae d'une fin de mandat
submitDateEndMandat.addEventListener('click', function(event){
   event.preventDefault();
   const form = modalDateEndMandat.querySelector('.modal-body form');
   let url = form.action;
   let data = new FormData(form);
   axios
       .post(url, data)
       .then(function (response) {
          window.location.reload();
       })
   ;
});

// Annule la procédure de mise du bien hors mandat
if(disDateEndMandat){
    disDateEndMandat.addEventListener('click', function(event){
        event.preventDefault();
        let recipient = this.getAttribute('data-bs-whatever');
        let url = '/gestapp/property/dis_dateendmandat/' + recipient;
        axios
            .get(url)
            .then(function(response){
                const liste = document.getElementById('list').innerHTML = response.data.liste;
                // initialisation du toaster
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
                toastBody.innerHTML = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000,});
                toastElement.show();
            })
        ;
    });
}
