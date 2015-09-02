<?php

namespace Resourceful\Exception;

class MethodNotAllowedException extends \Exception {
    public $allowedMethods = [];
}
