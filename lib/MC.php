<?php 
// ini_set('memcache.hash_strategy', 'consistent');
class MC {

    const NOVALUE = '\0__empty__\0';
    
    public $noread;
    private $_prefix;
    private $_cache;
    
    public function __construct($namespace = '') {
        
        $this->noread = Cfg::instance()->memcache_noread;
        if (!$this->noread) {
            $this->_cache = CacheConnection::instance()->cache;
            $this->_prefix = Cfg::instance()->app_prefix."/".$namespace ."/";
        }
    }
    
    public function get($key) {
        if ($this->noread) {
            return FALSE;
        }
        $key = $this->_prefix.$key;
        $value = $this->_cache->get($key);
        if ($value === self::NOVALUE)
            $value = null;
            
        return $value;
    }
    
    public function set($key, $var, $flag = null, $expire = null) {
        if ($this->noread) {
            return FALSE;
        }
        $key = $this->_prefix.$key;
        if ($var === null)
            $var = self::NOVALUE;
            
        return $this->_cache->set($key, $var, $flag, $expire);
    }
    
    public function delete($key) {
        if ($this->noread) {
            return FALSE;
        }
        $key = $this->_prefix.$key;
        return $this->_cache->delete($key);
    }
    
    public function add($key, $val, $flag, $expire) {
        if ($this->noread) {
            return TRUE;
        }
        $key = $this->_prefix.$key;
        return $this->_cache->add($key, $val, $flag, $expire);
    }
}
