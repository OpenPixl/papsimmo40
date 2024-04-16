// any CSS you import will output into a single css file (app.scss in this case)
import './styles/admin.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import flatpickr from "flatpickr";
import { french } from "flatpickr/dist/l10n/fr.js";
// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickr", {
    enableTime: false,
    allowInput: true,
    altFormat: "j F Y",
    dateFormat: "d/m/Y",
});
// mise en place du datapicker flatpickr sur les champs de date
flatpickr(".flatpickrtime", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true
});

// start the Stimulus application
import './bootstrap';
import tinymce from "tinymce";

// Mise en place de l'éditeur TinyMCE sur la partie Admin
tinymce.init({
    selector: '.tinymce',
    plugins: 'image table lists',
    toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image',
    images_file_types: 'jpg,svg,webp',
    language: 'fr_FR',
    language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
    entity_encoding: "raw",
    encoding: "html"
});