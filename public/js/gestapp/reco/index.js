// Déclaration des constructeurs
const modalReco = new bootstrap.Modal(document.getElementById('modalReco'));
const modalRecoBs = document.getElementById('modalReco');
const btnAddReco = document.getElementById('btnAddReco');

const btnStatusReco = document.getElementById('openReco');
const btnSubmitReco = document.getElementById('btnModalSubmit');
const btnCommission = document.getElementById('btnCommission');
const btnModalPrescriber = document.getElementById('btnModalPrescriber');


// Ajout d'une recommandation
btnAddReco.addEventListener('click', showReco);
if(btnModalPrescriber !== null){
    btnModalPrescriber.addEventListener('click', showPrescriber);
}

document.querySelectorAll('a.btnEditReco').forEach(function(link){
    link.addEventListener('click', showReco);
});
btnCommission.addEventListener('click', showComm);
modalRecoBs.addEventListener('hidden.bs.modal', function(){
    if(modalRecoBs.querySelector('.modal-dialog').classList.contains('modal-xl')){
        modalRecoBs.querySelector('.modal-dialog').classList.remove('modal-xl');
    }
    if(modalRecoBs.querySelector('.modal-dialog #btnEditPrescriber')){
        modalRecoBs.querySelector('.modal-dialog #btnEditPrescriber').id = "btnModalSubmit";
    }
    modalRecoBs.querySelector('.modal-body').innerHTML =
        "<div class=\"d-flex justify-content-center\">"+
        "<div class=\"spinner-border text-primary\" role=\"status\">"+
        "<span class=\"visually-hidden\">Loading...</span>"+
        "</div>"+
        "</div>"
    ;

});

function showReco(event) {
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let url = this.href;
    modalReco.show();
    document.getElementById('modalReco').querySelector('.modal-dialog').classList.add('modal-xl');
    document.getElementById('modalReco').querySelector('.modal-title').textContent = contentTitle;
    if(crud === 'ADD'){
        document.getElementById('modalReco').querySelector('#btnModalSubmit').textContent = "Ajouter la recommandation";
        document.getElementById('modalReco').querySelector('#btnModalSubmit').href = url;
        document.getElementById('modalReco').querySelector('#btnModalSubmit').addEventListener('click', submitReco);
    }else if(crud === 'EDIT'){
        document.getElementById('modalReco').querySelector('#btnModalSubmit').textContent = "Modifier la recommandation";
        document.getElementById('modalReco').querySelector('#btnModalSubmit').href = url;
        document.getElementById('modalReco').querySelector('#btnModalSubmit').addEventListener('click', submitReco);
    }
    axios
        .post(url)
        .then(function(response){
            document.getElementById('modalReco').querySelector('.modal-body').innerHTML = response.data.formView;
            const cardComm = document.getElementById('cardComm');
            let selectRecoStatut = document.getElementById('reco_statutReco');
            if(selectRecoStatut.value > 5){
                cardComm.classList.remove('d-none');
                cardComm.classList.add('animate__animated', 'animate__fadeIn');
            }
            selectRecoStatut.addEventListener('change', function(){
                if(selectRecoStatut.value > 5){
                    cardComm.classList.remove('animate__animated','animate__fadeOut', 'd-none');
                    cardComm.classList.add('animate__animated', 'animate__fadeIn');
                }else if(selectRecoStatut.value < 6){
                    cardComm.classList.remove('animate__animated','animate__fadeIn');
                    cardComm.classList.add('animate__animated','animate__fadeOut', 'd-none');
                }
            });
        })
        .catch(function(error){
            console.log(error);
        });
    document.getElementById('modalReco').querySelector('.modal-body').innerHTML = "";
}

function showPrescriber(event){
    event.preventDefault();
    let url = this.href;
    modalReco.show();
    document.getElementById('modalReco').querySelector('.modal-dialog').classList.add('modal-xl');
    document.getElementById('modalReco').querySelector('.modal-title').textContent = "Modifier vos informations personnelles";
    document.getElementById('modalReco').querySelector('.modal-footer #btnModalSubmit').innerHTML = "Modifier les informations";
    document.getElementById('modalReco').querySelector('.modal-footer #btnModalSubmit').href = url;
    axios
        .post(url)
        .then(function(response) {
            document.getElementById('modalReco').querySelector('.modal-body').innerHTML = response.data.formView;
            document.getElementById('modalReco').querySelector('.modal-footer #btnModalSubmit').id = "btnEditPrescriber";
            reloadEvent();
        })
        .catch(function(error){
           console.log(error);
        })
    ;
}

function showComm(event){
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let url = this.href;
    modalReco.show();
    document.getElementById('modalReco').querySelector('.modal-title').textContent = contentTitle;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('modalReco').querySelector('.modal-body').innerHTML = response.data.formView;
        })
        .catch(function(error){
            console.log(error);
    });
}

function submitReco(event){
    event.preventDefault();
    let form = document.getElementById('formReco');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            document.getElementById('liste').innerHTML = response.data.liste;
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function editPrescriber(event){
    event.preventDefault();
    let form = document.getElementById('formPrescriber');
    let action = form.action;
    let data = new FormData(form);
    axios
        .post(action, data)
        .then(function(response){
            console.log(response.data);
            document.getElementById('liste').innerHTML = response.data.liste;
            reloadEvent();
        })
        .catch(function(error){
            console.log(error);
        })
    ;
}

function reloadEvent(){
    // Ajout d'une recommandation
    btnAddReco.addEventListener('click', showReco);
    if(btnModalPrescriber !== null){
        btnModalPrescriber.addEventListener('click', showPrescriber);
    }
    if(btnSubmitReco !== null){
        btnSubmitReco.addEventListener('click', submitReco);
    }
    if(document.getElementById('btnEditPrescriber')){
        document.getElementById('btnEditPrescriber').addEventListener('click', editPrescriber);
    }
    document.querySelectorAll('a.btnEditReco').forEach(function(link){
        link.addEventListener('click', showReco);
    });
}



