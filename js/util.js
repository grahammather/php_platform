APP.provide('Util', {
    capitalize : function(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    },
    log : function(args) {
        if(window.console) {
            window.console.log(args);
        }
    },
    serverTime : function() {

        APP.Util.log('calling servertime with url ' + Const.MASTER_URL + 'time');

        var time = null;
        $.ajax({
            url : Const.MASTER_URL + 'time',
            async : false,
            dataType : 'text',
            success : function(text) {
                time = new Date(text);
            },
            error : function(http, message, exc) {
                time = new Date();
            }
        });
        return time;
    },
    currentTime : function() {
        return Math.round(new Date().getTime() / 1000.0);
    }
});
