document.querySelectorAll('a.page-link').forEach(function(link){
    link.addEventListener('click', onClickPage);
});

function onClickPage(event) {
    event.preventDefault();
    let url = this.href;
    let Params = url.split('?')[1];
    let page = url.split('?page=')[1];
    let section = document.getElementById('elements').children;
    let axiosURL = '';
    if(section[0].id === 'allpropertiessales'){
        // Construction de l'adresse url pour le controlleur et transmission des paramètres
        let axiosURL = '/gestapp/propertypublic/allpropertiessales' + "?" + Params.toString();
        axios
            .get(axiosURL)
            .then(response => {
                // rafraichissement du tableau
                const liste = document.getElementById('elements').innerHTML = response.data.liste;
                // Ajout d'un event sur Bouton de suppression dans la fenêtre modale
                document.querySelectorAll('a.page-link').forEach(function(link) {
                    link.addEventListener('click', onClickPage);
                });
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }
}