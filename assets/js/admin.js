var formExpert = $('.expert');

function controlNumber(input) {
    $(input).keypress(function (event) {
        if(isNaN(String.fromCharCode(event.which)) || event.which === 32){
            event.preventDefault();
        }
    });
}
controlNumber('#expert_phoneNumber')
controlNumber('#insurer_phoneNumber')
controlNumber('#expert_postal_code')


