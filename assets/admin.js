// any CSS you import will output into a single css file (app.scss in this case)
import './styles/admin.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import flatpickr from "flatpickr";
import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';

import { initNewEditEmployedPage } from './js/pages/admin/employed/newedit_employed';
import { initIndexEmployedPage } from './js/pages/admin/employed/index_employed';
import { initIndexCustomerPage } from './js/pages/gestapp/customer/index_customer';
import { initNewEditCustomerPage } from './js/pages/gestapp/customer/newedit_customer';
import { initShowPropertyPage } from './js/pages/gestapp/property/show_property';
import { initShowTransactionPage } from './js/pages/gestapp/transactions/show_transaction';
import { initIndexTransactionPage } from './js/pages/gestapp/transactions';
import { initIndexDocumentsPage } from './js/pages/gestapp/documents/index_documents';
import { initNewEditArticlesPage } from './js/pages/webapp/article/newedit_article';


document.addEventListener('DOMContentLoaded', () => {
    const page = document.body.dataset.page;

    switch (page) {
        case 'op_admin_employed_index':
        case 'op_admin_prescriber_index':
        case 'op_admin_prescriber_all':
            initIndexEmployedPage();
            break;
        case 'op_admin_employed_new':
        case 'op_admin_employed_edit':
        case 'op_admin_prescriber_edit':
            initNewEditEmployedPage();
            break;
        case 'op_gestapp_customer_index':
            initIndexCustomerPage();
            break;
        case 'op_gestapp_customer_new':
        case 'op_gestapp_customer_edit':
            initNewEditCustomerPage();
            break;
        case 'op_gestapp_property_show':
        case 'op_gestapp_property_duplicate':
            initShowPropertyPage();
            break;
        case 'op_gestapp_transaction_show':
            initShowTransactionPage();
            break;
        case 'op_gestapp_document_index':
            initIndexDocumentsPage();
            break;
        case 'op_gestapp_transaction_index':
            initIndexTransactionPage();
            break;
        case 'op_webapp_articles_new':
        case 'op_webapp_articles_edit':
        case 'op_webapp_articles_newactualite':
        case 'op_webapp_articles_editactualite':
            initNewEditArticlesPage();
            break;
        default:
            console.log('Page non reconnue ou pas de JS spécifique');
    }
});

document.addEventListener('keydown', function(event) {
    // Si la touche appuyée est "Entrée"
    if (event.key === 'Enter') {
        // Vérifie si la cible est un champ de formulaire
        const target = event.target;
        const isInput = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA';
        const isButton = target.tagName === 'BUTTON';
        const isSubmit = target.type === 'submit';

        // Empêche le comportement par défaut sauf si c’est un bouton ou un textarea
        if (isInput && !isButton && !isSubmit && target.type !== 'textarea') {
            event.preventDefault();
            return false;
        }
    }
});


// mise en place du datapicker flatpickr sur les champs de date
// flatpickr(".flatpickr", {
//     "locale": "fr",
//     enableTime: false,
//     allowInput: true,
//     altFormat: "j F Y",
//     dateFormat: "d/m/Y",
// });

// mise en place du datapicker flatpickr sur les champs de date
// flatpickr(".flatpickrtime", {
//     "locale": "fr",
//     enableTime: true,
//     noCalendar: true,
//     dateFormat: "H:i",
//     time_24hr: true
// });

// start the Stimulus application
import './bootstrap';