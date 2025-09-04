import axios from 'axios';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/bootstrap/toaster";

export function initIndexCustomerPage() {
    console.log('Bonjour, vous êtes sur la page Index des clients');

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
        if(crud === 'DELCUSTOMER'){
            let idCustomer = url.split("/").pop() || url.split("/").pop();
            const confirmBtn = modalEl.querySelector('.modal-footer a');
            confirmBtn.textContent = "Supprimmer";
            axios
                .get('/gestapp/customer/haslink/'+ idCustomer)
                .then(({data}) => {
                    let haslink = data.haslink;
                    if(haslink === true){
                        modalEl.querySelector('.modal-body').innerHTML = "<p class='mb-0'>" +
                            "Vous souhaitez supprimer ce client de notre registre clientelle. " +
                            "D'après nos informations, il semble que ce dernier est engagé avec nous : <br>" +
                            "- soit après avoir signer un mandat de vente pour l'un de ses biens, <br>" +
                            "- soit en s'engageant sur une transaction immobilière." +
                            "</p>";
                        modalEl.querySelector('.modal-footer a').classList.add('d-none');
                    }else{
                        confirmBtn.href = url;
                        modalEl.querySelector('.modal-body').innerHTML = "<p class='mb-0'>Attention, vous êtes sur le point de supprimer le client en cours</p>";
                    }
                })
                .catch()
            ;
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

    function searchCustomer(e){
        e.preventDefault();
        const query = this.value.trim();
        if (query.length > 0) {
            let form = e.currentTarget.parentNode;
            let action = form.action;
            let data = new FormData(form);
            axios
                .post(action, data)
                .then(({data}) => {
                    document.getElementById('liste').innerHTML = data.liste;
                    declareEvent();
                })
                .catch(function (error){
                    console.log(error);
                })
            ;
            declareEvent();
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
        if (searchInput) {
            searchInput.addEventListener('input', searchCustomer);
        }
    }

    declareEvent();
}