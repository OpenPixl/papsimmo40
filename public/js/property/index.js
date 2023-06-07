// ----------------------------------------------------------------
// STEP 0 : A l'initialisation de la page
// ----------------------------------------------------------------

// ajout des tableaux complémentaires
axios
    .get('/gestapp/property/propertyDiffusion')
    .then(function(response){
        document.getElementById('listDiffusion').innerHTML = response.data.listdiffusion;
    });
axios
    .get('/gestapp/property/listarchived')
    .then(function(response){
        document.getElementById("listArchived").innerHTML = response.data.listarchived;
    });

const list = document.getElementById('list');
const listDiffusion = document.getElementById("listDiffusion");
const listArchived = document.getElementById("listArchived");
const btnListDiffusion = document.getElementById('btnListDiffusion');

// Afficher le tableau des diffusions pour chaque bien
btnListDiffusion.onclick = function(event){
    listDiffusion.className = listDiffusion.className !== 'show' ? 'show' : 'hide';
    if(listDiffusion.className === 'show') {
        listDiffusion.style.display = 'block';
        list.style.display = 'none';
        listArchived.style.display = 'none';
        document.getElementById('btnListDiffusion').textContent = 'Retour sur la liste des biens';
    }
    if(listDiffusion.className === 'hide') {
        listDiffusion.style.display = 'none';
        list.style.display = 'block';
        listArchived.style.display = 'none';
        document.getElementById('btnListDiffusion').textContent = 'Afficher la diffusions des biens';
    }
};
// Afficher le tableau des biens archivés
document.getElementById('btnMListArchived').onclick = function(event){

    listArchived.className = listArchived.className !== 'show' ? 'show' : 'hide';
    if(listArchived.className === 'show') {
        listArchived.style.display = 'block';
        list.style.display = 'none';
        listDiffusion.style.display = 'none';
        document.getElementById('btnMListArchived').textContent = 'Retour sur la liste des biens';
    }
    if(listArchived.className === 'hide') {
        listArchived.style.display = 'none';
        list.style.display = 'block';
        listDiffusion.style.display = 'none';
        document.getElementById('btnMListArchived').textContent = 'Afficher les biens archivés';
    }

};

// ----------------------------------------------------------------
// STEP 1 : Après l'initialisation de la page
// ----------------------------------------------------------------

// Ouverture de la modale d'ajout d'un bien / propriété avec intégration du Numéro de mandat modifiable par le créateur
// ----------------------------------------------------------------
// I. Ouverture de la modal
const modalAddNewProperty = document.getElementById('modalAddNewProperty');
const NewProperty = new bootstrap.Modal(modalAddNewProperty, { keyboard: true });
const addNewProperty = document.getElementById('addNewProperty');
const submitAddnewproperty = document.getElementById('submitAddnewproperty');
const ArrayMandats = [];

axios
    .get('/gestapp/property/getlistmandats')
    .then(function(response){
        // alimenter le tableau des ref existantes
        let ListMandats = response.data.listmandats;
        ListMandats.forEach((element) => {
            ArrayMandats.push(parseInt(element));
        });
    });
addNewProperty.addEventListener('click', function(event){
    event.preventDefault();
    NewProperty.show();
});
// condition si le bien doit etre créer sans numéro de mandat
document.getElementById('nomandat').addEventListener('change', function(event){
    if(this.checked){
        document.getElementById('tabMandat').setAttribute("class", "d-none");
        document.getElementById("submitAddnewproperty").href = '/gestapp/property/add/0/0';
    }else {
        document.getElementById('tabMandat').classList.remove("d-none");
        document.getElementById('tabMandat').setAttribute("class", "table");
    }
});
// III. Modification de l'addresse de traitement sur l'input
document.getElementById('refMandat').addEventListener('input', function(event){
    let newmandat = parseInt(document.getElementById('refMandat').value);
    let flag = 0;
    for(let i=0; i<ArrayMandats.length; i++) {
        if(newmandat === ArrayMandats[i]) {
            flag = 1;
        }
    }
    if(flag === 1){
        document.getElementById("refMandat").classList.remove("is-valid");
        document.getElementById("refMandat").classList.add("is-invalid");
        document.getElementById('tdconsign').innerHTML = 'Veuillez corriger ce numéro de mandat, <br>il est présent dans la liste des biens <b>Paps immo</b>';

    }else{
        document.getElementById("refMandat").classList.remove("is-invalid");
        document.getElementById("refMandat").classList.add("is-valid");
        document.getElementById('tdconsign').textContent = "Numéro de mandat valide.";
    }
});

