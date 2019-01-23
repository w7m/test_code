import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min";

Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';


const routes = require('../../public/js/fos_js_routes.json');
Routing.setRoutingData(routes);

$.ajax({
    cache: false,
    url: Routing.generate('count-expert-Folders-month'),
    type: "get",
}).done(function (response, textStatus, jqXHR) {
    var created = response.created;
    var inProgress = response.inProgress;
    var toBeReconsidered = response.toBeReconsidered;
    var reassigned = response.reassigned;
    var sellingStandby = response.sellingStandby;
    var closed = response.closed;


    var maxFolder = 0;
    var result = Object.keys(response).map(function (key) {
        if (response[key] > maxFolder) {
            maxFolder = response[key];
        }
    });


    var ctx = document.getElementById("myBarChart");
    var myLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Nouveaux", "En cours", "En retour", "En contre expertise", "Epaves", "Archive"],
            datasets: [{
                label: "Nombre",
                backgroundColor: [
                    '#17a2b8',
                    '#007bff',
                    '#dc3545',
                    '#ffc107',
                    '#28a745',
                    '#17a2b8'
                ],
                borderColor: "rgba(2,117,216,1)",
                data: [created, inProgress, toBeReconsidered, reassigned, sellingStandby, closed],
            }],
        },
        options: {
            scales: {
                xAxes: [{
                    time: {
                        unit: 'month'
                    },
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        maxTicksLimit: 10
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        max: parseInt(maxFolder),
                        maxTicksLimit: parseInt(maxFolder)+1
                    },
                    gridLines: {
                        display: true
                    }
                }],
            },
            legend: {
                display: false
            }
        }
    });
    console.log(response)
}).fail(function (jqXHR, textStatus, errorThrown) {
    alert('Erreur serveur');
});














