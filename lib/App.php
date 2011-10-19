<?php 
class App extends Singleton {

    // the master "request time"
    public $time;
    
    private $app_access_token;
    
    protected function __construct() {
        $this->time = time();
    }
    
    public function getAppAccessToken() {
        if (!$this->app_access_token) {
        
            if ($this->app_access_token = Cache::instance()->get(CacheConst::CACHE_KEY_APP_ACCESS_TOKEN)) {
                return $this->app_access_token;
            }
            
            $app_id = Cfg::instance()->fb_app_id;
            $app_secret = Cfg::instance()->fb_app_secret;
            $token_url = "https://graph.facebook.com/oauth/access_token?"."client_id=".$app_id."&client_secret=".$app_secret."&grant_type=client_credentials";
            $this->app_access_token = file_get_contents($token_url);
            Cache::instance()->set(CacheConst::CACHE_KEY_APP_ACCESS_TOKEN, $this->app_access_token, MEMCACHE_COMPRESSED, CacheConst::CACHE_TIMEOUT_APP_ACCESS_TOKEN);
        }
        return $this->app_access_token;
    }
    
    function create_signed_data($data) {
        return self::base64UrlEncode(hash_hmac('sha256', $data, Cfg::instance()->fb_app_secret, $raw = true));
    }
    
    protected static function base64UrlDecode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    
    protected static function base64UrlEncode($data) {
        return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
    }
    
    public static function parse_signed_request($signed_request) {
        $secret = Cfg::instance()->fb_app_secret;
        
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);
        
        // decode the data
        $sig = self::base64UrlDecode($encoded_sig);
        $data = json_decode(self::base64UrlDecode($payload), true);
        
        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            return null;
        }
        
        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            return null;
        }
        
        return $data;
    }
    
    public static function isWhitelisted($user_id) {
        $whitelisted_ids = explode(',', Cfg::instance()->uid_whitelist);
        return in_array($user_id, $whitelisted_ids);
    }
}
