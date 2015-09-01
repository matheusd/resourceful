<?php

namespace ExampleApp\Templater;

class SampleTemplater extends \Resourceful\WebAppTemplater {

    public function setup() {
        $this->addFooterView(__DIR__."/templates/footer.php", []);
        $this->addHeaderView(__DIR__."/templates/header.php", []);
    }    

}