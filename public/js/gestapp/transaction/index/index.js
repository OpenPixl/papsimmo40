// Déclaration des constructeurs
const btnModalTransaction = document.getElementById('btnModalTransaction');
const modalShow = document.getElementById('modalTransaction');
const btnSubmitInvoiceAddColl = document.getElementById('btnSubmitInvoiceAddColl');
const modalInvoiceBS = new bootstrap.Modal(document.getElementById('modalInvoice'));

modalShow.addEventListener('show.bs.modal', function (event) {
    let a = event.relatedTarget;

    let opt = a.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    if(crud ==='DEL'){
        modalShow.querySelector('.modal-title').textContent = contentTitle;
        modalShow.querySelector('#btnModalSubmit').href = a.href;
        modalShow.querySelector('#btnModalSubmit').addEventListener('click', delTransaction);
    }
});

if(document.querySelectorAll('.transClosed') !== null){
    document.querySelectorAll('.transClosed').forEach(function(link){
        link.addEventListener('click', closedFolder);
    });
}

if(document.querySelectorAll('.modalInvoice') !== null){
    document.querySelectorAll('.modalInvoice').forEach(function(link){
        link.addEventListener('click', showModalInvoice);
    });
}

document.getElementById('submitModalInvoice').addEventListener('click', submitModalInvoice);

function delTransaction(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            if(response.data.accessAdmin === true)
            {
                document.getElementById('liste').innerHTML = response.data.liste;
                document.getElementById('ownliste').innerHTML = response.data.ownliste;
            }else{
                document.getElementById('ownliste').innerHTML = response.data.liste;
            }
            reloadEvent;
        })
        .catch(function(error){
            console.log(error);
        });
}

function showModalInvoice(event){
    event.preventDefault();
    let url = this.href;
    let a = event.currentTarget;
    let opt = a.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    modalInvoiceBS.show();
    let modalInvoice = document.getElementById('modalInvoice');
    if(crud === 'EDIT'){
        modalInvoice.querySelector('.modal-header').textContent = contentTitle;
        modalInvoice.querySelector('.modal-footer a').href = url;
        axios
            .get(url)
            .then(function(response){
                modalInvoice.querySelector('.modal-body').innerHTML = response.data.formView;
            })
            .catch(function(error){
                console.log(error);
            });
        reloadEvent();
    }

}

function submitModalInvoice(event){
    event.preventDefault();
    let form = document.getElementById('FormAddcollaboratorInvoice');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            // initialisation du toaster
            let toastHTMLElement = document.getElementById("toaster");
            let message = response.data.message;
            let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.textContent = message;
            let toastElement = new bootstrap.Toast(toastHTMLElement, {
                animation: true,
                autohide: true,
                delay: 5000,
            });
            toastElement.show();
        })
        .catch(function(error){
            console.log(error);
        });
}

function closedFolder(event){
    event.preventDefault();
    let url = this.href;
    axios
        .get(url)
        .then(function (response){
            // initialisation du toaster
            let toastHTMLElement = document.getElementById("toaster");
            let message = response.data.message;
            let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.textContent = message;
            let toastElement = new bootstrap.Toast(toastHTMLElement, {
                animation: true,
                autohide: true,
                delay: 5000,
            });
            toastElement.show();
        })
        .catch(function (error){
            console.log(error);
        });
}

function reloadEvent(){
    document.getElementById('submitModalInvoice').addEventListener('click', submitModalInvoice);
}