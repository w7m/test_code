$(document).ready(function () {
    const controlInputNumber = function() {
        let input = $(this).val();
        let regex = new RegExp("^[0-9]+(,?)[0-9]*$");
        if (!regex.test(input)) {
            console.log(regex.test(input));
            $(this).val(input.substr(0, input.length - 1));
        }
    };
    const controlInputCodePostal = function() {
        let input = $(this).val();
        let regex = new RegExp("^[0-9]+$");
        if (!regex.test(input) || input.length>4) {
            $(this).val(input.substr(0, input.length - 1));
        }
    };
    const controlInputPhone = function() {
        let input = $(this).val();
        let regex = new RegExp("^[0-9 ]*$");
        if (!regex.test(input)  || input.length>10) {
            $(this).val(input.substr(0, input.length - 1));
        }
    };
    $('#parameter_photoPrice').on('keyup', controlInputNumber );
    $('#parameter_openingFileExpense').on('keyup', controlInputNumber );
    $('#parameter_expertiseFees').on('keyup', controlInputNumber );
    $('#parameter_billPercentage').on('keyup', controlInputNumber );
    $('#parameter_postcodeInsurer').on('keyup', controlInputCodePostal );
    $('#parameter_insurancePhone').on('keyup', controlInputPhone );

});