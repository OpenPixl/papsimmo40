// any CSS you import will output into a single css file (app.scss in this case)
import './styles/admin.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import flatpickr from "flatpickr";

import { initShowTransactionPage } from './js/pages/gestapp/transactions/show_transaction';
import { initListTransactionPage } from './js/pages/gestapp/transactions/list_transaction';

document.addEventListener('DOMContentLoaded', () => {
    const page = document.body.dataset.page;

    switch (page) {
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
flatpickr(".flatpickr", {
    "locale": "fr",
    enableTime: false,
    allowInput: true,
    altFormat: "j F Y",
    dateFormat: "d/m/Y",
});

// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickrtime", {
    "locale": "fr",
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});
// start the Stimulus application
import './bootstrap';