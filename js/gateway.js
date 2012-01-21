APP.provide('Gateway', {

    call : function(params, cb) {

        if(!params['action'])
            return false;

        var action = params['action'];
        delete params['action'];

        // add access token
        if( typeof params['signed_request'] === 'undefined') {
            params['signed_request'] = Vars.signed_request;
        }

        $.ajax({
            url : Const.GATEWAY_URL + "/" + action,
            type : "post",
            dataType : "json",
            data : params,
            success : function(data) {
                if(cb)
                    cb(data);
            },
            error : function(data) {
                if(cb)
                    cb(false);
            }
        });
    },
    isSuccess : function(response) {
        if(!response || response['error'])
            return false;
        else
            return true;
    }
});
