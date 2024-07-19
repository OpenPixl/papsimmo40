// attribus
let carte = L.map('carte').setView([43.8909, -0.5009], 14);
let btnGetCoord = document.getElementById('findGeoCoord');
let map;
let marker;

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributeurs',
    minZoom: 10,
    maxZoom: 16
}).addTo(carte);

if(document.getElementById('coordLat') !== null && document.getElementById('coordLong') !== null){
    if (marker) {
        marker.setLatLng([document.getElementById('coordLat').value, document.getElementById('coordLong').value]);
    } else {
        marker = L.marker([document.getElementById('coordLat').value, document.getElementById('coordLong').value]).addTo(carte);
    }
    carte.setView([document.getElementById('coordLat').value, document.getElementById('coordLong').value], 13);
}


btnGetCoord.addEventListener('click', getCoordinates);

async function getCoordinates(event) {
    event.preventDefault;
    // Récupérer les valeurs des champs de formulaire
    const address = document.getElementById('property_step1_adress').value;
    const zipcode = document.getElementById('property_step1_zipcode').value;
    const city = document.getElementById('property_step1_city').value;
    const id = document.getElementById('property_id').value;

    // Construire l'URL de la requête
    const query = `${address} ${zipcode} ${city}`;
    let url = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}`;

    try {
        // Faire la requête à l'API
        const response = await fetch(url);
        const data = await response.json();

        // Vérifier s'il y a des résultats
        if (data.features.length > 0) {
            // Récupérer les coordonnées du premier résultat
            const coordinates = data.features[0].geometry.coordinates;
            const latitude = coordinates[1];
            const longitude = coordinates[0];

            // Afficher les coordonnées dans la page
            document.getElementById('result').innerText = `Latitude : ${latitude}, Longitude : ${longitude}`;

            // Mettre à jour la carte et le marqueur
            if (marker) {
                marker.setLatLng([latitude, longitude]);
            } else {
                marker = L.marker([latitude, longitude]).addTo(carte);
            }
            carte.setView([latitude, longitude], 13);

            // persistance des coordonnées en bdd
            let url = '/gestapp/property/' + id + '/updateCoordonnees/' + latitude +'&'+ longitude;
            axios
                .post(url)
                .then(function(response){

                })
                .catch(function(error){
                    console.log(error);
                });

        } else {
            document.getElementById('result').innerText = 'Aucun résultat trouvé.';
        }
    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('result').innerText = 'Erreur lors de la récupération des coordonnées.';
    }
}