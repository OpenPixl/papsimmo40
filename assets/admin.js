// any CSS you import will output into a single css file (app.scss in this case)
import './styles/admin.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import flatpickr from "flatpickr";
import { french } from "flatpickr/dist/l10n/fr.js";
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
import tinymce from "tinymce";
