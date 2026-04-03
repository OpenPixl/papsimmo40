import axios from 'axios';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/bootstrap/toaster";
import {zipcode, removeOptions, change_selectcity, formatDate} from "../../../components/appli/common";
import {typeClient, civilityChoice} from "../../../components/appli/customer";
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
            '<a href="#" type="button" class="btn btn-sm btn-primary btnModalSubmit">Ajouter</a>\n' +
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
            modalEl.querySelector('.modal-footer a').textContent = 'Modifier l\'acheteur';
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter l\'acheteur';
                    confirmBtn.href = url;
                    modalEl.dataset.deleteUrl = data.deleteUrl;

                    typeClient();
                    civilityChoice();
                    let dateinputddn = document.getElementById('customer_ddn');
                    formatDate(dateinputddn);

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
        else if (crud === 'SEARCHBUYERS'){
            modalEl.querySelector('.modal-footer a').classList.add('d-none');
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    let inputSearchCustomer = document.getElementById('search_customer_property_firstName');
                    inputSearchCustomer.addEventListener('input', submitSearchCustomer);
                })
                .catch(error => { console.log(error); })
            ;
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
        else if (crud === 'ADDAVENANT'){
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-dialog').classList.add('modal-lg');
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter l\'avenant';
                    confirmBtn.href = url;
                })
            ;
        }
        else if (crud === 'ADDCOLLAB'){
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter le collaborateur au dossier';
                    confirmBtn.href = url;
                })
            ;
        }
        else if (crud === 'ADDAGENCY') {
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter l\'agent au dossier';
                    confirmBtn.href = url;
                })
            ;
        }
        else if (crud === 'ADDCANCELLED' || crud === 'EDITCANCELLED'){
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    const confirmBtn = modalEl.querySelector('.modal-footer a');
                    confirmBtn.textContent = 'Ajouter un document lié à l\'annulation';
                    confirmBtn.href = url;
                })
            ;
        }
        else if (crud === 'DELCOLLAB') {
            modalEl.querySelector('.modal-body').innerHTML =
                "<p class='mb-0'>Attention, vous êtes sur le point de retirer ce collaborateur.</p>";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = 'Retirer le collaborateur';
            confirmBtn.href = url;
            modalEl.dataset.option = option; // donne le nom du support à retirer
            declareEvent();
        }
        else if (crud === 'DELAPPOINTMENT') {
            modalEl.querySelector('.modal-body').innerHTML =
                "<p class='mb-0'>Attention, vous êtes sur le point de supprimer ce RDV.</p>";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = 'Valider le document';
            confirmBtn.href = url;
            modalEl.dataset.option = option; // donne le nom du support à retirer
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
        else if (crud === 'DELDOCSCANCELLED'){
            modalEl.querySelector('.modal-body').innerHTML =
                "<p class='mb-0'>Attention, vous êtes sur le point de supprimer ce document lié à l'annumation.</p>";
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
        else if (crud === 'VIEWDOCS') {
            modalEl.querySelector('.modal-dialog').classList.add('modal-xl');
            modalEl.querySelector('.modal-body').innerHTML = '<iframe src="" width="100%" height="500px"></iframe>';
            axios.get(url).then(({data}) => {
                modalEl.querySelector('.modal-body iframe').src = data.path;
            });
            const footer = modalEl.querySelector('.modal-footer');
            const confirmBtn = footer.querySelector('a');
            confirmBtn.classList.add('d-none');
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
        else if (crud === 'SHOWCANCELLED'){
            modalEl.querySelector('.modal-dialog').classList.add('modal-xl');
            modalEl.querySelector('.modal-body').innerHTML = '<iframe src="" width="100%" height="500px"></iframe>';
            axios.get(url).then(({data}) => {
                modalEl.querySelector('.modal-body iframe').src = data.path;
            });
            const footer = modalEl.querySelector('.modal-footer');
            const confirmBtn = footer.querySelector('a');
            confirmBtn.textContent = 'Je valide ce document';
            confirmBtn.href = url;
            modalEl.dataset.option = "validFileCancelled";

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
        else{
            console.log('Erreur : le type de formulaire n\'est pas reconnu');
        }

        modalBs.show();
        declareEvent();
    }

    function submitModal(e) {
        e.preventDefault();
        delete modal.dataset.deleteUrl;

        const listForm = ['formCustomer_add', 'formCustomer_edit', 'formAppointment_add', 'formAppointment_edit', 'formDocuments_add', 'formDocuments_edit', 'formInvoice_add', 'formInvoice_edit', 'FormAddcollaborator', 'FormAddcollaboratorInvoice', 'formDocsCancelled_add', 'formActe_addAvenant'];
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
                            modalBs.hide();
                        }
                        else if(nameForm === 'formCustomer_add' || nameForm === 'formCustomer_edit'){
                            if(data.code === 422){
                                form.outerHTML = data.formView;
                                toasterMessage(data.message);
                                typeClient();
                                civilityChoice();

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
                            }else{
                                updateTransactionView({
                                    viewTargetId: 'Block_Buyers',
                                    view: data.view,
                                    state: data.state,
                                    progress: data.progress,
                                    actionButtons: data.actionButtons,
                                    message: data.message
                                });
                                modalBs.hide();
                            }
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
                            modalBs.hide();
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
                            modalBs.hide();
                        }
                        else if(nameForm === 'FormAddcollaboratorInvoice'){
                            updateTransactionView({
                                viewTargetId: 'Block_Invoices',
                                view: data.view,
                                state: data.state,
                                progress: data.progress,
                                actionButtons: data.actionButtons,
                                message: data.message
                            });
                            modalBs.hide();
                        }
                        else if(nameForm === 'FormAddcollaborator'){
                            document.getElementById('listCollaborator').innerHTML = data.listCollaborators;
                            modalBs.hide();
                            toasterMessage(data.message);
                            declareEvent();
                        }
                        else if(nameForm === 'formDocsCancelled_add' || nameForm === 'formDocsCancelled_edit'){
                            document.getElementById('Block_Cancelled').innerHTML = data.view;
                            modalBs.hide();
                            declareEvent();
                        }
                        else if(nameForm === 'formActe_addAvenant'){
                            modalBs.hide();
                            declareEvent();
                        }
                    })
                    .catch(function (error) {
                        console.log('error', error);
                    })
                ;
            }
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
            else if(option !== null && (option === 'Ho' || option === 'Fa' || option === 'FColl')){
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
                declareEvent();
            }
            else if(option !== null && option === 'validFileCancelled') {
                let data = { 'option' : option};
                axios
                    .post(url, data)
                    .then(({data})=> {
                        delete modal.dataset.option;
                        document.getElementById('Block_Cancelled').innerHTML = data.view;
                        toasterMessage(data.message);
                        declareEvent();
                    })
                ;
                modalBs.hide();

            }
            else if(option !== null && option === 'supprCollab') {
                axios.post(url).then(function ({data}) {
                    console.log(document.getElementById('listCollaborator'));
                    document.getElementById('listCollaborator').innerHTML = data.listCollaborators;
                    toasterMessage(data.message);
                    delete modal.dataset.option;
                    modalBs.hide();
                })
                ;
                declareEvent();
            }
            else if(option !== null && option === 'Annulation') {
                axios.post(url).then(function ({data}) {
                    document.getElementById('Block_Cancelled').innerHTML = data.view;
                    toasterMessage(data.message);
                    delete modal.dataset.option;
                    modalBs.hide();
                })
                ;
                declareEvent();
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

    // Blocs de code en lien avec la recherche et l'intégration du client dans un dossier de transactions
    function submitSearchCustomer(event){
        event.preventDefault();
        let form = document.getElementById('formSearch_CustomerTransaction');
        let action = form.action;
        let data = new FormData(form);
        axios
            .post(action, data)
            .then(function(response){
                document.getElementById('listeSearchCustomers').innerHTML = response.data.liste;
                toasterMessage(response.data.message);
                let linkAddCustomer = document.querySelectorAll('a.addcustomersearch');
                linkAddCustomer.forEach(function(link){
                    link.addEventListener('click', submitAddSearchCustomer);
                });
            })
            .catch(function(error){
                alert(error);
            })
        ;
    }

    function submitAddSearchCustomer(event){
        event.preventDefault();
        let url = event.currentTarget.href;
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
            .catch(function (error){
                alert(error);
            })
        ;
        modalBs.hide();
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
        let btnsSubmitModal = document.querySelectorAll('.btnModalSubmit');
        let btnsOpenModal = document.querySelectorAll('.openModal');
        /** ouverture */
        btnsOpenModal.forEach((btn) => {
            btn.addEventListener('click', openModalXl);
        });
        /** validation depuis le bouton du footer **/
        btnsSubmitModal.forEach((link) => {
            link.addEventListener('click', submitModal);
        });
    }
    declareEvent();
}