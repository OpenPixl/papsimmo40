const radioButtons = document.querySelectorAll('input[name=employed\\[civility\\]]');
const showAccount = document.getElementById('showAccount');

new TomSelect("#employed_referent",{
    plugins: ['remove_button'],
    create: true,
    onItemAdd:function(){
        this.setTextboxValue('');
        this.refreshOptions();
    },
    render:{
        option:function(data,escape){
            return '<div class="d-flex"><span>' + escape(data.data) + '</span><span class="ms-auto text-muted">' + escape(data.value) + '</span></div>';
        },
        item:function(data,escape){
            return '<div>' + escape(data.data) + '</div>';
        }
    }
});

let valcivility = document.querySelector('input[name=employed\\[civility\\]]:checked').value;
if (valcivility > 1){
    document.getElementById('employed_maidenName').classList.remove('d-none');
}


radioButtons.forEach(function(radio) {
    radio.addEventListener("change", function() {
        if (parseInt(this.value) === 2) {
            document.getElementById('employed_maidenName').classList.remove('d-none');
        } else if (parseInt(this.value) === 1){
            document.getElementById('employed_maidenName').classList.add('d-none');
        }
    });
});