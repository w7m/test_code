'use strict';


const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

$(function () {
    var isFolderRepair = true;
    var idFolder = $('#folderId').val();

    // Tracage et réinitialisation des points de chocs
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

    $('#crash-points-canvas').on('click', function (e) {
        let mouseX = e.pageX;
        let mouseY = e.pageY;
        let crashPoint = drawCrashPoints(mouseX, mouseY);
        let url = Routing.generate('addCrashPoint', {id: idFolder});
        $.post(url, {crashPoint});
    });

    $('#reset-crash-points-canvas').on('click', function (e) {
        e.preventDefault();
        context.clearRect(0, 0, 650, 400);
        let url = Routing.generate('deleteCrashPoint', {id: idFolder});
        $('#modal-loading').fadeIn();
        $.post(url)
            .done(function () {
                $('#modal-loading').fadeOut();
            })
        ;
    });
    // FIN Point de chocs

    // Ajout facture
    var addBillForm = $('#add-bill-form form');
    $('#add-bill-btn').on('click', function (e) {
        e.preventDefault();
        if (!checkEstimatedAmountValidity()) {
            return;
        }
        let url = addBillForm.attr('action');
        let serializedForm = addBillForm.serialize();

        $('#modal-loading').fadeIn();
        usedTypes.push($('#bill_type').val());
        usedworks.push($('#bill_works').val());

        function cleanArray(array) {
            var i, j, len = array.length, out = [], obj = {};
            for (i = 0; i < len; i++) {
                obj[array[i]] = 0;
            }
            for (j in obj) {
                out.push(j);
            }
            return out;
        }

        usedTypes = cleanArray(usedTypes);
        usedworks = cleanArray(usedworks);

        $.post(url, serializedForm)
            .done(function (data) {
                let line = $(createBillLine(data));
                line.hide();
                $('#bills-table').prepend(line);
                line.fadeIn(500);
                $("#bill_works").val('').focus();
                $("#bill_realAmount").val('');
                $("#bill_estimaedAmount").val('');
                setTimeout(function () {
                    calculateRealAmount();
                    calculateEstimatedAmount();
                }, 400);
            })
            .fail(function () {
                swal("ERREUR", "Une erreur est survenu lors de l\'ajout de la facture", "error");
            })
            .always(function () {
                $('#modal-loading').fadeOut();
            });
    });

    const createBillLine = function (form) {
        let billDate = new Date(form[2]);
        return `<tr>
                    <input type="hidden" value="${form[0]}">
                    <td>${form[1]}</td>
                    <td>${billDate.toLocaleDateString("en-GB")}</td>
                    <td>${form[3]}</td>
                    <td>${form[4]}</td>
                    <td class="realAmount">${form[5]}</td>
                    <td class="estimatedAmount">${form[6]}</td>
                    <td><button class="btn btn-danger btn-delete-bill"><i class="fas fa-trash-alt"></i></button></td>
                </tr>`
    };

    var usedTypes = [];
    var usedworks = [];
    $('#bill_type').autocomplete({
        source: usedTypes
    });
    $('#bill_works').autocomplete({
        source: usedworks
    });
    // FIN ajout factures

    // Controle des inputs en temps réel
    $('#bill_realAmount').on('keyup', function () {
        let input = $(this).val();
        let regex = new RegExp("^[0-9|,|.]+$");
        if (!regex.test(input)) {
            $(this).val(input.substr(0, input.length - 1));
        }
    });

    $('#bill_estimaedAmount').on('keyup', function () {
        let input = $(this).val();
        let regex = new RegExp("^[0-9|,|.]+$");
        if (!regex.test(input)) {
            $(this).val(input.substr(0, input.length - 1));
        }
    });
    // FIN controle des inputs en temps réel

    // Calcul et mise à jour des montants
    const calculateRealAmount = function () {
        let realAmountSum = 0;
        let fields = $(".realAmount");
        for (let elem of fields) {
            realAmountSum += parseFloat(elem.textContent);
        }
        $('#real-amount-text span').fadeOut().text(realAmountSum).fadeIn();
    };

    const calculateEstimatedAmount = function () {
        let estimatedAmountSum = 0;
        let fields = $(".estimatedAmount");
        for (let elem of fields) {
            estimatedAmountSum += parseFloat(elem.textContent);
        }
        $('#estimated-amount-text span').fadeOut().text(estimatedAmountSum).fadeIn();
    };

    calculateRealAmount();
    calculateEstimatedAmount();

    // FIN calcul et mise à jour des montants


    // Desactivation tabs dossier épave

    $('#btn-epave-folder').on('click', function (e) {
        e.preventDefault();
        if (isFolderRepair && $(this).hasClass('btn-outline-primary')) {
            isFolderRepair = false;
            disableTabsBtns();
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $("#btn-repair-folder").removeClass('btn-success').addClass('btn-outline-success');
        } else if (!isFolderRepair && $(this).hasClass('btn-primary')) {
            isFolderRepair = true;
            enableTabBtns();
            $(this).removeClass('btn-primary').addClass('btn-outline-primary');
            $("#btn-repair-folder").removeClass('btn-outline-success').addClass('btn-success');
        }
        updateRecap();
    });

    $('#btn-repair-folder').on('click', function (e) {
        e.preventDefault();
        if (isFolderRepair && $(this).hasClass('btn-success')) {
            isFolderRepair = false;
            disableTabsBtns();
            $(this).removeClass('btn-success').addClass('btn-outline-success');
            $("#btn-epave-folder").removeClass('btn-outline-primary').addClass('btn-primary');
        } else if (!isFolderRepair && $(this).hasClass('btn-outline-success')) {
            isFolderRepair = true;
            enableTabBtns();
            $(this).removeClass('btn-outline-success').addClass('btn-success');
            $("#btn-epave-folder").removeClass('btn-primary').addClass('btn-outline-primary');
        }
        updateRecap();
    });

    const disableTabsBtns = function () {
        let afterRepairTabBtn = $('#after-repair-tab-btn');
        let factuertabBtn = $('#facture-tab-btn');

        afterRepairTabBtn.removeClass('btn-outline-primary').addClass('btn-outline-danger').addClass('disabled');
        factuertabBtn.removeClass('btn-outline-primary').addClass('btn-outline-danger').addClass('disabled');
        displayEditReport();
    };

    const enableTabBtns = function () {
        let afterRepairTabBtn = $('#after-repair-tab-btn');
        let factuertabBtn = $('#facture-tab-btn');

        afterRepairTabBtn.removeClass('btn-outline-danger').removeClass('disabled').addClass('btn-outline-primary');
        factuertabBtn.removeClass('btn-outline-danger').removeClass('disabled').addClass('btn-outline-primary');
        hideEditReport();
    };

    // FIN désactivation tabs dossier épave

    // Controle de saisie sur les montants
    const checkEstimatedAmountValidity = function () {
        let estimatedAmountField = $('#bill_estimaedAmount');
        let realAmountField = $('#bill_realAmount');
        if (realAmountField.val() == '') {
            realAmountField.val('0');
        }
        if (parseFloat(estimatedAmountField.val()) > parseFloat(realAmountField.val())) {
            estimatedAmountField.addClass('is-invalid');
            return false;
        }
        estimatedAmountField.removeClass('is-invalid');
        return true;
    };

    // FIN controle de saisie sur les montants

    // Suupression de factures
    $(document).on('click', '.btn-delete-bill', function (e) {
        e.preventDefault();
        let billField = $(this).parent().parent();
        let billId = billField.find('input').val();

        $('#modal-loading').fadeIn();
        let url = Routing.generate('deleteBill') + '/' + billId;
        $.ajax(url)
            .done(function () {
                billField.fadeOut(400);
                setTimeout(function () {
                    billField.remove();
                    calculateEstimatedAmount();
                    calculateRealAmount();
                }, 400);
            })
            .always(function () {
                $('#modal-loading').fadeOut();
            });
    });
    // FIN suppression de factures

    // Affichage et traitment édition dossier épave

    $('#estimatedPriceField').text( $('#rapport_epave_estimatedCarPrice').val());
    $('#estimatedReparationsField').text( $('#rapport_epave_repairAmount').val());
    $('#expertCommentsField').text( $('#rapport_epave_comments').val());
    $('#rapport_epave_estimatedCarPrice').on('keyup', function () {
        $('#estimatedPriceField').text($(this).val());
    });
    $('#rapport_epave_repairAmount').on('keyup', function () {
        $('#estimatedReparationsField').text($(this).val());
    });
    $('#rapport_epave_comments').on('keyup', function () {
        $('#expertCommentsField').text($(this).val());
    });

    const displayEditReport = function () {
        $('#epave-folder-panel').css('display', 'flex');
        let url = Routing.generate('changeIsRepairState', {id: idFolder});
        $.get(url);
    };
    const hideEditReport = function () {
        $('#epave-folder-panel').css('display', 'none');
        let url = Routing.generate('changeIsRepairState', {id: idFolder});
        $.get(url);
    };
    // FIN Affichage et traitment édition dossier épave

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

    // Soumission panel
    const updateRecap = function () {
        let submissionType = '';
        if (isFolderRepair) {
            submissionType = 'Réparation'
        } else {
            submissionType = 'Epave'
        }
        $('#submission_type').text(submissionType);
    };
    updateRecap();
    // FIN soumission panel

    // Suppression des images
    $(document).on('click', '.delete-image', function (e) {
        e.preventDefault();
        let thisElem = $(this).parent().parent().parent();
        let url = $(this).parent().attr('href');
        $('#modal-loading').fadeIn();
        $.ajax({
            url: url,
            method: "DELETE",
        })
            .done(function () {
                thisElem.fadeOut(400);
                setTimeout(function () {
                    thisElem.remove();
                }, 400)
            })
            .always(function () {
                $('#modal-loading').fadeOut();
            });
    });
    //FIN Suppression des images

    const changeInterfaceToWreck = function () {
        $('#epave-folder-panel').css('display', 'flex');
        $('#btn-epave-folder').removeClass('btn-outline-primary').addClass('btn-primary');
        $("#btn-repair-folder").removeClass('btn-success').addClass('btn-outline-success');
        let afterRepairTabBtn = $('#after-repair-tab-btn');
        let factuertabBtn = $('#facture-tab-btn');

        afterRepairTabBtn.removeClass('btn-outline-primary').addClass('btn-outline-danger').addClass('disabled');
        factuertabBtn.removeClass('btn-outline-primary').addClass('btn-outline-danger').addClass('disabled');
        updateRecap();
    };

    if (isWreck) {
        isFolderRepair = false;
        changeInterfaceToWreck();
    }

    $('#wreckReportSave').on('click', function (e) {
        e.preventDefault();
        let $wreckageForm = $('#wreckageReportForm form');
        let url = $wreckageForm.attr('action');
        let serializedForm = $wreckageForm.serialize();
        $('#modal-loading').fadeIn();
        $.post(url, serializedForm)
            .fail(function () {
                swal("ERREUR", "Une erreur est survenu lors de la mise à jour du rapport d\'épave", "error");
            })
            .always(function () {
                $('#modal-loading').fadeOut();
            });
    });

    $('#btn-submit-folder').on('click', function (e) {
        e.preventDefault();
        let link = $(this).attr('href');
        let wreckLink = $('#wreckSumbitLink').val();
        if(isFolderRepair) {
            swal({
                title: "Etes vous sûre ??",
                text: "Vous allez soumettre ce dossier en réparation !",
                icon: "warning",
                buttons: ["Annuler", "Valider"],
                dangerMode: false,
            })
                .then((willSubmit) => {
                    if (willSubmit) {
                        window.location.href = link;
                    }
                });
        } else {
            swal({
                title: "Etes vous sûre ??",
                text: "Vous allez soumettre ce dossier en tant qu'épave !",
                icon: "warning",
                buttons: ["Annuler", "Valider"],
                dangerMode: false,
            })
                .then((willSubmit) => {
                    if (willSubmit) {
                        window.location.href = wreckLink;
                    }
                });
        }
    });
});