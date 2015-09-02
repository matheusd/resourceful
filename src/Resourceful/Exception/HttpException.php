<?php

namespace Resourceful\Exception;

interface HttpException {
    public function getHttpStatusCode();
}