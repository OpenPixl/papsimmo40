export function estimate(response){
    let sales = document.getElementById('sale');
    let rent = document.getElementById('rent');
    let rentCommerce = document.getElementById('rentCommerce');

    if(response.data.data[0] === 4 && response.data.data[1] === 8) {
        let warranty = document.getElementById('warrantyDeposit');
        sales.classList.add('d-none');
        rent.classList.add('d-none');
        if(warranty !== null){
            document.getElementById('warrantyDeposit').remove();
        }
    }else if (response.data.data[0] === 5) {
        let warranty = document.getElementById('warrantyDeposit');
        sales.classList.add('d-none');
        rentCommerce.classList.add('d-none');
        if(warranty !== null){
            document.getElementById('warrantyDeposit').remove();
        }
    }else{
        rent.classList.add('d-none');
        rentCommerce.classList.add('d-none');
    }

    const tsdiagChoice = new TomSelect("#property_step2_diagChoice",TsSimple);
    const diagChoice = document.getElementById('property_step2_diagChoice');
    // Affichage des inputs DPE et GPE dès le chargement de la page
    if(diagChoice.value === 'non_obligatoire' || diagChoice.value === 'vierge' ){
        document.getElementById('block_dpeAt').className += " d-none";
        document.getElementById('block_diagDpeResult').className += " d-none";
        document.getElementById('block_diagGesResult').className += " d-none";
        document.getElementById('block_estimation').className += " d-none";
    }
    // Affichage des inputs DPE et GPE sur le changement de valeur
    const changeTsDiag = function(){
        let value = tsdiagChoice.getValue();
        if(value === 'obligatoire'){
            document.getElementById('block_dpeAt').className = "row mb-1 mt-1 g-1";
            document.getElementById('block_diagDpeResult').className = "row mb-1 mt-1 g-1";
            document.getElementById('block_diagGesResult').className = "row mb-1 mt-1 g-1";
            document.getElementById('block_estimation').className = "row mb-1 mt-1 g-1";

        }else if(value === 'non_obligatoire' || value === 'vierge' ){
            document.getElementById('block_dpeAt').className += " d-none";
            document.getElementById('block_diagDpeResult').className += " d-none";
            document.getElementById('block_diagGesResult').className += " d-none";
            document.getElementById('block_estimation').className = " d-none";
        }
    };
    tsdiagChoice.on('change', changeTsDiag );
    calculatePrices(document.getElementById('property_step2_price'),document.getElementById('property_step2_honoraires'), document.getElementById('property_step2_priceFai'));
}

export function calculatePrices(price, honoraires, priceFai){
    price.addEventListener('change', function () {
        let priceValue = parseInt(price.value);
        let honorairesValue = parseInt(honoraires.value);
        priceFAI.value = priceValue + honorairesValue;
    });
    honoraires.addEventListener('change', function () {
        let priceValue = parseInt(price.value);
        let honorairesValue = parseInt(honoraires.value);
        priceFAI.value = priceValue + honorairesValue;
    });
}