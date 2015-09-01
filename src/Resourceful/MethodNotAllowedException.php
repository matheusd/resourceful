<?php

namespace Resourceful;

class MethodNotAllowedException extends \Exception {
    public $allowedMethods = [];
}
