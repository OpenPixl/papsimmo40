// ----------------------------------------------------------------
// STEP 0 : A l'initialisation de la page
// ----------------------------------------------------------------

// autres mises en place
// AddAvenant.style.display = 'none'
// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickr", {
    "locale": "fr",
    enableTime: false,
    allowInput: true,
    altFormat: "j F Y",
    dateFormat: "d/m/Y",
});
// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickrtime", {
    "locale": "fr",
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

const list = document.getElementById('list');
const listDiffusion = document.getElementById("listDiffusion");
const listArchived = document.getElementById("listArchived");

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

// Afficher le tableau des diffusions pour chaque bien
document.getElementById('btnListDiffusion').onclick = function(event){
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