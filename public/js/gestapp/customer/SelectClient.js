const typeClient = document.getElementById('customer_typeClient');

if(typeClient.value === "professionnel"){
    document.getElementById("rowStructure").classList.remove('d-none');
    document.getElementById("kbis").classList.remove('d-none');
}

typeClient.addEventListener('change', function(event){
    if(document.getElementById("rowStructure").classList.contains('d-none')){
        document.getElementById("rowStructure").classList.remove('d-none');
        document.getElementById("kbis").classList.remove('d-none');
    }else{
        document.getElementById("rowStructure").classList.add('d-none');
        document.getElementById("kbis").classList.add('d-none');
    }
});