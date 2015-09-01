<?php

namespace ExampleApp\Home\Control;

class IndexResource extends \Resourceful\RestfulWebAppResource {

    use \Resourceful\GeneratesTemplatedHtml;    
    
    public function __construct() {
        $this->CONTENT_VIEWS = [__DIR__."/../view/hello.php"];
    }

    public function get() {        
        return ['msg' => 'Hello!'];
    }
}