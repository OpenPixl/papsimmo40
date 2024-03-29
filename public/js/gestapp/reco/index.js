// Déclaration des constructeurs
const modalReco = new bootstrap.Modal(document.getElementById('modalReco'));
const modalRecoBs = document.getElementById('modalReco');
const btnAddReco = document.getElementById('btnAddReco');

const btnStatusReco = document.getElementById('openReco');
const btnSubmitReco = document.getElementById('btnModalSubmit');
const btnCommission = document.getElementById('btnCommission');


// Ajout d'une recommandation
btnAddReco.addEventListener('click', showReco);

btnSubmitReco.addEventListener('click', submitReco);

document.querySelectorAll('a.btnEditReco').forEach(function(link){
    link.addEventListener('click', showReco);
});
btnCommission.addEventListener('click', showComm);
modalRecoBs.addEventListener('hidden.bs.modal', function(){
    if(modalRecoBs.querySelector('.modal-dialog').classList.contains('modal-xl')){
        modalRecoBs.querySelector('.modal-dialog').classList.remove('modal-xl');
    }
});
function showReco(event){
    event.preventDefault();
    let opt = this.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let url = this.href;
    modalReco.show();
    document.getElementById('modalReco').querySelector('.modal-dialog').classList.add('modal-xl');
    document.getElementById('modalReco').querySelector('.modal-title').textContent = contentTitle;

    if(crud === 'EDIT'){
        document.getElementById('btnModalSubmit').textContent = "Modifier la recommandation";
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
                if(cardComm.classList.contains('d-none')){
                    cardComm.classList.remove('d-none');
                    cardComm.classList.add('animate__animated', 'animate__fadeIn');
                }else{
                    cardComm.classList.remove('animate__fadeIn');
                    cardComm.classList.add('animate__fadeOut');
                    cardComm.classList.add('d-none');
                }
            });

        })
        .catch(function(error){
            console.log(error);
        });
    document.getElementById('modalReco').querySelector('.modal-body').innerHTML = "";
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
            modalReco.hide();
            document.getElementById('modalReco').querySelector('.modal-dialog').classList.remove('modal-xl');
            document.getElementById('modalReco').querySelector('.modal-title').textContent = "Ajout d'une recommandation";
            document.getElementById('modalReco').querySelector('.modal-body').innerHTML =
                '<div class="d-flex justify-content-center">\n' +
                '<div class="spinner-border text-primary" role="status">\n' +
                '<span class="visually-hidden">Loading...</span>\n' +
                '</div>\n' +
                '</div>';
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

    btnSubmitReco.addEventListener('click', submitReco);
    document.querySelectorAll('a.btnEditReco').forEach(function(link){
        link.addEventListener('click', showReco);
    });
}



