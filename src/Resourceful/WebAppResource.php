<?php

namespace Resourceful;

class WebAppResource {

    use FindMostAcceptableResponseFunction;

    public $request;
    
    public $parameters;
        
    protected function beforeExec() {        
        $methods = get_class_methods($this);
        $fbefMethods = array_filter($methods, function ($i) {
            return stripos($i, "fbef_") === 0;
        });
        foreach ($fbefMethods as $method) {
            $resp = $this->$method();
            if ($resp !== null) {
                return $resp;
            }
        }
    }
    
    protected function afterExec($response) {
        $methods = get_class_methods($this);
        $faefMethods = array_filter($methods, function ($i) {
            return stripos($i, "faft_") === 0;
        });
        foreach ($faefMethods as $method) {
            $response = $this->$method($response);
        }
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
        return $res;
    }    
        
    public function responseDataToResponse($responseData) {        
        list($forceType, $acceptString) = $this->decodeRequestPathAndAccept($this->request);
        $methodName = $this->mostAcceptableResponseFunction($forceType, $acceptString);        
        
        if (method_exists($this, $methodName)) {
            return $this->$methodName($responseData);
        } else {
            throw new Exception\UnsupportedRepresentationException("Cannot build representation '$ext' of resource " . get_class($this));
        }
    }
    
    public function exec() {
        $responseData = $this->beforeExec();        
        if ($responseData !== null) {
            $this->responseData = $responseData;                
            if (!($responseData instanceof \Psr\Http\Message\ResponseInterface)) {                        
                $response = $this->responseDataToResponse($responseData);                
            }
            $response = $this->afterExec($response);        
            return $response;
        }
        
        $method = $this->request->getMethod();
        if (!method_exists($this, $method)) {                                                
            $ex = new Exception\MethodNotAllowedException("Method '$method' is not allowed on resource " . get_class($this));
            $ex->allowedMethods = $this->listAllowedMethods();
            throw $ex;
        }        
        $responseData = $this->$method();
        $this->responseData = $responseData;                
        if (!($responseData instanceof \Psr\Http\Message\ResponseInterface)) {            
            $response = $this->responseDataToResponse($responseData);
        } else {
            $response = $responseData;
        }
        
        $response = $this->afterExec($response);        
        
        return $response;
    }       
}
