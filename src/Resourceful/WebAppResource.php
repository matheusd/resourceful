<?php

namespace Resourceful;

class WebAppResource {

    public $request;
    
    public $parameters;
    
    protected function beforeExec() {
        return null;
    }
    
    protected function afterExec($response) {
        return $response;
    }
    
    public function listAllowedMethods() {
        $defMethods = ['get', 'post', 'put', 'delete', 'options', 'head'];
        $res = [];
        foreach ($defMethods as $methodName) {
            if (method_exists($this, $methodName)) {
                $res[] = $methodName;
            }
        }
        return $methodName;
    }
    
    private function getAcceptArray()
    {
        $acceptString = $this->request->getHeader('Accept')[0];        
        $accept = $acceptArray = array();
        foreach (explode(',', strtolower($acceptString)) as $part) {
            $parts = preg_split('/\s*;\s*q=/', $part);
            if (isset($parts) && isset($parts[1]) && $parts[1]) {
                $num = $parts[1] * 10;
            } else {
                $num = 10;
            }
            if ($parts[0]) {
                $accept[$num][] = $parts[0];
            }
        }
        krsort($accept);
        foreach ($accept as $parts) {
            foreach ($parts as $part) {
                $acceptArray[] = trim($part);
            }
        }        

        return $acceptArray;
    }    
    
    protected function acceptableMimes() {
        return [
            'text/plain' => 'plain',
            'text/html' => 'html',
            'application/json' => 'json',            
        ];
    }
    
    public function responseDataToResponse($responseData) {
        $path = $this->request->getUri()->getPath();
        if (preg_match("|^.+\.(.+)$|", $path, $matches)) {
            //forcing a specific type 
            $ext = $matches[1];
            $methodName = "to_$ext";
        } else {
            $accepts = $this->getAcceptArray();    
            if ($accepts) {
                $mimes = $this->acceptableMimes();                
                $ext = 'html';
                $methodName = 'to_html';                
                foreach ($accepts as $mime) {
                    if (!isset($mimes[$mime])) continue;
                    $ext = $mimes[$mime];
                    $methodName = "to_$ext";
                    if (method_exists($this, $methodName)) break;
                }
            } else {
                $ext = 'html';
                $methodName = "to_html";
            }
        }        
        if (method_exists($this, $methodName)) {
            return $this->$methodName($responseData);
        } else {
            throw new UnsupportedRepresentationException("Cannot build representation '$ext' of resource " . get_class($this));
        }
    }
    
    public function exec() {
        $response = $this->beforeExec();
        if ($response) {
            return $response;
        }
        
        $method = $this->request->getMethod();
        if (!method_exists($this, $method)) {                                                
            $ex = new MethodNotAllowedException("Method '$method' is not allowed on resource " . get_class($this));
            $ex->allowedMethods = $this->listAllowedMethods();
            throw $ex;
        }
        $responseData = $this->$method();
        $this->responseData = $responseData;
        if (!$responseData instanceof Psr\Http\Message\ResponseInterface) {
            $response = $this->responseDataToResponse($responseData);
        }
        
        $response = $this->afterExec($response);
        
        return $response;
    }       
}
