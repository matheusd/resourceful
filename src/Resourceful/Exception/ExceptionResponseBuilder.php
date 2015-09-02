<?php

namespace Resourceful\Exception;

class ExceptionResponseBuilder extends \Resourceful\RestfulWebAppResource {

    use \Resourceful\DecodesAcceptArray;

    public $responseFactory;
    
    public $includeStackTrace = true;
   
    
    public function buildResponse($exception, $request) {
        $acceptHeader = "";
        $forceType = "";
        
        if ($request) {
            list($forceType, $acceptHeader) = $this->decodeRequestPathAndAccept($request);
        }        
        
        $methodName = $this->mostAcceptableResponseFunction($forceType, $acceptHeader);
        
        $responseData = $this->exceptionToData($exception);
        
        
        $response = $this->$methodName($responseData);
        $response = $this->setResponseCode($response, $exception);
        return $response;
    }
    
    protected function setResponseCode($resp, $exception) {
        if ($exception instanceof HttpException) {            
            $resp = $resp->withStatus($exception->getHttpStatusCode());
        } else {
            $resp = $resp->withStatus(500);
        }
        //FIXME: not pretty... the diactoros uses this side-effect call to set
        //the default http status line
        $resp->getReasonPhrase();
        return $resp;
    }
    
    protected function exceptionToData($exception) {
        $res = [
            'status' => 'error',
            'errorMsg' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'class' => get_class($exception),
            'trace' => $this->includeStackTrace ? $exception->getTraceAsString() : null,
            'previous' => null,
        ];
        if ($exception->getPrevious()) {
            $res['previous'] = $this->exceptionData($exception);
        }
        return $res;
    }
    
    public function to_json($responseData) {        
        return $this->responseFactory->newJsonResponse($responseData);                
    }
    
    public function to_html($responseData) {
        if (method_exists($this, "toTemplatedHtml")) {
            $body = $this->toTemplatedHtml($responseData);
        } else {
            $body = "<pre>".print_r($responseData, true)."</pre>";
        }                
        return $this->responseFactory->newHtmlResponse($body);        
    }
}