// déclaration des constructeurs
const btnStatusReco = document.getElementById('openReco');
const modalStatusReco = document.getElementById('modalStatusRecos');
const btnSubmitReco = document.getElementById('btnModalSubmit');
// Step 1 : validation de la recommandation
if(btnStatusReco !== null){
    modalStatusReco.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget;
        let recipient = button.getAttribute('data-bs-whatever');

        let step = recipient.split('-')[0];
        let id = recipient.split('-')[1];
        let annoucerFirstName = recipient.split('-')[2];
        let annouceLastName = recipient.split('-')[3];

        let modalTitle = modalStatusReco.querySelector('.modal-title');
        let modalBodyContent = modalStatusReco.querySelector('.modal-body');
        let btnSubmit = modalStatusReco.querySelector('a#btnModalSubmit');

        if(step == 'validReco'){
            modalTitle.textContent = 'Evolution de la recommandation : Etape 1';
            modalBodyContent.innerHTML = '<p>Recommandation de ' + annoucerFirstName + ' ' + annouceLastName +'</p><p>Avant de valider cette nouvelle recommandation, avez-vous contacté le client potentiel pour vous assurer de cette recommandation ?</p>';
            btnSubmit.removeAttribute('class');
            btnSubmit.classList.add('btn', 'btn-sm','btn-primary');
            btnSubmit.href = '/gestapp/reco/'+id+'/step1';
            btnSubmit.innerHTML = "<i class=\"fa-duotone fa-circle-check\"></i> Je valide la recommandation";
        }else if(step == 'validbyAdmin'){
            modalTitle.textContent = 'Evolution de la recommandation de ' + annoucerFirstName + ' ' + annouceLastName;
            modalBodyContent.innerHTML = '<p>Suite à la validation de cette proposition commerciale, le mandataire pourra inscrire le bien dans le processus de vente.</p>';
            btnSubmit.removeAttribute('class');
            btnSubmit.classList.add('btn', 'btn-sm','btn-primary');
            btnSubmit.href = '/gestapp/reco/'+id+'/step2';
            btnSubmit.innerHTML = "<i class=\"fa-duotone fa-circle-check\"></i> Je publie la recommandation";
        }else if(step == 'isValidbyAdmin'){
            modalTitle.textContent = 'Evolution de la recommandation de ' + annoucerFirstName + ' ' + annouceLastName;
            modalBodyContent.innerHTML = '<p>A partir de cette étape, la recommandation sera publiée sur la plateforme.</p>';
            btnSubmit.removeAttribute('class');
            btnSubmit.classList.add('btn', 'btn-sm','btn-primary');
            btnSubmit.href = '/gestapp/reco/'+id+'/step3';
            btnSubmit.innerHTML = "<i class=\"fa-duotone fa-circle-check\"></i> Je publie la recommandation";
        }


    });
}

btnSubmitReco.addEventListener('click', submitReco);

function submitReco(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('liste').innerHTML = response.data.liste;
        })
        .catch(function(error){
            console.log(error);
        })

}