import * as bootstrap from "bootstrap";
import {toasterMessage} from "../../../components/bootstrap/toaster";

export function initIndexTransactionPage() {
    console.log('Page d\'index des transactions initialisées.');

    const modalEl = document.getElementById('modal');
    if (!modalEl) return;
    const modalBs = new bootstrap.Modal(modalEl);

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
                })
                .catch();
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
                        document.getElementById('myTabContent').innerHTML = data.liste;
                        toasterMessage(message);
                    })
                    .catch()
                ;
            }
        }
        modalBs.hide();
        declareEvent();
    }

    function opentab(e){
        e.preventDefault();
        let tab_name = e.currentTarget.parentNode.id;
        console.log(tab_name);
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