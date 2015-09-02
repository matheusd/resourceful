<?php

namespace Resourceful\Exception;

class BadRequestException extends \Exception implements HttpException {
    public function getHttpStatusCode() {
        return 400;
    }
}