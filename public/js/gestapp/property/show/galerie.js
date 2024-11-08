const btndelVideo = document.querySelector('.delVideo');

if (btndelVideo !== null) {
    btndelVideo.addEventListener('click', (e) => {
        e.preventDefault();
        let a = e.currentTarget;
        let url = a.href;
        console.log(url);
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