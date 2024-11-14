import { toasterMessage } from '../../../commonFunctions.js';

const btndelVideo = document.querySelector('.delVideo');
const btnaddVideo = document.getElementById('btnAddVideo');

function loadEvent(){
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