import axios from 'axios';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/toaster";
import {zipcode, removeOptions, change_selectcity, formatDate} from "../../../components/common";
import flatpickr from "flatpickr";


export function initShowTransactionPage() {

    const modalEl = document.getElementById('modal');
    if (!modalEl) return;
    const modalBs = new bootstrap.Modal(modalEl);

    let btnSubmitModal = document.getElementById('btnModalSubmit');

    let btnsOpenModal = document.querySelectorAll('.openModal');

    /** reset modal automatique après fermeture */
    modalEl.addEventListener('hidden.bs.modal', () => {
        const deleteUrl = modal.dataset.deleteUrl;
        if (deleteUrl) {
            axios.post(deleteUrl)
                .then(() => console.log('Entité temporaire supprimée'))
                .catch(err => console.error('Erreur lors de la suppression', err));

            // Nettoyage de la valeur
            delete modal.dataset.deleteUrl;
        }

        modalEl.querySelector('.modal-dialog').classList.remove('modal-lg', 'modal-xl');
        modalEl.querySelector('.modal-body').innerHTML = `
              <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading…</span>
                </div>
              </div>`;
        const confirmBtn = modalEl.querySelector('.modal-footer a');
        confirmBtn.textContent = 'Ajouter';
        confirmBtn.href = '#';
    });

    function openModalXl(e) {
        e.preventDefault();
        let a = e.currentTarget;
        let url = a.href;
        const [crud, contentTitle, option] = a.dataset.bsData.split('-');

        modalEl.querySelector('.modal-title').textContent = contentTitle;
        if (['ADDBUYERS', 'EDITBUYERS'].includes(crud)) {
            modalEl.querySelector('.modal-dialog').classList.add('modal-xl');
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter l\'acheteur';
                    confirmBtn.href = url;
                    modalEl.dataset.deleteUrl = data.deleteUrl;

                    const typeClient = document.getElementById('customer_typeClient');
                    if (typeClient.value === "professionnel") {
                        document.getElementById("box_professionnel").classList.remove('d-none');
                        document.getElementById("box_particulier").classList.add('d-none');
                    }

                    typeClient.addEventListener('change', function (event) {
                        if (typeClient.value === "professionnel") {
                            document.getElementById("box_professionnel").classList.remove('d-none');
                            document.getElementById("box_professionnel").classList.add('animate__animated', 'animate__fadeIn');
                        } else {
                            document.getElementById("box_professionnel").classList.add('d-none');
                            document.getElementById("box_professionnel").classList.remove('animate__animated', 'animate__fadeIn');
                        }
                        if (typeClient.value === "particulier") {
                            document.getElementById("box_particulier").classList.remove('d-none');
                            document.getElementById("box_particulier").classList.add('animate__animated', 'animate__fadeIn');
                        } else {
                            document.getElementById("box_particulier").classList.add('d-none');
                            document.getElementById("box_particulier").classList.remove('animate__animated', 'animate__fadeIn');
                        }
                    });

                    // block pour interagir sur la civilité
                    if (document.querySelector('input[name=customer\\[civility\\]]:checked').value > 1) {
                        console.log("ok");
                        document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
                    }
                    const radioCustomerButtons = document.querySelectorAll('input[name=customer\\[civility\\]]');
                    radioCustomerButtons.forEach(function (radio) {
                        radio.addEventListener("change", function () {
                            if (parseInt(this.value) === 2) {
                                document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
                            } else if (parseInt(this.value) === 1) {
                                document.getElementById('customer_maidenName').parentElement.classList.add('d-none');
                            }
                        });
                    });
                    if (document.querySelector('input[name=customer_resp\\[civility\\]]:checked').value > 1) {
                        document.getElementById('customer_maidenName').parentElement.classList.remove('d-none');
                    }
                    const radioRespButtons = document.querySelectorAll('input[name=customer_resp\\[civility\\]]');
                    radioRespButtons.forEach(function (radio) {
                        radio.addEventListener("change", function () {
                            if (parseInt(this.value) === 2) {
                                document.getElementById('customer_resp_maidenName').parentElement.classList.remove('d-none');
                            } else if (parseInt(this.value) === 1) {
                                document.getElementById('customer_resp_maidenName').parentElement.classList.add('d-none');
                            }
                        });
                    });

                    // Variables liés aux modifications des champs du bloc adresse.
                    let customer_commune = document.getElementById('customer_city');
                    let customer_zipcode = document.getElementById('customer_zipcode');
                    let customer_selectcity = document.getElementById('customer_selectcity');
                    let customer_addresseInput = document.getElementById('customer_adress');
                    let customer_proCity = document.getElementById('customer_proCity');
                    let customer_proZipcode = document.getElementById('customer_proZipcode');
                    let customer_proSelectcity = document.getElementById('customer_proSelectcity');
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
                    declareEvent();
                });
        }
        else if (crud === 'ADDAPPOINTMENT' || crud === 'EDITAPPOINTMENT') {
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    let dateinput = document.getElementById('form_appointment');
                    formatDate(dateinput);
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter le rendez-vous';
                    confirmBtn.href = url;
                })
            ;
        }
        else if (crud === 'ADDDOCUMENTS' || crud === 'EDITDOCUMENTS'){
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter le rendez-vous';
                    confirmBtn.href = url;
                })
            ;
        }
        else if (crud === 'ADDINVOICES' || crud === 'EDITINVOICES'){
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter la facture';
                    confirmBtn.href = url;
                })
            ;
        }
        else if (crud === 'DELAPPOINTMENT') {
            modalEl.querySelector('.modal-body').innerHTML =
                "<p class='mb-0'>Attention, vous êtes sur le point de supprimer ce RDV.</p>";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = 'Valider le document';
            confirmBtn.href = url;
            modalEl.dataset.option = option;
            declareEvent();
        }
        else if (crud === 'DELDOCUMENTS'){
            modalEl.querySelector('.modal-body').innerHTML =
                "<p class='mb-0'>Attention, vous êtes sur le point de supprimer ce document.</p>";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = 'Supprimer le document';
            confirmBtn.href = url;
            modalEl.dataset.option = option;
            declareEvent();
        }
        else if (crud === 'Del_Buyers') {
            modalEl.querySelector('.modal-body').innerHTML =
                "<p class='mb-0'>Attention, vous êtes sur le point de supprimer cet acheteur de la vente.</p>";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = 'Suppression';
            confirmBtn.href = url;
            declareEvent();
        }
        else if (crud === 'SHOWFILE') {
            modalEl.querySelector('.modal-body').innerHTML =
                "";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = 'Je valide le document';
            confirmBtn.href = url;
            modalEl.dataset.option = 'validFiles';
            declareEvent();
        }

        modalBs.show();
        declareEvent();
    }

    function submitModal(e) {
        e.preventDefault();

        const listForm = ['formCustomer_add', 'formCustomer_edit', 'formAppointment_add', 'formAppointment_edit', 'formDocuments_add', 'formDocuments_edit', 'formInvoice_add', 'formInvoice_edit'];
        const list = ['dateAtPromise', 'dateAtActe', 'Promise', 'valid'];
        let modalContent = e.currentTarget.parentNode.parentElement;
        let form = modalContent.querySelector('form');
        if (form) {
            let nameForm = form.id;
            let action = form.action;
            let data = new FormData(form);
            if (listForm.includes(nameForm)) {
                console.log('formulaire présent.');
                axios
                    .post(action, data)
                    .then(function ({data}) {
                        if(nameForm === 'formAppointment_add' || nameForm === 'formAppointment_edit'){
                            console.log('Date');
                            document.getElementById('Block_Appointment').innerHTML = data.view;
                            document.getElementById('stateTransaction').innerHTML = data.state;
                        }else if(nameForm === 'formCustomer_add' || nameForm === 'formCustomer_edit'){
                            console.log('Acheteurs');
                            delete modal.dataset.deleteUrl;
                            document.getElementById('Block_Buyers').innerHTML = data.view;
                            document.getElementById('stateTransaction').innerHTML = data.state;
                        }else if(nameForm === 'formDocuments_add' || nameForm === 'formDocuments_edit'){
                            console.log('Documents');
                            document.getElementById('Block_Documents').innerHTML = data.view;
                            document.getElementById('stateTransaction').innerHTML = data.state;
                        }
                        else if(nameForm === 'formInvoice_add' || nameForm === 'formInvoice_edit'){
                            console.log('Invoice');
                            document.getElementById('Block_Invoices').innerHTML = data.view;
                            document.getElementById('stateTransaction').innerHTML = data.state;
                        }
                        toasterMessage(data.message);
                        declareEvent();
                    })
                    .catch(function (error) {
                        console.log('error', error);
                    })
                ;
            }
            modalBs.hide();
        }
        else {
            let option = modalEl.dataset.option;
            if (option !== null && (option === 'dateAtActe' || option === 'dateAtPromise')){
                axios
                    .post(btnSubmitModal.href)
                    .then(({data}) => {
                        document.getElementById('Block_Appointment').innerHTML = data.view;
                        document.getElementById('stateTransaction').innerHTML = data.state;
                        toasterMessage(data.message);
                        declareEvent();
                    })
                ;
                modalBs.hide();
            }else if(option !== null && (option === 'Prom' || option === 'Ac' || option === 'Tf')){
                axios
                    .post(btnSubmitModal.href)
                    .then(({data}) => {
                        document.getElementById('Block_Documents').innerHTML = data.view;
                        document.getElementById('stateTransaction').innerHTML = data.state;
                        toasterMessage(data.message);
                        declareEvent();
                    })
                ;
                modalBs.hide();
            }else if(option !== null && (option === 'Ho' || option === 'Fv' || option === 'Fcoll')){
                axios
                    .post(btnSubmitModal.href)
                    .then(({data}) => {
                        document.getElementById('Block_Invoices').innerHTML = data.view;
                        document.getElementById('stateTransaction').innerHTML = data.state;
                        toasterMessage(data.message);
                        declareEvent();
                    })
                ;
                modalBs.hide();
            }
            else{
                axios
                    .post(btnSubmitModal.href)
                    .then(({data}) => {
                        document.getElementById('Block_Buyers').innerHTML = data.view;
                        document.getElementById('stateTransaction').innerHTML = data.state;
                        toasterMessage(data.message);
                        declareEvent();
                    })
                ;
                modalBs.hide();
            }

        }
    }

    function declareEvent() {
        let btnSubmitModal = document.getElementById('btnModalSubmit');
        let btnsOpenModal = document.querySelectorAll('.openModal');
        /** ouverture */
        btnsOpenModal.forEach((btn) => {
            btn.addEventListener('click', openModalXl);
        });
        /** validation depuis le bouton du footer */
        if (btnSubmitModal) {
            btnSubmitModal.addEventListener('click', submitModal);
        }
    }

    declareEvent();
}