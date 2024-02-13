// PARTIE Suppression du document
// ---------------------------------------
// Code d'ouverture de la modale de suppression d'une ligne en mode création d'un bien
function ModalSupprDocument(event){
    event.preventDefault();
    SupprDocument.show();
    let a = event.currentTarget;
    document.getElementById('submitSupprDocument').href = a.href;
}