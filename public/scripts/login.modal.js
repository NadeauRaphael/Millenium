$(document).ready(() => {
    $(".login-modal").click(async ($event) => {
        event.preventDefault();

        const href = event.currentTarget.href;
        
        const response = await axios.get(href);
        if(response.status === 200){
            $("#login-modal-content").html(response.data);
            const loginViewModal = new bootstrap.Modal(document.getElementById('login-modal'), {});
            loginViewModal.show();   
        }
    });
});