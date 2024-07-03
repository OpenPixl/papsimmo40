const modalPrint = document.getElementById('modalPrint');

modalPrint.addEventListener('show.bs.modal', function (event) {
    let a = event.relatedTarget;
    let opt = a.getAttribute('data-bs-whatever');
    let crud = opt.split('-')[0];
    let contentTitle = opt.split('-')[1];
    let idproperty = opt.split('-')[2];
    let urlPortrait = "/admin/pdf/Property/ficheagenceportrait/" +idproperty;
    let urlPaysage = "/admin/pdf/Property/ficheagencepaysage/" +idproperty;
    modalPrint.querySelector("#printPdfPortrait").href = urlPortrait;
    modalPrint.querySelector("#printPdfPaysage").href = urlPaysage;
});