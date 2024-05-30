const modalDisClosed = document.getElementById('modalDisClosed');
const modalDisclosedBS = new boostrap.Modal(document.getElementById('modalClosed'));

if(document.querySelectorAll('.modalDisClosed') !== null){
    document.querySelectorAll('.modalDisClosed').forEach(function(link){
        link.addEventListener('click', showDisclosed);
    });
}

function showDisclosed(event){
    event.preventDefault();
    let a = event.currentTarget;
    let url = this.href;
    axios
        .post(url)
        .then(function(response){
            document.getElementById('listClosed').innerHTML = response.data.listClosed;
        })
        .catch(function(error){
            console.log(error);
        });
}

function reloadClosedProperty(){

}
