<?php
require 'facebook-php-sdk-3.1.1/src/facebook.php';

class FB extends Singleton {

    public $facebook;
    public $user_id;
    public $me;
    
    protected function __construct() {
    
        $cfg = Cfg::instance();
        
        // override https
        // Facebook::$DOMAIN_MAP['www'] = 'http://www.facebook.com/';
        
        // this is the new facebook api
        // Create our Application instance.
        $this->facebook = new Facebook(array('appId'=>$cfg->fb_app_id, 'secret'=>$cfg->fb_app_secret, 'cookie'=>true, ));
        
        // We may or may not have this data based on a $_GET or $_COOKIE based session.
        //
        // If we get a session here, it means we found a correctly signed session using
        // the Application Secret only Facebook and the Application know. We dont know
        // if it is still valid until we make an API call using the session. A session
        // can become invalid if it has already expired (should not be getting the
        // session back in this case) or if the user logged out of Facebook.
        $this->user_id = $this->facebook->getUser();
        // Session based API call.
        if ($this->user_id) {
            try {
                // TODO: add more params to fetch for the current user?
                $this->me = $this->facebook->api('/me', array('fields' => array('birthday','email','first_name','name','picture','third_party_id','locale','gender')));
            }
            catch(FacebookApiException $e) {
                error_log($e);
            }
        } else {
            error_log('no FB user id');
        }
    }
}
