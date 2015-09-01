<?php

namespace ExampleApp\Home\Control;

class FormResource extends \Resourceful\RestfulWebAppResource {

    use \Resourceful\GeneratesTemplatedHtml;    
   

    public function get() {        
        $this->CONTENT_VIEWS = [__DIR__."/../view/form.php"];
        return ['msg' => 'Hello!'];
    }
    
    public function post() {        
        $this->CONTENT_VIEWS = [__DIR__."/../view/formSuccess.php"];
        error_log('okokokok');
        error_log(print_r($this->data, true));
        return ['ha' => 'hehe'];
    }
}