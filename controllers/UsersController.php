<?php 
/**
 * class that provides user operations
 */
class UsersController {

    const BEAN_TYPE = 'user';
    const CACHE_LIFETIME = 3600;

    private $user_id;
    private $user;    
    
    public function __construct($user_id = null) {
        if (!$user_id)
            $user_id = FB::instance()->user_id;
            
        $this->user_id = $user_id;
    }
    
    /**
     * find or create the current user
     */
    public function getCurrentUser() {
        
        // check in-memory cache
        if ($this->user)
            return $this->user;
        
        // check memcache
        $this->user = $this->cache()->getBeans($this->user_id);
        if ($this->user !== FALSE) {
            return $this->user;
        }
        
        $this->user = R::findOne(self::BEAN_TYPE, 'user_id = :uid', array('uid' => $this->user_id));
        
        // no user? create
        if (!$this->user) {
            $this->user = R::dispense(self::BEAN_TYPE);
            
            $this->user->user_id = $this->user_id;
            $this->user->is_active = true;
            $this->user->created_on = App::instance()->time;
            $this->user->last_on = App::instance()->time;
            $this->user->preferences = 0;
            $this->user->name = FB::instance()->me['name'];
            $this->user->email_last_updated = null;
            $this->user->score = 0;
            
            if (FB::instance()->me['email'])
                $this->user->email = FB::instance()->me['email'];
            
            R::store($this->user);
        }
        
        if (!$this->user)
            throw new Exception('could not find or create user with id = ' .$this->user_id);
        
        $this->cache()->set($this->user_id, $this->user, MEMCACHE_COMPRESSED, self::CACHE_LIFETIME);
        
        return $this->user;
    }

    private function cache() {
        return new BeanMC(get_class($this));
    }
}
