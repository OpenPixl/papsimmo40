export function removeOptions(selectElement) {
    for (let i = selectElement.options.length - 1; i >= 0; i -= 1) {
        selectElement.remove(i);
    }
}

export function zipcode(zipcodeInput, communeInput, select) {
    zipcodeInput.addEventListener('input', () => {
        if (zipcodeInput.value.length !== 5) return;

        axios
            .get(`https://apicarto.ign.fr/api/codes-postaux/communes/${zipcodeInput.value}`)
            .then(({ data }) => {
                removeOptions(select);

                data.forEach((el, idx) => {
                    const label = `${el.nomCommune.toUpperCase()} (${el.codePostal})`;
                    const opt = new Option(label, label, idx === 0, idx === 0);
                    select.options.add(opt);
                });

                if (data.length) {
                    zipcodeInput.value = data[0].codePostal;
                    communeInput.value = data[0].nomCommune.toUpperCase();
                }
            })
            .catch(() => alert('Pas de commune pour ce code postal'));
    });
}

export function change_selectcity(zipcode, commune, select){
    let regex = /^(.+) \((\d+)\)$/;
    let select_value = select.options[select.selectedIndex].text;
    const match = select_value.match(regex);
    zipcode.value = match[2];
    commune.value = match[1].toUpperCase();
}

export function formatDate(dateInput){
    dateInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // supprime tout sauf chiffres
        if (value.length > 8) value = value.substring(0, 8); // max 8 chiffres
        let formatted = '';
        if (value.length > 0) {
            formatted += value.substring(0, 2);
        }
        if (value.length > 2) {
            formatted += '/' + value.substring(2, 4);
        }
        if (value.length > 4) {
            formatted += '/' + value.substring(4, 8);
        }
        // Appliquer le format
        e.target.value = formatted;
    });
    dateInput.addEventListener('paste', function (e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const digits = paste.replace(/\D/g, '').substring(0, 8);
        let formatted = '';
        if (digits.length >= 2) formatted += digits.substring(0, 2);
        if (digits.length >= 4) formatted += '/' + digits.substring(2, 4);
        if (digits.length > 4) formatted += '/' + digits.substring(4, 8);
        e.target.value = formatted;
    });
}