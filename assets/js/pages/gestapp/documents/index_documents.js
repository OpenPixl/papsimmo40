import axios from 'axios';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';
import * as bootstrap from "bootstrap";
import {toasterMessage} from "../../../components/bootstrap/toaster";

export function initIndexDocumentsPage() {
    const sortableDoc = document.getElementById("sortDoc");
    const btnSubmitdocument = document.getElementById('btnSubmitdocument');
    let category = 0;

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

    const TsSimple = {
        //plugins: ['remove_button'],
        create: true,
        onItemAdd:function(){
            this.setTextboxValue('');
            this.refreshOptions();
        },
        render:{
            option:function(data,escape){
                return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
            },
            item:function(data,escape){
                return '<div>' + escape(data.data) + '</div>';
            }
        }
    };
    const TsMulti = {
        plugins: ['remove_button'],
        create: true,
        onItemAdd:function(){
            this.setTextboxValue('');
            this.refreshOptions();
        },
        render:{
            option:function(data,escape){
                return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
            },
            item:function(data,escape){
                return '<div>' + escape(data.data) + '</div>';
            }
        }
    };
    function initializeTomSelect(selector, options = {}) {
        document.querySelectorAll(selector).forEach(selectElement => {
            new TomSelect(selectElement, options);
        });
    }

    function openModal(e){
        e.preventDefault();
        let a = e.currentTarget;
        let url = a.href;
        const [crud, contentTitle, option] = a.dataset.bsData.split('-');
        console.log(crud, contentTitle, option);
        modalEl.querySelector('.modal-title').textContent = contentTitle;
        if (['ADDCATEGORY', 'EDITCATEGORY'].includes(crud)) {
            axios
                .get(url)
                .then(response => {
                    modalEl.querySelector('.modal-body').innerHTML = response.data.formView;
                    modalEl.querySelector('.modal-footer a').href = url;
                })
                .catch(error => {
                    console.log(error);
                });
            modalBs.show();
        }
        else if (crud === 'DELDOCUMENT'){
            modalEl.querySelector('.modal-body').innerHTML = "<b>Attention</b> : Vous allez supprimer ce document. Voulez-vous continuer ? ";
            modalEl.querySelector('.modal-footer a').href = url;
            modalEl.querySelector('.modal-footer a').textContent = "Oui";
            modalEl.querySelector('.modal-footer a').classList.remove('btn-primary');
            modalEl.querySelector('.modal-footer a').classList.add('btn-danger');
            modalBs.show();
        }
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
                    document.getElementById('sortDoc').innerHTML = data.liste;
                    toasterMessage(data.message);
                    declareEvent();
                })
                .catch(function(error){
                    console.log(error);
                });

        }
        modalBs.hide();
    }

    function addDocument(e){
        e.preventDefault();
        let form = document.getElementById('FormAddDocument');
        if(form){
            let nameForm = form.id;
            let action = form.action;
            let data = new FormData(form);
            axios
                .post(action, data)
                .then(({data}) => {
                    document.getElementById('sortDoc').innerHTML = data.liste;
                    toasterMessage(data.message);
                    declareEvent();
                })
                .catch(function(error){
                    console.log(error);
                });
        }
    }

    // I. Mise en place du Select2
    const TsPropertyBanner = new TomSelect("#document_category",{
        plugins: ['remove_button'],
        create: true,
        onItemAdd:function(){
            this.setTextboxValue('');
            this.refreshOptions();
        },
        render:{
            option:function(data,escape){
                return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
            },
            item:function(data,escape){
                return '<div>' + escape(data.data) + '</div>';
            }
        }
    });

    function FilterDocument(event){
        event.preventDefault();
        let url = this.href;
        category = url.split('/').pop() || url.pop();
        axios
            .get(url)
            .then(function(response){
                document.getElementById('sortDoc').innerHTML = response.data.liste;
                declareEvent();
            })
            .catch(function (error){
                console.log(error);
            });
    }

    function declareEvent(){
        new Sortable(sortableDoc, {
            animation:150,
            // Called by any change to the list (add / update / remove)
            onSort: function (event) {
                let cols = sortableDoc.children;
                let data = Array();
                // on boucle sur le résultat des enfants pour envoyer au controller la modification du positionnement des photos
                for(let i = 0; i < cols.length; i++){
                    let idcol = cols[i].id;
                    let key = i + 1;
                    data.push({"key" : key, "idcol" : parseInt(idcol)});
                }
                let url = "/gestapp/document/updateposition/" + category;
                console.log(data);
                axios
                    .post(url, data)
                    .then(function(response){
                        document.getElementById('sortDoc').innerHTML = response.data.listDocument;
                        // initialisation du toaster bootstrap
                        var toastHTMLElement = document.getElementById("toaster");
                        var message = response.data.message;
                        var toastBody = toastHTMLElement.querySelector('.toast-body'); // selection de l'élément possédant le message
                        toastBody.textContent = message;
                        var toastElement = new bootstrap.Toast(toastHTMLElement, {animation: true, autohide: true, delay: 3000});
                        toastElement.show();
                    })
                    .catch(function(error){
                        console.log(error);
                    });
            },
        });
        let btnsModalSubmit = document.querySelectorAll('.btnModalSubmit');
        let btnSubmitDocument = document.getElementById('submitDocument');

        btnSubmitDocument.addEventListener('click', addDocument);
        // Mise en place de l'évenement pour la suppression d'un bien en cours de création
        document.querySelectorAll('a.btnModalSupprDocument').forEach(function(link){
            link.addEventListener('click', openModal);
        });
        // Suppression d'un document
        btnsModalSubmit.forEach(function(link){
            link.addEventListener('click', submitModal);
        });

        // Filtrage des documents
        document.querySelectorAll('a.btnCat').forEach(function(link){
            link.addEventListener('click', FilterDocument);
        });
    }

    declareEvent();


}