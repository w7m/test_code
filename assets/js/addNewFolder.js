'use strict';

$(document).ready(function () {
    $("#vehicleRegistrationNumber").click(function (e) {
        e.preventDefault();
        let val = $('#vehicleRegistration').val();
        $.post('/receptionist/new-sinister', {
            val
        }).done(function (data) {
            console.log(data[1][0]);
            if (!($.isEmptyObject(data[1][0]))) {
                $("#type").html(data[1][0].type);
                $("#registrationNumber").html(data[1][0].registrationNumber);
                $("#grayCard").html(data[1][0].grayCard);
                $("#doorsNumber").html(data[1][0].doorsNumber);
                $("#horsePower").html(data[1][0].horsePower);
                $("#dateOfRegistration").html(data[1][0].dateOfRegistration);
                $("#color").html(data[1][0].color);
                $("#cin").html(data[1][0].ensured.cin);
                $("#city").html(data[1][0].ensured.city);
                $("#email").html(data[1][0].ensured.email);
                $("#firstName").html(data[1][0].ensured.firstName);
                $("#lastName").html(data[1][0].ensured.lastName);
                $("#id_v").val(data[1][0].id);
            } else {
                swal("", "Le matricule de véhicule n'existe pas", "error")

            }
        })

    });
    $("#expert").change(function (e) {
        e.preventDefault();
        let valExp = $(this).val();
        $.post('/receptionist/new-sinister', {
            valExp
        }).done(function (dataExp) {
            if (!($.isEmptyObject(dataExp[0][0]))) {
                $("#address").html(dataExp[0][0].address);
                $("#non").html(dataExp[0][0].firstName);
                $("#prenom").html(dataExp[0][0].lastName);
                $("#ville").html(dataExp[0][0].city);
                $("#telephone").html(dataExp[0][0].phoneNumber);
                $("#emailexp").html(dataExp[0][0].email);
                $("#id_exp").val(dataExp[0][0].id);
            } else {
                swal("L'expert n'existe pas", "error");
            }

        }).fail(
            swal("L'expert n'existe pas", "error")
        )
    });
    $("#addFolder").click(function (e) {
        e.preventDefault();
        let id_veh = $("#id_v").val();
        let id_exp = $("input[name='expertId']:checked").val();
        let constatform = $("#attached_file_imageFile_file").val();

        if (!id_veh || !id_exp || !constatform) {
            swal({

                text: "veuillez remplir tout les champs!",
                icon: "error",
            })
        } else {
            $.post('/receptionist/newfolder', {
                id_veh, id_exp, constatform
            }).done(function (response) {
                console.log(response);
                if (response === 'success') {
                    console.log(response);
                    swal({

                        text: "un nouveau dossier a été créé!",
                        icon: "success",
                    })
                    var url = '/receptionist/generatePdf/' + id_veh + '/' + id_exp;
                    window.open(url, '_blank');
                    window.location = '/receptionist/folder-sinister'
                } else {
                    swal({

                        text: "veuillez remplir tout les champs!",
                        icon: "error",
                    })
                }
            })
        }
    })
});