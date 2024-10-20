const modalDetails = new bootstrap.Modal(document.getElementById('modalDetails'));
const modalDetailsBs = document.getElementById('modalDetails');

document.querySelectorAll('a.btnModalDetails').forEach(function(link){
    link.addEventListener('click', showModal);
});

modalDetailsBs.addEventListener('hidden.bs.modal', function(){
    if(modalDetailsBs.querySelector('.modal-dialog').classList.contains('modal-xl')){
        modalDetailsBs.querySelector('.modal-dialog').classList.remove('modal-xl');
    }
    modalDetailsBs.querySelector('.modal-header').innerHTML =
        "<h1 class=\"modal-title fs-5\" id=\"exampleModalLabel\">Ajout d'information au registre</h1>\n" +
        "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>";
    modalDetailsBs.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">"+
        "<div class=\"spinner-border text-primary\" role=\"status\">"+
        "<span class=\"visually-hidden\">Loading...</span>"+
        "</div>"+
        "</div>"
    ;

});

document.querySelectorAll('.btnAddPropertySOAP').forEach(function(link){
    link.addEventListener('click', addProperty);
});

function showModal(event){
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let id= opt.split('-')[2];
    let compl= opt.split('-')[3];
    let url = this.href;
    if(crud === 'SHOW' && compl === 'Ras'){
        document.getElementById('modalDetails').querySelector('.modal-title').textContent = contentTitle;
    }else if(crud === 'EDIT' && compl === 'Mandant')
    {
        document.getElementById('modalDetails').querySelector('.modal-title').textContent = contentTitle;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('modalDetails').querySelector('.modal-body').innerHTML = response.data.formView;
                reloadEventRegistre();
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === 'EDIT' && compl === 'TMandat')
    {
        document.getElementById('modalDetails').querySelector('.modal-title').textContent = contentTitle;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('modalDetails').querySelector('.modal-body').innerHTML = response.data.formView;
                reloadEventRegistre();
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === 'EDIT' && compl === 'DMandat')
    {
        document.getElementById('modalDetails').querySelector('.modal-title').textContent = contentTitle;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('modalDetails').querySelector('.modal-body').innerHTML = response.data.formView;
                reloadEventRegistre();
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === 'EDIT' && compl === 'OMandat')
    {
        document.getElementById('modalDetails').querySelector('.modal-title').textContent = contentTitle;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('modalDetails').querySelector('.modal-body').innerHTML = response.data.formView;
                reloadEventRegistre();
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === 'VALID' && compl === 'VMandat')
    {
        document.getElementById('modalDetails').querySelector('.modal-title').textContent = contentTitle;
        document.getElementById('modalDetails').querySelector('.modal-body').innerHTML = '<p>En cliquant sur validation, la réservation sera validée dans le registre.<br>Toutes modification sera alors impossible.Etes-vous sur de vouloir continuer ?</p>';
    }
}

function addProperty(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
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
        .catch(function(response){
            console.log(error);
        })
    ;
}

function reloadEventRegistre()
{
    document.querySelectorAll('a.btnModalDetails').forEach(function(link){
        link.addEventListener('click', showModal);
    });

}