<?php
class GatewayRouteHandler {

    private $signed_request;

    public function beforeRoute() {
        
        if (Cfg::instance()->gateway_signed_request_override == 1) {
            return true;
        }
        
        $this->signed_request = isset($_REQUEST['signed_request']) ? App::parse_signed_request($_REQUEST['signed_request']) : false;
        if (!$this->signed_request) {
            F3::error(404);
            return false;
        } else {
            return true;
        }
    }
}
