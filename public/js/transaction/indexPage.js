// Déclaration des constructeurs
const btnModalTransaction = document.getElementById('btnModalTransaction');
const modalTransaction = document.getElementById('modalTransaction');
const btnModalSubmit = document.getElementById('btnModalSubmit');

modalTransaction.addEventListener('show.bs.modal', function (event) {
    let url = btnModalTransaction.href;
    let modalSubmit = modalTransaction.querySelector('.modal-footer a');
    modalSubmit.href = url;
});

function delTransaction(event){
    event.preventDefault();
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('liste').innerHTML = response.data.liste;
            document.getElementById('ownliste').innerHTML = response.data.liste;
        })
        .catch(function(error){
            console.log(error);
        });
}
btnModalSubmit.addEventListener('click', delTransaction);
