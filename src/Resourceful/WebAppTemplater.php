<?php

namespace Resourceful;

function do_view_include($filename, $context) {
    //******************************
    //*  View globals
    //******************************
    //Any variable below this point (and anything global) will be exported to the view file.
    //For security reasons, be aware of what you are exporting!

    //fill global variables with context. This will set what the view template
    //will be able to use
    foreach ($context as $k => $v) {
        //export context as a "global-like" variable to the view template.
        $$k = $v;
    }
        
    include($filename);
}


class WebAppTemplater {

    protected $headerViews = [];
    protected $contentViews = [];
    protected $footerViews = [];
    protected $headerContent = [];
    protected $contentContent = [];
    protected $footerContent = [];
    
    public $globalContext;
    
    public function addHeaderView($viewFile, $context) {
        $this->headerViews[] = [$viewFile, $context];
    }
    
    public function addContentView($viewFile, $context) {
        $this->contentViews[] = [$viewFile, $context];
    }    
    
    public function addFooterView($viewFile, $context) {
        $this->footerViews[] = [$viewFile, $context];
    }
    
    public function addHeaderContent($content) {
        $this->headerContent[] = $content;
    }    
    
    public function addContentContent($content) {
        $this->contentContent[] = $content;
    }
    
    public function addFooterContent($content) {
        $this->footerContent[] = $content;
    }
    
    protected function includeView($viewFile, $context) {
        
        $context = array_merge($this->globalContext, $context);

        //set templater object for use in the view
        $context['templater'] = $this;

        do_view_include($viewFile, $context);        
    }
    
    public function generateHtml() {
        ob_start();        
        
        foreach ($this->headerViews as $v) {
            list($viewFile, $context) = $v;
            $this->includeView($viewFile, $context);
        }
        foreach ($this->headerContent as $ct) {
            echo $ct;
        }        
        
        foreach ($this->contentViews as $v) {
            list($viewFile, $context) = $v;
            $this->includeView($viewFile, $context);
        }
        foreach ($this->contentContent as $ct) {
            echo $ct;
        }
    
        foreach ($this->footerViews as $v) {
            list($viewFile, $context) = $v;
            $this->includeView($viewFile, $context);
        }
        foreach ($this->footerContent as $ct) {
            echo $ct;
        }
    
        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}
