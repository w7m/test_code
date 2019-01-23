$(document).ready(function () {
    $('.commentsBtn').on("click", function () {
        var idFolder = $("#folderId").val();
        let btnChoice = $(this).text();
        if (btnChoice === 'Refuser') {
            var comments= $('#commentsRefus').val();
        } else if(btnChoice === 'Contre expertise') {
            var comments= $('#commentsReassign').val();
        } else {
            var comments= '';
        }
        if (comments === ""){
            swal("Oops...", "Saisissez des commentaires pour expliquer votre décision", "error");
        } else {
            if (btnChoice === 'Refuser') {
                $.post(
                    "/validator/reconsider-folder/",
                    {comments,idFolder}
                )
                    .done(function (data) {
                        if (data === 'success') {
                            swal("c'est fait...", $("#refFolder").text() +"a été refusé", "success").then(() => {
                                window.location.replace("/validator/list-folder");
                            });
                        } else if (data === 'transitionError') {
                            swal("Oops...", "un erreur est survenu, le dossier ne peut pas etre retourné. Veuillez notifier l'admin", "error").then(() => {
                                window.location.replace("/validator/details-folder/"+idFolder);
                            });
                        } else {
                            alert(data)
                        }
                    })
                    .fail(function (data) {
                        alert(data)
                    })
            } else if(btnChoice === 'Contre expertise') {
                $.post(
                    "/validator/reassign-folder",
                    {comments,idFolder}
                )
                    .done(function (data) {
                        if (data === 'success') {
                            window.location.replace("/validator/list-experts-to-reassign/"+idFolder);
                        } else if (data === 'transitionError') {
                            swal("Oops...", "'un erreur est survenu, le dossier ne peut pas etre réaffecté. Veuillez notifier l'admin'", "error").then(() => {
                                window.location.replace("/validator/details-folder/"+idFolder);
                            });
                        } else {
                            alert(data)
                        }
                    })
                    .fail(function (data) {
                        alert(data)
                    })
            }
        }
    })
});