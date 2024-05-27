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
const maxChars = 2000; // Limite de caractères
// Mise en place de l'éditeur TinyMCE sur la partie Admin
tinymce.init({
    selector: '.tinymce',
    setup: function(editor) {
        editor.on('input', function() {
            const content = editor.getContent({ format: 'text' });
            if (content.length > maxChars) {
                const truncatedContent = content.substring(0, maxChars);
                editor.setContent(truncatedContent);
                alert(`La limite de ${maxChars} caractères a été atteinte.`);
            }
        });

        editor.on('keydown', function(event) {
            const content = editor.getContent({ format: 'text' });
            if (content.length >= maxChars && event.key !== "Backspace" && event.key !== "Delete") {
                event.preventDefault();
                alert(`La limite de ${maxChars} caractères a été atteinte.`);
            }
        });
    },
    plugins: 'image table lists visualchars wordcount',
    toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image',
    images_file_types: 'jpg,svg,webp',
    language: 'fr_FR',
    language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
    entity_encoding: "raw",
    encoding: "html"
});