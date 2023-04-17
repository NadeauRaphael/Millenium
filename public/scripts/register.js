$(document).ready(() => {

    $('.txtPhone').mask('000-000-0000', { placeholder : '___-___-____'});
    $('.txtPostalCode').mask('A0E 0E0', { placeholder: '___-___', translation: { A:{ pattern: /[ABCEGHJ-NPRSTVXYabceghj-nprstvxy]/}, E:{pattern:/[ABCEGHJ-NPRSTV-Zabceghj-nprstv-z]/} }});
   
    $('.txtPostalCode').keyup(function(){
        $(this).val($(this).val().toUpperCase());
    });

    const registrationForm = document.querySelectorAll('.needs-validation-register');

    addValidationToForm(registrationForm);

});

function addValidationToForm(forms) {
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
    });
}