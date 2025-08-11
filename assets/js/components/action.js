export default function blockBackNavigation() {
    // Empêche l'utilisateur de revenir en arrière
    window.history.pushState(null, "", window.location.href);

    window.addEventListener("popstate", function () {
        window.history.pushState(null, "", window.location.href);
        alert("Navigation arrière désactivée !");
    });
}