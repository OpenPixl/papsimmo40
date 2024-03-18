// Déclaration des constructeurs
const modalReco = new bootstrap.Modal(document.getElementById('modalReco'));
const btnAddReco = document.getElementById('btnAddReco');

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

