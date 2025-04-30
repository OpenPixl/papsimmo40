const modal = new bootstrap.Modal(document.getElementById('modal'), {keyboard: false});
const modalSuppr = new bootstrap.Modal(document.getElementById('modalSuppr'), {keyboard: false});
const strechedlinks = document.querySelectorAll(".stretched-link");
const modalBanner = document.getElementById('modalBanner');
const cardBodylistBanner = document.getElementById('ListBanner');

strechedlinks.forEach(function(link){
    link.addEventListener('click', openModal);
});

function openModal(event){
    event.preventDefault();
    let url = this.href;
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let id = opt.split('-')[2];
    modal.show();
    document.getElementById('modal').querySelector('.modal-title').textContent = contentTitle;
    if(crud === "OPEN_Agency"){
        document.getElementById('modal').querySelector('.modal-dialog').classList.add('modal-xl');
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
                loadEvents();
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "OPEN_Agent"){
        document.getElementById('modal').querySelector('.modal-dialog').classList.add('modal-lg');
        loadEvents();
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
                loadEvents();
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "OPEN_Transac"){
        axios
            .get(url)
            .then(function(response){
                console.log(response.data.view);
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "OPEN_Rubric"){
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "OPEN_Ssrubric"){
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "EDIT_Banner"){
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
                loadEvents();
            })
            .catch(function(error){
                console.log(error);
            });
    }
}

function submitModalForm(event){
    event.preventDefault();
    // Récupération du formulaire
    let form = document.querySelector('.modal-body form');
    let action = form.action;
    let data = new FormData(form);
    let idForm = form.id;
    // Soumission du formulaire
    axios
        .post(action, data)
        .then(function(response){
            if(idForm === 'FormPropertyBanner'){
                console.log(idForm);
                cardBodylistBanner.innerHTML = response.data.view;
            }
            loadEvents();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function openModalSuppr(event) {
    event.preventDefault();
    let url = this.href;
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    modalSuppr.show();
    document.getElementById('modalSuppr').querySelector('.modal-title').textContent = contentTitle;
    document.getElementById('modalSuppr').querySelector('.modal-footer a#btnSuppr').href = url;
    document.getElementById('modalSuppr').querySelector('.modal-footer button').setAttribute('data-bs-whatever','OPEN_Rubric-Liste des rubriques');
    document.getElementById('modalSuppr').querySelector('.modal-body').innerHTML =
    "Vous êtes sur le point de supprimer un élément. Etes-vous sur de votre choix ?";
    document.getElementById('modalSuppr').querySelector('.modal-footer a#btnSuppr').addEventListener('click', SupprAgency);
}

function SupprAgency(event){
    event.preventDefault;
    modal.show();
    document.getElementById('modal').querySelector('.modal-dialog').classList.add('modal-xl');
    document.getElementById('modal').querySelector('.modal-dialog').setAttribute('style', 'width:1600px');
    let url = this.href;
    axios
        .post(url)
        .then(function (response){
            document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
            loadEvents();
        })
        .catch(function(error){
            console.log(error);
        });
}

function loadEvents(){
    let btnOpenModal = document.querySelectorAll('.btnOpenModal');
    btnOpenModal.forEach(function(link){
       link.addEventListener('click', openModal);
    });
    let buttons = document.querySelectorAll('#btnModalSubmit');
    buttons.forEach(function(btn){
        btn.addEventListener('click', submitModalForm);
    });
    let btnModalSuppr = document.querySelectorAll('.btnModalSuppr');
    btnModalSuppr.forEach(function(link){
        link.addEventListener('click', openModalSuppr);
    });
}

function listeBanner(){
    axios
        .get('/gestapp/choice/property/banner')
        .then(function(response){
            cardBodylistBanner.innerHTML = response.data.form;
            loadEvents();
        })
        .catch(function(error)
        {
            console.log(error);
        })
    ;

}

document.getElementById('modal').addEventListener('hidden.bs.modal', function(){
    document.getElementById('modal').querySelector('.modal-dialog').classList.remove('modal-xl');
    document.getElementById('modal').querySelector('.modal-dialog').classList.remove('modal-lg');
    document.getElementById('modal').querySelector('.modal-title').textContent = "Adhésions";
    document.getElementById('modal').querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">"+
        "<div class=\"spinner-border text-primary\" role=\"status\">"+
        "<span class=\"visually-hidden\">Chargement ...</span>"+
        "</div>"+
        "</div>"
    ;
});

// Modal Banner
if (modalBanner) {
    modalBanner.addEventListener('show.bs.modal', event => {
        // Button that triggered the modal
        const button = event.relatedTarget;
        const recipient = button.getAttribute('data-bs-whatever');
        let crud = recipient.split('-')[0];
        let contentTitle = recipient.split('-')[1];
        let id = recipient.split('-')[2];
        // If necessary, you could initiate an Ajax request here
        // and then do the updating in a callback.

        // Update the modal's content.
        const modalTitle = modalBanner.querySelector('.modal-title');
        modalTitle.textContent = contentTitle;
    });
}
listeBanner();
loadEvents();