const searchCustomerform = document.getElementById('searchCustomerform');
const searchCustomerInput = document.getElementById('searchCustomerInput');
let liste = document.getElementById('liste')

searchCustomerInput.addEventListener('input', function(event){
    if(searchCustomerInput.value.length >= 2){
        document.getElementById('liste').innerHTML = '';
        let action = searchCustomerform.action;
        let value = searchCustomerInput.value;
        axios
            .post(action, {'word': value})
            .then(function(response){
                document.getElementById('liste').innerHTML = response.data.liste;
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }
});