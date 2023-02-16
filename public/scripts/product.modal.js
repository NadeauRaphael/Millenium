$(document).ready(() => {
    $(".product-modal").click(async ($event) => {
        event.preventDefault();

        const href = event.currentTarget.href;
        
        const response = await axios.get(href);
        if(response.status === 200){
            $("#product-modal-content").html(response.data);
            const championViewModal = new bootstrap.Modal(document.getElementById('product-modal'), {});
            championViewModal.show();   
        }
    });
});