document.getElementById("refMandat").addEventListener('click', function(event){
    if(document.getElementById("refMandat").value !== "" ){
        event.preventDefault();
    }
});

// Archivages d'un bien en création
// ----------------------------------------------------------------
// I.
function DelPropertyIncreating(event)
{
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response) {
            // rafraichissement du tableau
            document.getElementById('listeIncreating').innerHTML = response.data.liste;
            // Mise en place de l'évenement pour la suppression d'un bien en cours de création
            document.querySelectorAll('a.jsModalDelPropertyIncreating').forEach(function(link){
                link.addEventListener('click', DelPropertyIncreating);
            });
            // Mise en place de l'évenement pour la suppression d'un bien
            document.querySelectorAll('a.jsModalDelProperty').forEach(function(link){
                link.addEventListener('click', DelProperty);
            });

            // initialisation du toaster
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.textContent = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true,autohide: true,delay: 3000});
            toastElement.show();
        });
}


function DelProperty(event)
{
    event.preventDefault();
    const url = this.href;
    axios
        .post(url)
        .then(function(response) {
            // rafraichissement du tableau
            const liste = document.getElementById('list').innerHTML = response.data.liste;

            // Mise en place de l'évenement pour la suppression d'un bien en cours de création
            document.querySelectorAll('a.jsModalDelPropertyIncreating').forEach(function(link){
                link.addEventListener('click', DelPropertyIncreating);
            });

            // Mise en place de l'évenement pour la suppression d'un bien
            document.querySelectorAll('a.jsModalDelProperty').forEach(function(link){
                link.addEventListener('click', DelProperty);
            });

            // initialisation du toaster
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.innerHTML = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000,});
            toastElement.show();
        });
}

// Code d'ouverture de la modale de suppression d'une ligne en mode création d'un bien
var Suppr = document.getElementById('SupprIncreatring');
Suppr.addEventListener('show.bs.modal', function (event){
    // Button that triggered the modal
    var a = event.relatedTarget;
    // Extract info from data-bs-* attributes
    var recipient = a.getAttribute('data-bs-whatever');
    // If necessary, you could initiate an AJAX request here
    // and then do the updating in a callback.
    //
    // Update the modal's content.
    var modalTitle = Suppr.querySelector('.modal-title');
    var modalText = Suppr.querySelector('.modal-text');
    var modalBodyInput = Suppr.querySelector('.modal-body input');
    let modalFooterA = Suppr.querySelector('.modal-footer a');

    modalTitle.textContent = "Suppression d'un bien en cours de création";
    modalBodyInput.value = recipient;
    modalText.innerHTML = "Vous êtes sur le point de supprimer le bien en cours de création.<br><b>Etes-vous sur de vouloir continuer ?</b>";
    modalFooterA.href = '/gestapp/property/increatingdel/' + recipient;
});

// Mise en place de l'évenement pour la suppression d'un bien en cours de création
document.querySelectorAll('a.jsModalDelPropertyIncreating').forEach(function(link){
    link.addEventListener('click', DelPropertyIncreating);
});

// Mise en place de l'évenement pour la suppression d'un bien
document.querySelectorAll('a.jsModalDelProperty').forEach(function(link){
    link.addEventListener('click', DelProperty);
});

// PARTIE : Archivages de plusieurs biens
// -----------------------
// sélectionner toutes les checkBoxs à partir de la checkbox du ht de page
let arrayCheckbox = [];
document.getElementById('CheckAllProperties').onclick = function() {
    let checkboxes = document.getElementsByName('CheckProperty');
    for (let checkbox of checkboxes) {
        checkbox.checked = this.checked;
        if(checkbox.checked){
            arrayCheckbox.push(parseInt(checkbox.value));
            arrayCheckbox = [... new Set(arrayCheckbox)];
        }
        console.log(arrayCheckbox);
    }
};
// ou récupération des lignes sélectionnées
document.getElementById('SupprRows').onclick = function(event){
    event.preventDefault();
    let checkboxes = document.getElementsByName('CheckProperty');
    for (let checkbox of checkboxes) {
        if(checkbox.checked){
            arrayCheckbox.push(parseInt(checkbox.value));
            arrayCheckbox = [... new Set(arrayCheckbox)];
        }
        console.log(arrayCheckbox);
    }
};