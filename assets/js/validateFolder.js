
$(document).ready(function () {
    $(".tick_btn").on("click",function () {
        var folderID = $(this).parent().parent().children()[4].value;
        $(this).css('color', 'green');
        $(this).hide();
        $(this).parent().append("<button class='btn btn-success disabled'>Validé</button>");
                $.post(
                    "/financial/validateFolder",
                    {folderID}
                )
                    .done(function (reponse) {
                        if (reponse === 'success'){


                            swal("", "Le remboursement du dossier a été pris en considération", "success")
                        } else {
                            swal("", "un erreur de validation est survenue", "error")
                        }
                    })
                    .fail(function () {

                    })
    })
})
