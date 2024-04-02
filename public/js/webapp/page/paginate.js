document.querySelectorAll('a.page-link').forEach(function(link){
    link.addEventListener('click', onClickPage);
})
function onClickPage(event) {
    event.preventDefault();
    const url = this.href;
    const Params = url.split('?')[1];
    const page = url.split('?page=')[1];

    // Construction de l'adresse url pour le controlleur et transmission des paramètres
    const axiosURL = '/gestapp/product/filterwebapp' + "?" + type + '=' + name + '&' + Params.toString();

    if (page === undefined) {
        axios
            .get(axiosURL)
            .then(response => {
                // rafraichissement du tableau
                const liste = document.getElementById('liste').innerHTML = response.data.liste;
                // Ajout d'un event sur Bouton de suppression dans la fenêtre modale
                document.querySelectorAll('a.page-link').forEach(function(link) {
                    link.addEventListener('click', onClickPage);
                });
            });
    } else {
        axios
            .get(axiosURL + "?" + type + '=' + name + '&' + Params.toString())
            .then(response => {
                // rafraichissement du tableau
                const liste = document.getElementById('liste').innerHTML = response.data.liste;
                // Ajout d'un event sur Bouton de suppression dans la fenêtre modale
                document.querySelectorAll('a.page-link').forEach(function(link) {
                    link.addEventListener('click', onClickPage);
                });
            });
    }
}