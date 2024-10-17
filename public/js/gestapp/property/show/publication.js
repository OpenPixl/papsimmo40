const switchAllPublication = document.getElementById('AllPublications');

switchAllPublication.addEventListener('change', AllCheckedPublication);

function AllCheckedPublication(){
    const isChecked = this.checked;
    const switches = document.querySelectorAll('#FormPublication .form-check-input[type="checkbox"]');
    switches.forEach((switchInput) => {
        switchInput.checked = isChecked;  // Cocher chaque switch
    });
    //console.log(switches);
}
