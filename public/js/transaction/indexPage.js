// Déclaration des constructeurs
const btnModalTransaction = document.getElementById('btnModalTransaction');
const modalTransaction = document.getElementById('modalTransaction');
const btnModalSubmit = document.getElementById('btnModalSubmit');

modalTransaction.addEventListener('show.bs.modal', function (event) {
    let a = event.relatedTarget;
    let url = a.href;
    let submit = modalTransaction.querySelector('#btnModalSubmit');
    submit.href = url;
    modalTransaction.querySelector('#btnModalSubmit').addEventListener('click', delTransaction);
});

document.querySelectorAll('a.transClosed').forEach(function(link){
    link.addEventListener('click', closedFolder);
});

function delTransaction(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            if(response.data.accessAdmin === true)
            {
                document.getElementById('liste').innerHTML = response.data.liste;
                document.getElementById('ownliste').innerHTML = response.data.liste;
            }else{
                document.getElementById('ownliste').innerHTML = response.data.liste;
            }
            btnModalSubmit.addEventListener('click', delTransaction);
            btnModalSubmit.addEventListener('click', delTransaction);
            document.querySelectorAll('a.transClosed').forEach(function(link){
                link.addEventListener('click', closedFolder);
            });
        })
        .catch(function(error){
            console.log(error);
        });
}

function closedFolder(event){
    event.preventDefault();
    let url = this.href;
    axios
        .get(url)
        .then(function(response){
            if(response.data.accessAdmin === true)
            {
                document.getElementById('liste').innerHTML = response.data.liste;
                document.getElementById('ownliste').innerHTML = response.data.liste;
            }else{
                document.getElementById('ownliste').innerHTML = response.data.liste;
            }
            btnModalSubmit.addEventListener('click', delTransaction);
            document.querySelectorAll('a.transClosed').forEach(function(link){
                link.addEventListener('click', closedFolder);
            });
        })
        .catch(function(error){
            console.log(error);
        });
}

function AllEvents(){

}

