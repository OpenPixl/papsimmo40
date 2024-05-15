const typeClient = document.getElementById('customer_typeClient');

if(typeClient.value === "professionnel"){
    document.getElementById("rowStructure").classList.remove('d-none');
    document.getElementById('kbis').classList.remove('d-none');
}

typeClient.addEventListener('change', function(event){
    if(typeClient.value === "professionnel"){
        document.getElementById("rowStructure").classList.remove('d-none');
        document.getElementById('kbis').classList.remove('d-none');
        document.getElementById("rowStructure").classList.add('animate__animated', 'animate__fadeIn');
        document.getElementById('kbis').classList.add('animate__animated', 'animate__fadeIn');
    }else{
        document.getElementById("rowStructure").classList.add('d-none');
        document.getElementById('kbis').classList.add('d-none');
        document.getElementById("rowStructure").classList.remove('animate__animated', 'animate__fadeIn');
        document.getElementById('kbis').classList.remove('animate__animated', 'animate__fadeIn');
    }
});

