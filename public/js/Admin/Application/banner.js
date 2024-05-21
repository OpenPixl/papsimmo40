// Déclaration des const
const modalBanner = document.getElementById('modalBanner');

// Modal Banner
if (modalBanner) {
    modalBanner.addEventListener('show.bs.modal', event => {
        // Button that triggered the modal
        const button = event.relatedTarget;
        const recipient = button.getAttribute('data-bs-whatever');
        let crud = recipient.split('-')[0];
        let contentTitle = recipient.split('-')[1];
        let id = recipient.split('-')[2];
        // If necessary, you could initiate an Ajax request here
        // and then do the updating in a callback.

        // Update the modal's content.
        const modalTitle = modalBanner.querySelector('.modal-title');
        modalTitle.textContent = contentTitle;
    });
}

// Déclaration des évènements

