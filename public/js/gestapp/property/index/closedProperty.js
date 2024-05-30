const modalDisClosed = document.getElementById('modalDisClosed');
let btnSubmitDisclosed = document.getElementById('submitDisclosed');

if(document.querySelectorAll('.modalDisClosed') !== null){
    document.querySelectorAll('.modalDisClosed').forEach(function(link){
        link.addEventListener('click', showDisclosed);
    });
}

modalDisClosed.addEventListener('show.bs.modal', showDisclosed);
btnSubmitDisclosed.addEventListener('click', submitDisclosed);

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
        .get(url)
        .then(function (response){
            document.getElementById('listClosed').innerHTML = response.data.listClosed;
            // initialisation du toaster
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.textContent = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, option);
            toastElement.show();
        })
        .catch(function(error){
            console.log(error);
        });
}

function transClosed(){
    event.preventDefault();
}

function reloadClosedProperty(){
    modalDisClosed.addEventListener('show.bs.modal', showDisclosed);
    btnSubmitDisclosed.addEventListener('click', submitDisclosed);
}
