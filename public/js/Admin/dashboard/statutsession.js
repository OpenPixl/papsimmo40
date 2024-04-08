const alertSession = new bootstrap.Modal(document.getElementById('alertSession'));
let main = document.querySelector('#main');
let timer = 0;

setInterval(statutSession, 30000);
setInterval(augmenter, 1000);

document.body.addEventListener('click', function( event ){
    if( main.contains( event.target ) ){
        timer = 0;
    } else {
        console.log(timer);
    }
});

function augmenter() {
    timer++;
    console.log(timer);
}

function statutSession()
{
    axios
        .get('/opadmin/dashboard/sessionstatut')
        .then(function (response){
            let stTimeless = response.data.sttimeless;
            if(stTimeless === 2){
                console.log('session à 600');
                alertSession.show();
                document.getElementById('alertSession').querySelector('.modal-body').innerHTML = "<p>Il vous reste moins de 10 min avant de devoir vous reconnecter.</p>";
            }else if(stTimeless === 1){
                console.log('session à 300');
                alertSession.show();
                document.getElementById('alertSession').querySelector('.modal-body').innerHTML = "<p>Il vous reste moins de 5 min avant de devoir vous reconnecter.</p>";
            }else if(stTimeless === 0){
                console.log('Session à zéro');
                alertSession.show();
                document.getElementById('alertSession').querySelector('.modal-body').innerHTML = "<h4>Fermeture de session</h4>";
                document.getElementById('alertSession').querySelector('.modal-body').innerHTML = "<p>La session est terminée. Vous allez être rediriger vers la page de connexion.</p>";
                document.getElementById('alertSession').querySelector('.modal-footer').innerHTML ='<a href="/security/login" class="btn btn-sm btn-outline-primary" data-bs-dismiss="modal"> Suivant</a>';

                let modalAlert = document.getElementById('alertSession');
                modalAlert.addEventListener('hidden.bs.modal', function(){
                    window.location.replace('/security/login');
                });
            }
        })
        .catch(function (error){
            console.log(error);
        });
}
