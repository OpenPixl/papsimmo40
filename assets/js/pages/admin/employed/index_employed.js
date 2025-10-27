import axios from 'axios';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/bootstrap/toaster";

export function initIndexEmployedPage() {
    console.log('Bonjour, vous êtes sur la page Index des Employé ou des prescripteurs');

    const modalEl = document.getElementById('modal');
    if (!modalEl) return;
    const modalBs = new bootstrap.Modal(modalEl);

    const searchForm = document.getElementById('SearchFormProperty');
    const searchInput = document.getElementById('search_customer_slug');

    modalEl.addEventListener('hidden.bs.modal', () => {
        // Cas ou une url de suppression serait en place
        const deleteUrl = modal.dataset.deleteUrl;
        if (deleteUrl) {
            axios.post(deleteUrl)
                .then(() => console.log('Entité temporaire supprimée'))
                .catch(err => console.error('Erreur lors de la suppression', err));

            // Nettoyage de la valeur
            delete modal.dataset.deleteUrl;
        }
        // Remise à zero HTML de la modal
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
        if(crud === 'DELPRESCRIBER'){
            let idCustomer = url.split("/").pop() || url.split("/").pop();
            modalEl.querySelector('.modal-body').innerHTML = "<p><b>Attention</b></p><p>Vous êtes sur le point de supprimer un prescriptueur et ses accès de la plateforme.</p>";
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = "Supprimer le prescripteur";
            confirmBtn.href = url;

            modalBs.show();
        }
        declareEvent();
    }

    function submitModal(e){
        e.preventDefault();
        let modalContent = e.currentTarget.parentNode.parentElement;
        let form = modalContent.querySelector('form');
        if(form){
            let nameForm = form.id;
            let action = form.action;
            let data = new FormData(form);
            declareEvent();
        }else{
            let url = this.href;
            axios
                .post(url)
                .then(({data}) => {
                    document.getElementById('liste').innerHTML = data.liste;
                    toasterMessage(data.message);
                    modalBs.hide();
                    declareEvent();
                })
                .catch(function(error){
                    console.log(error);
                });
        }
    }

    function declareEvent(){
        const btnsOpenModal = document.querySelectorAll('.btnOpenModal');
        const btnsModalSubmit = document.querySelectorAll('.btnModalSubmit');

        btnsOpenModal.forEach(function(link){
            link.addEventListener('click', openModal);
        });
        btnsModalSubmit.forEach(function(link){
            link.addEventListener('click', submitModal);
        });
    }

    declareEvent();
}