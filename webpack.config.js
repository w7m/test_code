var Encore = require('@symfony/webpack-encore');

Encore
// directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addStyleEntry('dropZone', './assets/css/dropzone.min.css')
    .addStyleEntry('floatRealTime', './assets/css/floatRealTime.css')
    .addEntry('dataTabes', './assets/js/dataTabes.js')
    .addEntry('expertNewFolder', './assets/js/expertNewFolder.js')
    .addStyleEntry('expert', './assets/css/expert.css')
    .addStyleEntry('lc_switch_style', './assets/css/lc_switch.css')
    .addEntry('editFolder', './assets/js/editFolder.js')
    .addEntry('listExpert', './assets/js/listExpert.js')
    .addEntry('countFolderExpert', './assets/js/countFolderExpert.js')
    .addEntry('pluginCharts', './assets/js/pluginCharts.js')
    .addEntry('chartFolderExpert', './assets/js/chartFolderExpert.js')
    .addEntry('validateFolder', './assets/js/validateFolder.js')
    .addEntry('expertChoice', './assets/js/expertChoice.js')
    .addEntry('historyFolderExpert', './assets/js/historyFolderExpert.js')
    .addEntry('jqueryFloatRealplugin', './assets/js/jqueryFloatRealplugin.js')
    .addEntry('jqueryFloat', './assets/js/jqueryFloat.js')
    .addEntry('floatRealFunction', './assets/js/floatRealFunction.js')
    .addEntry('displayFolder', './assets/js/displayFolder.js')
    .addEntry('lcSwitch', './assets/js/lc_switch.js')
    .addEntry('listFolder', './assets/js/listFolder.js')
    .addEntry('flashbag', './assets/js/flashbag.js')
    .addEntry('admin', './assets/js/admin.js')
    .addEntry('commentsDecision', './assets/js/commentsDecision.js')
    .addEntry('addnewfolder', './assets/js/addNewFolder.js')
    .addEntry('newContract', './assets/js/addContract.js')
    .addEntry('realTimeControlParameterInput', './assets/js/realTimeControlParameterInput.js')
    .addEntry('listContract', './assets/js/listContract.js')
    .addEntry('expertChoiceList', './assets/js/expertChoiceList.js')


    .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        to: 'images/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    })


    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
