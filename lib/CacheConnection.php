<?php

class CacheConnection extends Singleton {
    
    private $debug = false;
    
    public $cache;
    
    protected function __construct() {
        
        if (Cfg::instance()->debug_memcache)
            $this->debug = true;
        
        $servers = array();
        
        $server_strings = explode(';', Cfg::instance()->memcache_servers);
        foreach ($server_strings as $server_string) {
            $parts = explode(':', $server_string);
            $servers[$parts[0]] = $parts[1];
        }
        
        $connected = false;
        $memcache = new Memcache();
        // if any of the servers connect successfully, we set $connected to true
        foreach ($servers as $host=>$port) {
            $result = $memcache->addServer($host, $port);
            $connected |= $result;
        }
        
        if (!$connected) {
            if ($this->debug) {
                error_log('unable to connect to any memcache server');
            }
            return;
        }
        
        if ($this->debug) {
            error_log('connected to at least one memcache server');
        }
        
        $this->cache = $memcache;
    }
    
    public function flush() {
        return $this->cache->flush();
    }
}