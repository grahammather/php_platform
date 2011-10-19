<?php
abstract class RouteHandler {
    
    abstract public function display();
    
    /**
     * Bootstrapping code - show something other than the route under certain conditions
     * @return 
     */
    public function beforeRoute() {
        
        F3::set('v', Cfg::instance()->v);
        F3::set('app_dir', Cfg::instance()->app_dir);
        F3::set('master_url', Cfg::instance()->master_url);
        
        // logged in to the game?
        if (FB::instance()->me) {
            $c = new UsersController();
            F3::set('user', $c->getCurrentUser());
        } else {
            echo F3::render('templates/notloggedin.php');
            return FALSE;
        }
        
        // whitelisting
        if (!App::isWhitelisted(FB::instance()->user_id)) {
            F3::error(404);
            return FALSE;
        }
        
        // Sanitizing Fat-Free's REQUEST global also sanitizes PHP's $_REQUEST
        F3::set('REQUEST', F3::scrub($_REQUEST));

    }
}
