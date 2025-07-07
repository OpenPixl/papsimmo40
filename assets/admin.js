// any CSS you import will output into a single css file (app.scss in this case)
import './styles/admin.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import flatpickr from "flatpickr";
import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';

import { initNewEditCustomerPage } from './js/pages/gestapp/customer/newedit_customer';
import { initShowPropertyPage } from './js/pages/gestapp/property/show_property';
import { initShowTransactionPage } from './js/pages/gestapp/transactions/show_transaction';
import { initListTransactionPage } from './js/pages/gestapp/transactions/list_transaction';


document.addEventListener('DOMContentLoaded', () => {
    const page = document.body.dataset.page;

    switch (page) {
        case 'op_gestapp_customer_new':
        case 'op_gestapp_customer_edit':
            initNewEditCustomerPage();
            break;
        case 'op_gestapp_property_show':
            initShowPropertyPage();
            break;
        case 'op_gestapp_transaction_show':
            initShowTransactionPage();
            break;
        case 'op_gestapp_transaction_index':
            initListTransactionPage();
            break;
        default:
            console.log('Page non reconnue ou pas de JS spécifique');
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