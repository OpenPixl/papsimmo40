import "./events.js";
import "./fonctions";

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
