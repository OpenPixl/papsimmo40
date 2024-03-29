// Préparation des variables
const SupprDocument  = new bootstrap.Modal(document.getElementById('SupprDocument'), {keyboard: true});
const submitSupprDocument = document.getElementById('submitSupprDocument');

// Chargement de Filetype selon le select
// -------------------------------------
// I. On sélectionne le select
const typeDoc = document.getElementById('document_typeDoc');
// II. Mise en place de l'event
typeDoc.addEventListener('change', function(event){
    if(typeDoc.value == 'Pdf'){
        document.getElementById('pdf').removeAttribute('style');
        document.getElementById('word').style.display = "none";
        document.getElementById('excel').style.display = "none";
        document.getElementById('mp4').style.display = "none";
    }else if(typeDoc.value == 'Word'){
        document.getElementById('pdf').style.display = "none";
        document.getElementById('word').removeAttribute('style');
        document.getElementById('excel').style.display = "none";
        document.getElementById('mp4').style.display = "none";
    }else if(typeDoc.value == 'Excel'){
        document.getElementById('pdf').style.display = "none";
        document.getElementById('word').style.display = "none";
        document.getElementById('excel').removeAttribute('style');
        document.getElementById('mp4').style.display = "none";
    }else if(typeDoc.value == 'Mp4'){
        document.getElementById('pdf').style.display = "none";
        document.getElementById('word').style.display = "none";
        document.getElementById('excel').style.display = "none";
        document.getElementById('mp4').removeAttribute('style');
    }
});

// PARTIE Ajout de la category
// ---------------------------------------
// I. Mise en place du Select2
const TsPropertyBanner = new TomSelect("#document_category",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});
// II. Préparation de la modale
const addCategory  = new bootstrap.Modal(document.getElementById('addCategory'), {keyboard: true});
document.getElementById('btnAddCatDocument').addEventListener('click', function(event){
    event.preventDefault();
    addCategory.show();
});
document.getElementById('submitAddCatDocument').addEventListener('click', function(event){
    event.preventDefault();
    let formAddCatDocument = document.getElementById('formAddCatDocument');
    let urlAddCatDocument = formAddCatDocument.action;
    let dataAddCatDocument = new FormData(formAddCatDocument);
    axios
        .post(urlAddCatDocument, dataAddCatDocument)
        .then(function(response){
            // Message d'alerte en cas de réussite
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.innerHTML = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000});
            toastElement.show();
            // Ajout dans le TomSelect de la nouvelle option.
            const selectbanner = document.getElementById('document_category');
            const optionbanner = document.createElement("option");
            optionbanner.setAttribute("value", response.data.value);
            optionbanner.setAttribute("data-data", response.data.data);
            optionbanner.text = response.data.banner;
            selectbanner.add(optionbanner);
            TsPropertyBanner.addOption({data: response.data.data, value:response.data.value});
            TsPropertyBanner.addItem(response.data.cat);
            addCategory.hide();
        })
        .catch(function(error){
            console.log(error);
        });
    axios
        .post('/gestapp/choice/cat/document/json/1')
        .then(function(response){
            document.getElementById('listCat').innerHTML = response.data.liste;
        })
        .catch(function(error){
            console.log(error);
        });
});

// PARTIE Ajout du document
// ---------------------------------------
document.getElementById('submitDocument').addEventListener('click', function(event){
    event.preventDefault();
    let formAddDocument = document.getElementById('FormAddDocument');
    let urlAddDocument = formAddDocument.action;
    let dataAddDocument = new FormData(formAddDocument);
    axios
        .post(urlAddDocument, dataAddDocument)
        .then(function(response){
            document.getElementById('listDocument').innerHTML = response.data.list;
            // Mise en place de l'évenement pour la suppression d'un bien en cours de création
            document.querySelectorAll('a.btnModalSupprDocument').forEach(function(link){
                link.addEventListener('click', ModalSupprDocument);
            });
            // Message d'alerte en cas de réussite
            var toastHTMLElement = document.getElementById("toaster");
            var message = response.data.message;
            var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
            toastBody.innerHTML = message;
            var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000});
            toastElement.show();
            formAddDocument.reset();
        })
        .catch(function(error){
            console.log(error.response.data);
            console.log(error.response.status);
            console.log(error.response.headers);
        });
});

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