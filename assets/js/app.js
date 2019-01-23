/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/sb-admin.css');
require('../css/global.scss');
require('@fortawesome/fontawesome-free/css/all.css');
require('../js/sb-admin');
require('sweetalert');
require('../js/jquery-migrate');
require('../js/lc_switch_onload');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything

const $ = require('jquery');

require('bootstrap');
require('dropzone');
require('jqueryui');



// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

