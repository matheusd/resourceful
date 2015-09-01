<?php

namespace Resourceful;

class SessionStorage implements \arrayaccess, \Iterator {

    protected $container;    

    public $sessionName;
    
    public function __construct($sessionName) {
        $this->sessionName = $sessionName;
    }
    
    public function startSession() {
        session_name($this->sessionName);
        session_start();            
        $this->container =& $_SESSION;
    }

    /**
     * Returns the id value of this session's cookie.
     */
    public function sessionId() {
        return session_id();
    }
    
    
    public function offsetSet($offset, $value) {        
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }


    public function current () {
        return current($this->container);
    }

    public function key() {
        return key($this->container);
    }

    public function next () {
        return next($this->container);
    }

    public function rewind () {
        return reset($this->container);
    }

    public function valid () {
        return $this->offsetExists(key($this->container));
    }

    public function exists($key) {
        return array_key_exists($key, $this->container);
    }

}
