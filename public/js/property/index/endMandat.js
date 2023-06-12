// ---------------------------------------------------------
// Mise en place du code pour archiver un bien
// si une date de fin de mandat est donné par le client
// ---------------------------------------------------------

const dateEndMandat = document.getElementById('dateEndMandat');
const modalDateEndMandat = document.getElementById('modalDateEndMandat');
const btnModalDateEndMandat = document.getElementById('btnModalDateEndMandat');
const submitDateEndMandat = document.getElementById('submitDateEndMandat');

const form = modalDateEndMandat.querySelector('.modal-body form');

modalDateEndMandat.addEventListener('show.bs.modal', function (event) {
    let button = event.relatedTarget;
    let recipient = button.getAttribute('data-bs-whatever');

    form.action = '/gestapp/property/add_dateendmandat/' + recipient;
});

submitDateEndMandat.addEventListener('click', function(event){
   event.preventDefault();
   let url = form.action;
   let data = new FormData(form);
   console.log(data);
   axios
       .post(url, data)
       .then(function (response) {
           let toastHTMLElement = document.getElementById("toaster");
           var message = "Le bien sera archivé le :";
           var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
           toastBody.textContent = message;
           var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000});
           toastElement.show();
       })
   ;
});