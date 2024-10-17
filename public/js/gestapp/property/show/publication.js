const switchAllPublication = document.getElementById('AllPublications');

switchAllPublication.addEventListener('change', AllCheckedPublication);

function AllCheckedPublication(){
    const isChecked = this.checked;
    const switches = document.querySelectorAll('#FormPublication .form-check-input[type="checkbox"]');
    switches.forEach((switchInput) => {
        // Ne pas changer l'état des switches désactivés
        if (!switchInput.disabled) {
            switchInput.checked = isChecked;  // Cocher ou décocher les autres switches non désactivés
        }
    });
    //console.log(switches);
}
