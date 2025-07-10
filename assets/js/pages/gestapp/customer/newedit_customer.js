import axios from 'axios';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/bootstrap/toaster";
import {
    zipcode,
    removeOptions,
    change_selectcity,
    formatDate,
    useTomSelect,
    initializeTinyMCE
} from "../../../components/appli/common";
import {typeClient, civilityChoice} from "../../../components/appli/customer";

export function initNewEditCustomerPage() {

    const modalEl = document.getElementById('modal');
    if (!modalEl) return;
    const modalBs = new bootstrap.Modal(modalEl);

    function submitCustomer(event){
        event.preventDefault();
        let form = document.getElementById('FormEditCustomer');
        let action = form.action;
        let data = new FormData(form);
        axios
            .post(action, data)
            .then(function(response){
                if(response.data.code === 422){
                    document.getElementById('listeResearch').innerHTML = response.data.liste;
                    toasterMessage(response.data.message);
                    declareEvent();
                }else{
                    toasterMessage(response.data.message);
                    declareEvent();
                }
            })
            .catch(function(error){
                console.log(error);
            })
        ;
    }

    function addResponsable(event){
        event.preventDefault();
        let form = document.getElementById('AddRespStructure');
        let action = form.action;
        let data = new FormData(form);
        axios
            .post(action, data)
            .then(function(response){
                document.getElementById('liste_respcustomer').innerHTML = response.data.listeResp;
                toasterMessage(response.data.message);
                form.reset();
            })
            .catch(function(error){
                console.log(error);
            })
        ;
        declareEvent();
    }

    function dellResponsable(event){
        event.preventDefault();
        let url = this.href;
        axios
            .post(url)
            .then(function(response){
                document.getElementById('liste_respcustomer').innerHTML = response.data.listeResp;
                toasterMessage(response.data.message);
            })
            .catch(function(error){
                console.log(error);
            })
        ;
        declareEvent();
    }

    function openModal(e){
        e.preventDefault();
        let a = e.currentTarget;
        let url = a.href;
        const [crud, contentTitle, option] = a.dataset.bsData.split('-');
        modalEl.querySelector('.modal-title').textContent = contentTitle;
        if (['ADDRESEARCH', 'EDITRESEARCH'].includes(crud)) {
            modalEl.querySelector('.modal-dialog').classList.add('modal-xl');
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Créer la recherche';
                    confirmBtn.href = url;

                    initializeTinyMCE(500);
                    useTomSelect('.oneChoice', 'Simple');
                    useTomSelect('.multiChoice', 'Multi');

                    declareEvent();
                });
            modalBs.show();
        }

    }

    function submitModal(e){
        e.preventDefault();
        let modalContent = e.currentTarget.parentNode.parentElement;
        let form = modalContent.querySelector('form');
        let nameForm = form.id;
        let action = form.action;
        let data = new FormData(form);
        axios
            .post(action, data)
            .then(function ({data}) {
                document.getElementById('listeResearch').innerHTML = data.liste;
                toasterMessage(data.message);
                declareEvent();
            })
        ;
        modalBs.hide();
    }

    function declareEvent(){
        const btnAddCustomer = document.getElementById('btnAddCustomer');
        const btnAddResp = document.getElementById('btnAddResp');
        const btnsOpenModal = document.querySelectorAll('.btnOpenModal');
        const btnsModalSubmit = document.querySelectorAll('.btnModalSubmit');
        // Variables liés aux modifications des champs du bloc adresse.
        let customer_commune = document.getElementById('customer_city');
        let customer_zipcode = document.getElementById('customer_zipcode');
        let customer_selectcity = document.getElementById('customer_selectcity');
        let customer_addresseInput = document.getElementById('customer_adress');
        let customer_proCity = document.getElementById('customer_proCity');
        let customer_proZipcode = document.getElementById('customer_proZipcode');
        let customer_proSelectcity = document.getElementById('customer_proSelectcity');

        console.log(btnsModalSubmit);

        typeClient();
        civilityChoice();

        if (customer_commune && customer_addresseInput) {
            customer_zipcode.addEventListener('input', function (event) {
                zipcode(customer_zipcode, customer_commune, customer_selectcity);
            });
            customer_selectcity.addEventListener('change', function (event) {
                change_selectcity(customer_zipcode, customer_commune, customer_selectcity);
            });
            customer_proZipcode.addEventListener('input', function (event) {
                zipcode(customer_proZipcode, customer_proCity, customer_proSelectcity);
            });
            customer_proSelectcity.addEventListener('change', function (event) {
                change_selectcity(customer_proZipcode, customer_proCity, customer_proSelectcity);
            });
        }

        btnAddCustomer.addEventListener('click', submitCustomer);
        btnAddResp.addEventListener('click', addResponsable);
        let btnSupprResps = document.querySelectorAll('.btnSupprResp');
        btnSupprResps.forEach(function(link){
            link.addEventListener('click', dellResponsable);
        });
        btnsOpenModal.forEach(function(link){
            link.addEventListener('click', openModal);
        });
        btnsModalSubmit.forEach(function(link){
            link.addEventListener('click', submitModal);
        });
    }
    declareEvent();
}