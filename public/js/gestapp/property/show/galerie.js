const btndelVideo = document.querySelector('.delVideo');

if (btndelVideo !== null) {
    btndelVideo.addEventListener('click', (e) => {
        e.preventDefault();
        let url = this.href;
        axios
            .post(url)
            .then(function(response){
                toasterMessage(response.data.message);
            })
            .catch(function(error){
                console.log(error);
            });
    });
}