<?php

/**
 * Finds and caches all beans of a given type
 */
abstract class LibraryDataController {
    
    const CACHE_KEY_SUFFIX = '_KEY/';
    const DEFAULT_CACHE_LIFETIME = 1800;
    
    protected $type;
    protected $lifetime;

    private $data;

    public function __construct() {
        $vars = get_class_vars(get_class($this));
        if ( !isset($vars['BEAN_TYPE']) ) throw new Exception('Must define bean type');
        $this->type = $vars['BEAN_TYPE'];
        
        if (isset($vars['CACHE_LIFETIME']))
            $this->lifetime = $vars['CACHE_LIFETIME'];
        else
            $this->lifetime = self::DEFAULT_CACHE_LIFETIME;
    }
    
    public function getData() {
        
        // check in-memory cache
        if ($this->data)
            return $this->data;
        
        // check memcache
        $this->data = $this->cache()->getBeans($this->type .self::CACHE_KEY_SUFFIX);
        if ($this->data !== FALSE) {
            return $this->data;
        }
        
        $this->data = R::find($this->type);
        
        if (!$this->data)
            throw new Exception('could not find any classes');
        
        $this->cache()->set($this->type .self::CACHE_KEY_SUFFIX, $this->data, MEMCACHE_COMPRESSED, $this->lifetime);
        
        return $this->data;
    }
    
    public function getDatum($id) {
        $data = $this->getData();
        return isset($data[$id]) ? $data[$id] : null;
    }
    
    protected function flushCache() {
        $this->cache()->delete($this->type .self::CACHE_KEY_SUFFIX);
    }
    
    private function cache() {
        return new BeanMC(get_class($this));
    }
}
