<?php

namespace Resourceful\Exception;

class NotFoundException extends \Exception implements HttpException {
    public function getHttpStatusCode() {
        return 404;
    }
}