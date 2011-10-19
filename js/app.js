if(!window.APP) {
    APP = {

        TEST_OUTPUT_DIV_ID : '#output',

        ME : {},

        // ---------------------------------

        testprint : function(text) {
            $(APP.TEST_OUTPUT_DIV_ID).html(text);
        },
        // ---------------------------------

        init : function() {
            APP.Util.log('INIT!');

            // bootstrap the app

            // start the energy countdown
            if(Vars.energyDeficit > 0) {
                APP.startEnergyTimer();
            }
        },
        facebookReady : function() {
            FB.init({
                appId : Const.APP_ID,
                status : true,
                cookie : true,
                xfbml : true
            });

            FB.Canvas.setAutoResize(true);

            APP.init();
        },
        onDocumentReady : function() {

            if(window.FB) {
                APP.facebookReady();
            } else {
                window.fbAsyncInit = APP.facebookReady;
            }
        },
        // ---------------------------------------

        handleDataError : function(data) {
            if(data["error"]) {
                APP.log('error: ' + data["error"]);
            } else {
                APP.log(data);
            }
        },
        handleGenericCall : function(data) {
            if(data["success"]) {
                APP.log(JSON.stringify(data["success"]));
            } else {
                APP.handleDataError(data);
            }
        },
        // ---------------------------------------

        copy : function(target, source, overwrite, transform) {
            for(var key in source) {
                if(overwrite || typeof target[key] === 'undefined') {
                    target[key] = transform ? transform(source[key]) : source[key];
                }
            }
            return target;
        },
        create : function(name, value) {
            var node = window.APP, nameParts = name ? name.split('.') : [], c = nameParts.length;
            for(var i = 0; i < c; i++) {
                var part = nameParts[i];
                var nso = node[part];
                if(!nso) {
                    nso = (value && i + 1 == c) ? value : {};
                    node[part] = nso;
                }
                node = nso;
            }
            return node;
        },
        provide : function(target, source, overwrite) {
            // a string means a dot separated object that gets appended to, or created
            return APP.copy( typeof target == 'string' ? APP.create(target) : target, source, overwrite);
        },
    }

}