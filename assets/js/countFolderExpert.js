
    const routes = require('../../public/js/fos_js_routes.json');
    import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
    Routing.setRoutingData(routes);
    $(document).ready(function() {
    setInterval(function () {
        $.ajax({
            cache: false,
            url: Routing.generate('count-expert-Folders'),
            type: "get",
        }).done(function (response, textStatus, jqXHR) {
            $(".new-folder").text(response.created)
            $(".progress-folder").text(response.inProgress)
            $(".returned-folder").text(response.toBeReconsidered)
            $(".reassigned-folder").text(response.reassigned)
            $(".selling-standby-folder").text(response.sellingStandby)
            $(".closed-folder").text(response.closed)
            console.log(response)
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert("Erreur serveur")
        });
    }, 5000);

});