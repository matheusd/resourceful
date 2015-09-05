<?php

namespace Resourceful\Exception;

class ForbiddenActionException extends \Exception implements HttpException {
    public function getHttpStatusCode() {
        return 403;
    }
}