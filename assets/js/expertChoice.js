const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);

$(document).ready(function () {
    $("#reassign").on("click",function () {
        var idFolder = $("#folderId").val();
        var idExpert = null;
        var $radios = $('input:radio[name=expertId]');
        if($radios.is(':checked') === true) {
            var idExpert = $("input[name='expertId']:checked").val();
            $.post(
                Routing.generate('new-expert-reassigned'),
                {idFolder,idExpert}
            )
                .done(function (data) {
                    if (data === 'success') {
                        window.location.replace("/validator/list-folder");
                    } else {
                        alert(data);
                    }
                })
        } else {
            swal("ERREUR", "Veuillez choisir un expert", "error");
        }
    })
});