// Déclaration des constructeurs
const btnModalTransaction = document.getElementById('btnModalTransaction');
const modalTransaction = document.getElementById('modalTransaction');
const btnModalSubmit = document.getElementById('btnModalSubmit');

modalTransaction.addEventListener('show.bs.modal', function (event) {
    let a = event.relatedTarget;
    let url = a.href;
    let submit = modalTransaction.querySelector('#btnModalSubmit');
    submit.href = url;
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
            }else{
                document.getElementById('ownliste').innerHTML = response.data.liste;
            }
            btnModalSubmit.addEventListener('click', delTransaction);
        })
        .catch(function(error){
            console.log(error);
        });
}

btnModalSubmit.addEventListener('click', delTransaction);
