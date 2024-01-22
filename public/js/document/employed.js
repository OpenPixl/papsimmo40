// Préparation des variables
const SupprDocument  = new bootstrap.Modal(document.getElementById('SupprDocument'), {keyboard: true});
const submitSupprDocument = document.getElementById('submitSupprDocument');

// PARTIE Suppression du document
// ---------------------------------------
// Code d'ouverture de la modale de suppression d'une ligne en mode création d'un bien
function ModalSupprDocument(event){
    event.preventDefault();
    SupprDocument.show();
    let a = event.currentTarget;
    document.getElementById('submitSupprDocument').href = a.href;
}

submitSupprDocument.addEventListener('click', function(event){
    event.preventDefault();
    let urlSupprDocument = this.href;
    axios
        .post(urlSupprDocument)
        .then(function(response){
            document.getElementById('listDocument').innerHTML = response.data.liste;
            // Mise en place de l'évenement pour la suppression d'un bien en cours de création
            document.querySelectorAll('a.btnModalSupprDocument').forEach(function(link){
                link.addEventListener('click', ModalSupprDocument);
            });
            // Message d'alerte en cas de réussite
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.textContent = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000});
            toastElement.show();
        });
});

// ------------------------------------
// Génération du Sortable - Déplacement des documents et ordres d'affichages
//-------------------------------------
const sortableDoc = document.getElementById("sortDoc");
new Sortable(sortableDoc, {
    animation:150,
    // Called by any change to the list (add / update / remove)
    onSort: function (event) {
        let cols = sortableDoc.children;
        let data = Array();
        // on boucle sur le résultat des enfants pour envoyer au controller la modification du positionnement des photos
        for(i = 0; i < cols.length; i++){
            let idcol = cols[i].id;
            let key = i;
            data.push({"key" : key, "idcol" : parseInt(idcol)});
        }
        let url = "/gestapp/document/updateposition";
        axios
            .post(url, data)
            .then(function(response){
                //document.getElementById('listDocument').innerHTML = response.data.listDocument
                // initialisation du toaster bootstrap
                var toastHTMLElement = document.getElementById("toaster");
                var message = response.data.message;
                var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
                toastBody.textContent = message;
                var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000});
                toastElement.show();
            })
            .catch(function(error){
                console.log(error);
            });
    },
});

function FilterDocument(event){
    event.preventDefault();
    let url = this.href;
    axios
        .get(url)
        .then(function(response){
            document.getElementById('listDocument').innerHTML = response.data.liste;
        })
        .catch(function (error){
            console.log(error);
        });
}

// Mise en place de l'évenement pour la suppression d'un bien en cours de création
document.querySelectorAll('a.btnModalSupprDocument').forEach(function(link){
    link.addEventListener('click', ModalSupprDocument);
});

// Filtrage des documents
document.querySelectorAll('a.btnCat').forEach(function(link){
    link.addEventListener('click', FilterDocument);
});