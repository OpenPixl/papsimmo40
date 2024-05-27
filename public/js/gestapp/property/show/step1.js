// ------------------------------------------------
// Javascript en lien avec la première partie de l'édition d'un bien
const maxChars = 2000; // Limite de caractères

// Mise en place de l'éditeur TinyMCE sur la partie Admin
tinymce.init({
    selector: '#property_step1_annonce',
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