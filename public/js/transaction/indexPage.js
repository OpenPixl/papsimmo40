// Déclaration des constructeurs
const btnModalTransaction = document.getElementById('btnModalTransaction');
const modalTransaction = document.getElementById('modalTransaction');
const btnModalSubmit = document.getElementById('btnModalSubmit');
const btnSubmitInvoiceAddColl = document.getElementById('btnSubmitInvoiceAddColl');
const modalInvoiceBS = new bootstrap.Modal(document.getElementById('modalInvoice'));

modalTransaction.addEventListener('show.bs.modal', function (event) {
    let a = event.relatedTarget;
    let url = a.href;
    let submit = modalTransaction.querySelector('#btnModalSubmit');
    submit.href = url;
});

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
            }else{
                document.getElementById('ownliste').innerHTML = response.data.liste;
            }
            btnModalSubmit.addEventListener('click', delTransaction);
        })
        .catch(function(error){
            console.log(error);
        });
}

function showModalInvoice(event)
{
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

function submitModalInvoice(event)
{
    event.preventDefault();
    let form = document.getElementById('FormAddcollaboratorInvoice');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){

        })
        .catch(function(error){
            console.log(error);
        });
}

btnModalSubmit.addEventListener('click', delTransaction);

function reloadEvent(){
    document.getElementById('submitModalInvoice').addEventListener('click', submitModalInvoice);
}