APP.provide('Gateway', {

    call: function(params, cb){
        $.ajax({
            url: Const.GATEWAY_URL,
            type: "post",
            dataType: "json",
            data: params,
            success: function(data){
                cb(data);
            }
        });
    }
    
});
