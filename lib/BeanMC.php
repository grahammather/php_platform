<?php
/**
 * class that makes sure that when a bean is deserialized from memcache it won't cause a segfault because the model is technically set but
 * somehow corrupt.
 */
class BeanMC extends MC {
    
    public function __construct($namespace = '') {
        parent::__construct($namespace);
    }
    
    public function getBeans($key) {
        $result = parent::get($key);
        if (is_array($result)) {
            foreach ($result as $bean) {
                if ($bean && method_exists($bean, "setMeta"))
                    $bean->setMeta("model", NULL);
            }
        } else if ($result && method_exists($result, "setMeta")) {
            $result->setMeta("model", NULL);
        }
        return $result;
    }

}
