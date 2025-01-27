(function () {
    'use strict';

    //=require modules/app.js
    //=require modules/nospam.js
    //=require modules/form.js

    document.addEventListener('DOMContentLoaded', () => {
        app.init();
        nospam.init();
        form.init();
    });
})();
