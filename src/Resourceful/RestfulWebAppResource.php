<?php

namespace Resourceful;

class RestfulWebAppResource extends WebAppResource {

    protected $data;
    
    protected function toObject($array) {
        $obj = new \stdClass();
        foreach ($array as $key => $val) {
            $obj->$key = is_array($val) ? toObject($val) : $val;
        }
        return $obj;
    }
    
    protected function decodeFormData() {
        return $this->toObject($this->request->getParsedBody());            
    }

    protected function checkJsonDecodeErrors() {
        $res = json_last_error();
        if ($res != JSON_ERROR_NONE) {
            switch ($res) {
                case JSON_ERROR_DEPTH: $error = 'Maximum stack depth exceeded'; break;
                case JSON_ERROR_STATE_MISMATCH: $error = 'Underflow or the modes mismatch'; break;
                case JSON_ERROR_CTRL_CHAR: $error = 'Unexpected control character found'; break;
                case JSON_ERROR_SYNTAX: $error = 'Syntax error, malformed JSON'; break;
                case JSON_ERROR_UTF8: $error = 'Malformed UTF-8 characters, possibly incorrectly encoded'; break;
                default: "Unrecognized error: $res";
            }
            throw new Exception\WebAppJsonDecodeException($error);
        }
    }
    
    protected function quote($str) {
        if ($str === null) return null;
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8', false);
    }    
    
    protected function protectData($data) {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $data[$k] = $this->protectData($v);
                } else {
                    $data[$k] = $this->quote($v);
                }
            }
        } else if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $data->$k  = $this->protectData($v);
                } else {
                    $data->$k = $this->quote($v);
                }
            }
        }
        return $data;
    }

    protected function decodeRequestData() {
        $ctype = $this->request->getHeaderLine('Content-Type');        
        if ($ctype == "application/x-www-form-urlencoded" ||
            stripos($ctype, "multipart/form-data;") === 0)
        {
            $data = $this->decodeFormData();
        } else {
            $data = json_decode($this->request->getBody());
            $this->checkJsonDecodeErrors();
        }
            $this->data = $this->protectData($data);
    }        

    protected function beforeExec() {
        if (in_array($this->request->getMethod(), ['POST', 'PUT'])) {
            $this->decodeRequestData();
        }
        parent::beforeExec();
    }

    public function to_json($responseData) {
        $res = $this->responseFactory->newJsonResponse($responseData);
        if ($this->request->getMethod('OPTIONS')) {
            $methods = implode(',', array_map('strtoupper', $this->listAllowedMethods()));
            $res = $res->withHeader("Allow", $methods);
        }
        return $res;
    }
    
    public function to_html($responseData) {
        if (method_exists($this, "toTemplatedHtml")) {
            $body = $this->toTemplatedHtml($responseData);
        } else {
            $body = "<pre>".print_r($responseData, true)."</pre>";
        }                
        $res = $this->responseFactory->newHtmlResponse($body);
        if ($this->request->getMethod('OPTIONS')) {
            $methods = implode(',', array_map('strtoupper', $this->listAllowedMethods()));
            $res = $res->withHeader("Allow", $methods);
        }
        return $res;
    }

    public function options() {
        return [];
    }
}
