<?php 
class PurgeCacheRouteHandler extends RouteHandler {
    
     public function beforeRoute() {
        return;
    }
    
    public function display() {
        echo CacheConnection::instance()->flush();
        echo "<br/>ok";
    }
}
?>
