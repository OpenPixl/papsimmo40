import axios from "axios";

const modal = new bootstrap.Modal(document.getElementById('modal'), {keyboard: false});
const modalSuppr = new bootstrap.Modal(document.getElementById('modalSuppr'), {keyboard: false});
const strechedlinks = document.querySelectorAll(".stretched-link");

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
    if(crud === "OPEN_Agency"){
        document.getElementById('modal').querySelector('.modal-dialog').classList.add('modal-xl');
        document.getElementById('modal').querySelector('.modal-title').textContent = contentTitle;
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
        document.getElementById('modal').querySelector('.modal-title').textContent = contentTitle;
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "OPEN_Transac"){
        document.getElementById('modal').querySelector('.modal-title').textContent = contentTitle;
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
        document.getElementById('modal').querySelector('.modal-title').textContent = contentTitle;
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
            })
            .catch(function(error){
                console.log(error);
            });
    }else if(crud === "OPEN_Ssrubric"){
        document.getElementById('modal').querySelector('.modal-title').textContent = contentTitle;
        axios
            .get(url)
            .then(function(response){
                document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
            })
            .catch(function(error){
                console.log(error);
            });
    }
}

function submitModalForm(event){
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let name = opt.split('-')[0];

    if(name === 'formAgency'){
        let form = document.getElementById(name);
        let action = form.action;
        let data = new FormData(form);
        axios
            .post(action, data)
            .then(function(response){
                document.getElementById('liste').innerHTML = response.data.liste;
            })
            .catch(function(error){console.log(error);});
    }

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
    document.getElementById('modalSuppr').querySelector('.modal-body').innerHTML =
    "Vous êtes sur le point de supprimer une agence. Etes-vous sur de votre choix ?";
    document.getElementById('modalSuppr').querySelector('.modal-footer a#btnSuppr').addEventListener('click', SupprAgency);
}

function SupprAgency(event){
    event.preventDefault;
    let url = this.href;
    axios
        .post(url)
        .then(function (response){
            let url = 'gestapp/agency/';
            modalSuppr.hide();
            modal.show();
            document.getElementById('modal').querySelector('.modal-title').textContent = contentTitle;
            document.getElementById('modal').querySelector('.modal-dialog').classList.add('modal-xl');
            axios
                .post(url)
                .then(function(response){
                    document.getElementById('modal').querySelector('.modal-body').innerHTML = response.data.view;
                    loadEvents();
                })
                .catch();

        })
        .catch(function(error){
            console.log(error);
        });
}

function loadEvents(){
    let buttons = document.querySelectorAll('.modalSubmit');
    buttons.forEach(function(btn){
        btn.addEventListener('click', submitModalForm);
    });
    let btnModalSuppr = document.querySelectorAll('.btnModalSuppr');
    btnModalSuppr.forEach(function(link){
        link.addEventListener('click', openModalSuppr);
    });
}

document.getElementById('modal').addEventListener('hidden.bs.modal', function(){
    document.getElementById('modal').querySelector('.modal-dialog').classList.remove('modal-xl');
    document.getElementById('modal').querySelector('.modal-title').textContent = "Adhésions";
    document.getElementById('modal').querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">"+
        "<div class=\"spinner-border text-primary\" role=\"status\">"+
        "<span class=\"visually-hidden\">Chargement ...</span>"+
        "</div>"+
        "</div>"
    ;
});