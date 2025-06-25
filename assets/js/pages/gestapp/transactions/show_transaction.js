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
        modalEl.querySelector('.modal-footer').innerHTML = '\n' +
            '<a id="btnModalSubmit" href="#" type="button" class="btn btn-sm btn-primary">Ajouter</a>\n' +
            '<button type="button" class="btn btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>';

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
        else if (crud === 'DELBUYERS') {
            modalEl.querySelector('.modal-body').innerHTML =
                "<p class='mb-0'>Attention, vous êtes sur le point de supprimer cet acheteur de la vente.</p>";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = 'Suppression';
            confirmBtn.href = url;
            declareEvent();
        }
        else if (crud === 'SHOWFILE') {
            modalEl.querySelector('.modal-dialog').classList.add('modal-xl');
            modalEl.querySelector('.modal-body').innerHTML = '<iframe src="" width="100%" height="500px"></iframe>';
            axios.get(url).then(({data}) => {
                    modalEl.querySelector('.modal-body iframe').src = data.path;
                });
            const footer = modalEl.querySelector('.modal-footer');
            const confirmBtn = footer.querySelector('a');
            confirmBtn.textContent = 'Je valide ce document';
            confirmBtn.href = url;
            modalEl.dataset.option = "validFile";

            // Nouveau lien pour invalider le document
            const invalidateBtn = document.createElement('a');
            invalidateBtn.textContent = 'J\'invalide ce document';
            invalidateBtn.href = url ; // adapte l’URL si nécessaire
            invalidateBtn.classList.add('btn', 'btn-sm','btn-danger', 'ms-2');
            invalidateBtn.addEventListener('click', function (e) {
                e.preventDefault();
                modalEl.dataset.option = "invalidFile";
                // 1. Préparer le formulaire de refus
                modalEl.querySelector('.modal-body').innerHTML = `
                    <div class="mb-3">
                        <label for="refuseMessage" class="form-label">Motif du refus :</label>
                        <textarea id="refuseMessage" class="form-control" rows="4" placeholder="Expliquez pourquoi ce document est refusé..."></textarea>
                    </div>
                `;

                // 2. Mettre à jour le footer
                const footer = modalEl.querySelector('.modal-footer');
                footer.innerHTML = ''; // on vide le footer pour mettre les nouveaux boutons

                // Bouton d'annulation (retour à l'aperçu du document)
                const backBtn = document.createElement('button');
                backBtn.textContent = 'Retour au document proposé';
                backBtn.classList.add('btn', 'btn-sm', 'btn-outline-dark');
                backBtn.addEventListener('click', () => {
                    // Recharger la vue d'origine avec le document
                    // Simule à nouveau le comportement de SHOWFILE
                    modalEl.querySelector('.modal-body').innerHTML = '<iframe src="" width="100%" height="500px"></iframe>';
                    axios.get(url).then(({ data }) => {
                        modalEl.querySelector('.modal-body iframe').src = data.path;
                    });

                    footer.innerHTML = ''; // On vide à nouveau le footer
                    footer.appendChild(confirmBtn); // on remet le bouton de validation
                    footer.appendChild(invalidateBtn); // on remet le bouton de refus
                    footer.appendChild(closeBtn); // on remet le bouton de fermeture
                });

                // Bouton d'envoi du refus
                const sendBtn = document.createElement('button');
                sendBtn.textContent = 'Envoyer le message';
                sendBtn.classList.add('btn', 'btn-sm', 'btn-danger');
                sendBtn.addEventListener('click', () => {
                    const message = modalEl.querySelector('#refuseMessage').value;
                    let option = modalEl.dataset.option;
                    let data = {'option': option, 'message':message};
                    axios.post(url, data )
                        .then(() => {
                            bootstrap.Modal.getInstance(modalEl).hide();
                            toasterMessage('Le message a été envoyé au mandataire.');
                        })
                        .catch(err => {
                            console.error('Erreur lors du refus du document', err);
                            alert("Erreur lors de l'envoi du refus.");
                        });
                });

                // Bouton de fermeture
                const closeBtn = document.createElement('button');
                closeBtn.textContent = 'Annuler';
                closeBtn.classList.add('btn', 'btn-sm', 'btn-secondary');
                closeBtn.setAttribute('type', 'button');
                closeBtn.setAttribute('data-bs-dismiss', 'modal');

                // Ajouter les boutons
                footer.appendChild(backBtn);
                footer.appendChild(sendBtn);
                footer.appendChild(closeBtn);
            });

            // Insertion juste après le bouton de validation
            if (confirmBtn.nextSibling) {
                footer.insertBefore(invalidateBtn, confirmBtn.nextSibling);
            } else {
                footer.appendChild(invalidateBtn); // fallback
            }


            declareEvent();
        }

        modalBs.show();
        declareEvent();
    }

    function submitModal(e) {
        e.preventDefault();
        delete modal.dataset.deleteUrl;

        const listForm = ['formCustomer_add', 'formCustomer_edit', 'formAppointment_add', 'formAppointment_edit', 'formDocuments_add', 'formDocuments_edit', 'formInvoice_add', 'formInvoice_edit'];
        const list = ['dateAtPromise', 'dateAtActe', 'Promise', 'valid'];
        let modalContent = e.currentTarget.parentNode.parentElement;
        let form = modalContent.querySelector('form');
        if (form) {
            let nameForm = form.id;
            let action = form.action;
            let data = new FormData(form);
            if (listForm.includes(nameForm)) {
                axios
                    .post(action, data)
                    .then(function ({data}) {
                        if(nameForm === 'formAppointment_add' || nameForm === 'formAppointment_edit'){
                            updateTransactionView({
                                viewTargetId: 'Block_Appointment',
                                view: data.view,
                                state: data.state,
                                progress: data.progress,
                                actionButtons: data.actionButtons,
                                message: data.message
                            });
                        }
                        else if(nameForm === 'formCustomer_add' || nameForm === 'formCustomer_edit'){
                            updateTransactionView({
                                viewTargetId: 'Block_Buyers',
                                view: data.view,
                                state: data.state,
                                progress: data.progress,
                                actionButtons: data.actionButtons,
                                message: data.message
                            });

                        }
                        else if(nameForm === 'formDocuments_add' || nameForm === 'formDocuments_edit'){
                            updateTransactionView({
                                viewTargetId: 'Block_Documents',
                                view: data.view,
                                state: data.state,
                                progress: data.progress,
                                actionButtons: data.actionButtons,
                                message: data.message
                            });
                        }
                        else if(nameForm === 'formInvoice_add' || nameForm === 'formInvoice_edit'){
                            updateTransactionView({
                                viewTargetId: 'Block_Invoices',
                                view: data.view,
                                state: data.state,
                                progress: data.progress,
                                actionButtons: data.actionButtons,
                                message: data.message
                            });
                        }
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
            let url = e.currentTarget.href;
            // Soumission du formulaire pour signature de document
            if (option !== null && (option === 'dateAtActe' || option === 'dateAtPromise')){
                delete modal.dataset.option;
                axios
                    .post(url)
                    .then(({data}) => {
                        updateTransactionView({
                            viewTargetId: 'Block_Appointment',
                            view: data.view,
                            state: data.state,
                            progress: data.progress,
                            actionButtons: data.actionButtons,
                            message: data.message
                        });
                    })
                ;

                modalBs.hide();
            }
            // Suppression d'un document de vente présent dans le dossier
            else if(option !== null && (option === 'Prom' || option === 'Ac' || option === 'Tf')){
                axios
                    .post(url)
                    .then(({data}) => {
                        delete modal.dataset.option;
                        updateTransactionView({
                            viewTargetId: 'Block_Documents',
                            view: data.view,
                            state: data.state,
                            progress: data.progress,
                            actionButtons: data.actionButtons,
                            message: data.message
                        });
                    })
                ;
                modalBs.hide();
            }
            // Suppression d'une facturation présente dans le dossier
            else if(option !== null && (option === 'Ho' || option === 'Fa' || option === 'Fcoll')){
                axios
                    .post(url)
                    .then(({data}) => {
                        delete modal.dataset.option;
                        updateTransactionView({
                            viewTargetId: 'Block_Invoices',
                            view: data.view,
                            state: data.state,
                            progress: data.progress,
                            actionButtons: data.actionButtons,
                            message: data.message
                        });
                    })
                ;
                modalBs.hide();
            }
            // procédure de validation d'un document fournis par le mandataire
            else if(option !== null && option === 'validFile') {
                let data = { 'option' : option};
                axios
                    .post(url, data)
                    .then(({data})=> {
                        delete modal.dataset.option;
                        updateTransactionView({
                            viewTargetId: data.blockId,
                            view: data.view,
                            state: data.state,
                            progress: data.progress,
                            actionButtons: data.actionButtons,
                            message: data.message
                        });
                    })
                ;
                modalBs.hide();
            }
            else{
                axios
                    .post(url)
                    .then(({data}) => {
                        updateTransactionView({
                            viewTargetId: 'Block_Buyers',
                            view: data.view,
                            state: data.state,
                            progress: data.progress,
                            actionButtons: data.actionButtons,
                            message: data.message
                        });
                    })
                ;
                modalBs.hide();
            }
        }
    }


    function updateTransactionView({viewTargetId, view, state, message, progress, actionButtons}) {
        // Bloc principal à modifier
        if (viewTargetId && view) {
            document.getElementById(viewTargetId).innerHTML = view;
        }
        // Bloc Ligne de suivi des consignes
        if (state !== undefined) {
            document.getElementById('stateTransaction').innerHTML = state;
        }
        // Bloc des informations de la cardInformation
        if (progress !== undefined) {
            document.getElementById('blockInformation').innerHTML = progress;
        }
        // Bloc des actions sur la page
        if (actionButtons !== undefined) {
            document.getElementById('block_buttons').innerHTML = actionButtons;
        }
        // tosater message
        if (message) {
            toasterMessage(message);
        }
        declareEvent(); // réappliquer les événements
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