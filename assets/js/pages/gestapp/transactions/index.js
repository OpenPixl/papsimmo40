import * as bootstrap from "bootstrap";
import {toasterMessage} from "../../../components/bootstrap/toaster";
import axios from "axios";

export function initIndexTransactionPage() {
    console.log('Page d\'index des transactions initialisées.');

    const modalEl = document.getElementById('modal');
    if (!modalEl) return;
    const modalBs = new bootstrap.Modal(modalEl);

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

    function openModal(e){
        e.preventDefault();
        let a = e.currentTarget;
        let url = a.href;
        const [crud, contentTitle, option] = a.dataset.bsData.split('-');
        modalEl.querySelector('.modal-title').textContent = contentTitle;

        if(crud === 'ANNTRANSAC'){
            axios
                .get(url)
                .then(({data}) => {
                    modalEl.querySelector('.modal-body').innerHTML = data.formView;
                    let annulation_reason = document.getElementById('annulation_reason');
                    annulation_reason.addEventListener('change', choiceCancelled);
                })
                .catch();
        }else if(crud === 'CLOSEDTRANSAC'){
            modalEl.querySelector('.modal-body').innerHTML = "<b>Attention</b> : Vous allez Cloturer cette transaction. Voulez-vous continuer ? ";
            modalEl.querySelector('.modal-footer a').href = url;
            modalEl.querySelector('.modal-footer a').textContent = "Oui";
        }

        modalBs.show();
        declareEvent();
    }

    function submitModal(e){
        e.preventDefault();
        const listForm = ['form_AnnulationTransaction'];
        let modalContent = e.currentTarget.parentNode.parentElement;
        let form = modalContent.querySelector('form');
        if (form) {
            let nameForm = form.id;
            let action = form.action;
            let data = new FormData(form);
            if (listForm.includes(nameForm)) {
                axios
                    .post(action, data)
                    .then(({data}) => {
                        document.getElementById('liste').innerHTML = data.liste;
                        toasterMessage(data.message);
                        declareEvent();
                    })
                    .catch()
                ;
            }
        }
        else{
            let url = this.href;
            console.log(url);
            axios
                .get(url)
                .then(({data}) => {
                    document.getElementById('liste').innerHTML = data.liste;
                    document.getElementById('liste_cancelled').innerHTML = data.listecancelled;
                    toasterMessage(data.message);
                    declareEvent();
                })
                .catch(err => console.error('Erreur lors de la cloture', err));
        }
        modalBs.hide();
        declareEvent();
    }

    function closedFolder(event){
        event.preventDefault();
        let url = this.href;
        axios
            .get(url)
            .then(function (response){
                // initialisation du toaster
                let toastHTMLElement = document.getElementById("toaster");
                let message = response.data.message;
                let toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
                toastBody.textContent = message;
                let toastElement = new bootstrap.Toast(toastHTMLElement, {
                    animation: true,
                    autohide: true,
                    delay: 5000,
                });
                toastElement.show();
            })
            .catch(function (error){
                console.log(error);
            });
    }

    function declareEvent(){
        let btnsSubmitModal = document.querySelectorAll('.btnModalSubmit');
        let btnsOpenModal = document.querySelectorAll('.openModal');


        /** ouverture */
        btnsOpenModal.forEach((btn) => {
            btn.addEventListener('click', openModal);
        });

        /** validation depuis le bouton du footer **/
        btnsSubmitModal.forEach((link) => {
            link.addEventListener('click', submitModal);
        });
    }

    declareEvent();
}