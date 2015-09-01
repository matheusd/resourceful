<?php

namespace Resourceful;

class ResponseFactory {

    public $templaterFactory;

    public function newHtmlResponse($body, $status = 200, array $headers = []) {
        return new \Zend\Diactoros\Response\HtmlResponse($body);  
    }
    
    public function newJsonResponse($responseData, $status = 200, array $headers = [], $encodingOptions = 15) {
        return new \Zend\Diactoros\Response\JsonResponse($responseData, $status, $headers, $encodingOptions);        
    }
    
    public function newTemplater() {
        return $this->templaterFactory->newTemplater();
    }
}
