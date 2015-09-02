<?php

namespace Resourceful;

trait FindMostAcceptableResponseFunction {

    use DecodesAcceptArray;
    
    protected function decodeRequestPathAndAccept($request) {
        $path = $request->getUri()->getPath();
        $acceptString = $request->getHeader("Accept")[0];
        $forceType = "";
        if (preg_match("|^.+\.(.+)$|", $path, $matches)) {
            //forcing a specific type 
            $forceType = $matches[1];
        }
        return [$forceType, $acceptString];
    }

    protected function acceptableMimes() {
        return [
            'text/plain' => 'plain',
            'text/html' => 'html',
            'application/json' => 'json',            
        ];
    }
    
    public function mostAcceptableResponseFunction($forceType, $acceptString) {        
        if ($forceType) {
            //forcing a specific type 
            $ext = $forceType;
            $methodName = "to_$forceType";
        } else {
            $accepts = $this->decodeAcceptArray($acceptString);    
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
        
        return $methodName;        
    }


}