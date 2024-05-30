const modalDisClosed = document.getElementById('modalDisClosed');
<<<<<<< Updated upstream
const modalDisclosedBS = new boostrap.Modal(document.getElementById('modalClosed'));

if(document.querySelectorAll('.modalDisClosed') !== null){
    document.querySelectorAll('.modalDisClosed').forEach(function(link){
        link.addEventListener('click', showDisclosed);
    });
}

function showDisclosed(event){
    event.preventDefault();
    let a = event.currentTarget;
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('listClosed').innerHTML = response.data.listClosed;
=======

modalDisClosed.addEventListener('show.bs.modal', showDisclosed);

function showDisclosed(event){
    //event.preventDefault();
    let a = event.relatedTarget;
    let url = a.href;
    modalDisClosed.querySelector('.modal-footer a').href = url;
}

function submitDisclosed(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function (response){
            document.getElementById('listClosed').innerHTML = response.data.listClosed;
            // initialisation du toaster
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.textContent = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, option);
            toastElement.show();
>>>>>>> Stashed changes
        })
        .catch(function(error){
            console.log(error);
        });
}

function reloadClosedProperty(){
<<<<<<< Updated upstream

=======
    modalDisClosed.addEventListener('show.bs.modal', showDisclosed);
>>>>>>> Stashed changes
}
