// any CSS you import will output into a single css file (app.scss in this case)
import './styles/admin.scss';
window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle.js');

// You can specify which plugins you need
import { Tooltip, Toast, Popover } from './bootstrap';

// start the Stimulus application
import './bootstrap';
import tinymce from "tinymce";

// Mise en place de l'éditeur TinyMCE sur la partie Admin
tinymce.init({
    selector: '.tinymce',
    plugins: 'image table advtable lists',
    toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image table',
    images_file_types: 'jpg,svg,webp',
    language: 'fr_FR',
    language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
    entity_encoding: "raw",
    encoding: "html"
});

const ContactSupport = document.getElementById('btnContactSupport');
ContactSupport.addEventListener('click', function(event){

});