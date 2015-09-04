<?php

namespace Resourceful\Exception;

class ForbiddenException extends \Exception implements HttpException {
    public function getHttpStatusCode() {
        return 403;
    }
}