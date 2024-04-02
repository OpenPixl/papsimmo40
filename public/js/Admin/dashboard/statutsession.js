const alertSession = new bootstrap.Modal(document.getElementById('alertSession'));
setInterval(statutSession, 30000);

function statutSession()
{
    axios
        .get('/opadmin/dashboard/sessionstatut')
        .then(function (response){
            let timeless = response.data.timeless;
            if(timeless > 605 && timeless < 595){
                console.log(timeless);
                alertSession.show();
                document.getElementById('alertSession').querySelector('.modal-body').innerHTML = "<p>Il vous reste moins de 10 min avant de devoir vous reconnecter.</p>";
            }else if(timeless > 310 && timeless < 290){
                alertSession.show();
                document.getElementById('alertSession').querySelector('.modal-body').innerHTML = "<p>Il vous reste moins de 5 min avant de devoir vous reconnecter.</p>";
            }else if(timeless < 0){
                console.log(alertSession);
                alertSession.show();
                document.getElementById('alertSession').querySelector('.modal-body').innerHTML = "<p>La session est terminée. Vous allez être rediriger vers la page de connexion.</p>";
                //window.Location.replace('/security/login');
            }
        })
        .catch(function (error){
            console.log(error);
        });
}
