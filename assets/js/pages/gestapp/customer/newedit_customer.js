import axios from 'axios';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/bootstrap/toaster";
import {zipcode, removeOptions, change_selectcity, formatDate} from "../../../components/appli/common";
import {typeClient, civilityChoice} from "../../../components/appli/customer";

export function initNewEditCustomerPage() {

    function submitCustomer(event){
        event.preventDefault();
        let form = document.getElementById('FormEditCustomer');
        let action = form.action;
        let data = new FormData(form);
        axios
            .post(action, data)
            .then(function(response){
                if(response.data.code === 422){
                    document.getElementById('form').innerHTML = response.data.formView;
                    toasterMessage(response.data.message);
                    loadEvent();
                }else{
                    toasterMessage(response.data.message);
                    loadEvent();
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
        loadEvent();
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
        loadEvent();
    }

    function loadEvent(){

        const btnAddCustomer = document.getElementById('btnAddCustomer');
        const btnAddResp = document.getElementById('btnAddResp');
        // Variables liés aux modifications des champs du bloc adresse.
        let customer_commune = document.getElementById('customer_city');
        let customer_zipcode = document.getElementById('customer_zipcode');
        let customer_selectcity = document.getElementById('customer_selectcity');
        let customer_addresseInput = document.getElementById('customer_adress');
        let customer_proCity = document.getElementById('customer_proCity');
        let customer_proZipcode = document.getElementById('customer_proZipcode');
        let customer_proSelectcity = document.getElementById('customer_proSelectcity');

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
    }
    loadEvent();
}