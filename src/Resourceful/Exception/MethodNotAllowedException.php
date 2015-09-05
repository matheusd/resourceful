<?php

namespace Resourceful\Exception;

class MethodNotAllowedException extends \Exception implements HttpException {
    public $allowedMethods = [];

    public function getHttpStatusCode() {
        return 405;
    }
}
