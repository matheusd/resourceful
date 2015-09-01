<?php

namespace ExampleApp\Templater;

class AssetsResource extends \Resourceful\WebAppResource {

    use \Resourceful\GeneratesTemplatedHtml;    
    
    public function __construct() {
        $this->CONTENT_VIEWS = [__DIR__."/../view/hello.php"];
    }

    public function get() {        
        return ['msg' => 'Hello!'];
    }
}