<?php

namespace ExampleApp\Home\control;

class ExceptionResource extends \Resourceful\WebAppResource {

    public function get() {
        throw new \Resourceful\Exception\BadRequestException("Throwing requested exception", 829);
    }    
    
    public function put() {
        throw new \Resourceful\Exception\BadRequestException("Throwing requested exception", 999);
    }

}