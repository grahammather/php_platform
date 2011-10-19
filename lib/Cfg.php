<?php

class Cfg extends Singleton {

    private static $SECTION_NAME = 'All';
    private $entries = array();
    
    protected function __construct() {
        $configFile = BASEDIR.'/temp/config.ini';
        $this->entries = $this->parse_ini($configFile);
    }
    
    function __get($id) {
        // version is special
        if ($id == 'v') {
            if ($this->is_test)
                return time();
        }
        return $this->entries[self::$SECTION_NAME][$id];
    }
    
    private function parse_ini($filepath) {
    
        $ini = file($filepath);
        if (count($ini) == 0) {
            return array();
        }
        $sections = array();
        $values = array();
        $globals = array();
        $i = 0;
        foreach ($ini as $line) {
        
            $line = trim($line);
            // Comments
            if ($line == '' || $line {0} == ';') {
                continue;
            }
            // Sections
            if ($line {0} == '[') {
                $sections[] = substr($line, 1, -1);
                $i++;
                continue;
            }
            
            // Key-value pair
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($i == 0) {
                // Array values
                if (substr($line, -1, 2) == '[]') {
                    $globals[$key][] = $value;
                } else {
                    $globals[$key] = $value;
                }
            } else {
                // Array values
                if (substr($line, -1, 2) == '[]') {
                    $values[$i - 1][$key][] = $value;
                } else {
                    $values[$i - 1][$key] = $value;
                }
            }
        }
        for ($j = 0; $j < $i; $j++) {
            $result[$sections[$j]] = $values[$j];
        }
        return $result + $globals;
    }
}
