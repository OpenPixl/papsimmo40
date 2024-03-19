// Déclaration des constructeurs
const modalReco = new bootstrap.Modal(document.getElementById('modalReco'));
const btnAddReco = document.getElementById('btnAddReco');

const cardComm = document.getElementById('cardComm');
let selectRecoStatut = document.getElementById('reco_statutReco');

const btnStatusReco = document.getElementById('openReco');
const btnSubmitReco = document.getElementById('btnModalSubmit');


// Ajout d'une recommandation
btnAddReco.addEventListener('click', function(event){
    event.preventDefault();
    let url = this.href;
    modalReco.show();
    document.getElementById('modalReco').querySelector('.modal-dialog').classList.add('modal-xl');
    document.getElementById('modalReco').querySelector('.modal-title').textContent = "Ajout d'une recommandation";
    axios
        .post(url)
        .then(function(response){
            document.getElementById('modalReco').querySelector('.modal-body').innerHTML = response.data.formView;
        })
        .catch(function(error){
            console.log(error);
        });
    document.getElementById('modalReco').querySelector('.modal-body').innerHTML = "";
});

btnSubmitReco.addEventListener('click', function(event){
   event.preventDefault();
   let form = document.getElementById('formAddReco');
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
       })
       .catch(function(error){
           console.log(error);
       })
   ;
});

