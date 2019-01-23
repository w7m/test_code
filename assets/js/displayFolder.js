'use strict';


const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

$(function () {
    var context = $('#crash-points-canvas')[0].getContext('2d');
    const drawCrashPoints = function (ptX, ptY) {
        let coordinate = $('#crash-points-canvas').offset(); // utiliser .top and .left pour les coordonnées

        let canvasX = ptX - coordinate.left;
        let canvasY = ptY - coordinate.top;

        context.beginPath();
        context.strokeStyle = "#FF0000";
        context.lineWidth = 2;
        context.arc(canvasX, canvasY, 20, 0, Math.PI * 2);
        context.stroke();
        context.closePath();
        return {canvasX, canvasY};
    };

    for (let e of crashPoints) {
        drawCrashPoints(e[0], e[1]);
    }

    const changeImageStatus = function (idImage) {
        let url = Routing.generate('changeAttachedFilState', {id: idImage});
        $.post(url);
    };

    $(document).on('click', '.imageValidator', function () {
        let imageStatus = $(this).parent().find('.statusImage').val();
        let idImage = $(this).parent().find('.idImage').val();
        let validatedText = `<i class="fas fa-check fa-2x text-success"></i>`;
        let invalidatedText = `<i class="fas fa-times fa-2x text-danger"></i>`;
        if (imageStatus == "true" || imageStatus == true) {
            $(this).html(invalidatedText);
            $(this).parent().find('.statusImage').val(false);
        }
        if (imageStatus == "false" || imageStatus == false) {
            $(this).html(validatedText);
            $(this).parent().find('.statusImage').val(true);
        }
        changeImageStatus(idImage);
    });

    $('img').on('click', function () {
        let thisSrc = $(this).attr('src');
        $('#modal-image img').attr('src', thisSrc);
        $('#modal-image').css('display', 'flex');
    });
    $('#modal-image').on('click', function () {
        $('#modal-image').hide();
    });
    $('#modal-image img').on('click', function () {
        $('#modal-image').hide();
    });
});