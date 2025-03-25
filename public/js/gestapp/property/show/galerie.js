import { toasterMessage } from '../../../commonFunctions.js';

const btndelVideo = document.querySelector('.delVideo');
const btnaddVideo = document.getElementById('btnAddVideo');
const input_image = document.getElementById('property_image_images');

function controlRatioImage(event){
    const files = event.target.files;
    const listeNomPhotos = document.getElementById('liste_nom_photos');
    listeNom_photos.innerHTML = '';
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileName = file.name;

            const img = new Image();
            const reader = new FileReader();

            reader.onload = function(e) {
                img.src = e.target.result;
                img.onload = function() {
                    const width = img.width;
                    const height = img.height;
                    const ratio = width / height;
                    const targetRatio = 16 / 9;
                    const ratioMessage = Math.abs(ratio - targetRatio) < 0.01 ? 'le ratio est respecté' : 'le ratio n\'est pas respecté';
                    if (Math.abs(ratio - targetRatio) > 0.01) {
                        fileNameDisplay.innerHTML += '<ul style="font-size: 0.8rem;"><li>' + fileName + ' : le ratio doit être de 16:9.</li></ul>';
                    } else {
                        fileNameDisplay.innerHTML += '<ul style="font-size: 0.8rem;"><li>' + fileName + ' : ratio ok</li></ul>';
                    }
                };
            };
        }
    }
}

function loadEvent(){
    input_image.addEventListener('change', controlRatioImage);
    if (btnaddVideo !== null) {
        btnaddVideo.addEventListener('click', event => {
            event.preventDefault();
            let form = document.getElementById('addVideo');
            let action = form.action;
            let data = new FormData(form);
            axios
                .post(action, data)
                .then(function (response) {
                    toasterMessage(response.data.message);
                    document.getElementById('setVideo').innerHTML = response.data.view;
                })
                .catch(function (error) {
                    console.log(error);
                });
            loadEvent();
        });
    }

    if (btndelVideo !== null) {
        btndelVideo.addEventListener('click', (e) => {
            e.preventDefault();
            let a = e.currentTarget;
            let url = a.href;
            console.log(url);
            axios
                .post(url)
                .then(function(response){
                    toasterMessage(response.data.message);
                    document.getElementById('setVideo').innerHTML = response.data.view;

                })
                .catch(function(error){
                    console.log(error);
                });
            loadEvent();
        });
    }
}

loadEvent();