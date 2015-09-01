<?php

namespace ExampleApp\Templater;


class SampleTemplaterFactory {

    public $globalContext;
    
    public function newTemplater() {
        $t = new SampleTemplater();
        $t->globalContext = $this->globalContext;
        $t->setup();
        return $t;
    }

}
