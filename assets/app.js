// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

function scrollValue() {
    var navbar = document.getElementById('navbar');
    var scroll = window.scrollY;
    if (scroll < 200) {
        navbar.classList.remove('bg-light');
    } else {
        navbar.classList.add('bg-light');
    }
}

window.addEventListener('scroll', scrollValue);

// start the Stimulus application
import './bootstrap';
import tinymce from "tinymce";

// Mise en place de l'éditeur TinyMCE sur la partie Admin
tinymce.init({
    selector: '.tinymce',
    plugins: 'image table lists',
    toolbar: 'undo redo | styles | bold italic alignleft aligncenter alignright alignjustify numlist bullist | link image table',
    images_file_types: 'jpg,svg,webp',
    language: 'fr_FR',
    language_url: '/js/tinymce/js/tinymce/languages/fr_FR.js',
    entity_encoding: "raw"
});