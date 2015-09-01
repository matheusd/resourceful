<?php

namespace Resourceful;

trait GeneratesTemplatedHtml {    
    
    protected function addContentViews($templater, $responseData) {
        if (property_exists($this, "CONTENT_VIEWS")) {
            foreach ($this->CONTENT_VIEWS as $viewFile) {
                $templater->addContentView($viewFile, $responseData);
            }
        }
    }

    public function toTemplatedHtml($responseData) {
        $templater = $this->responseFactory->newTemplater();
        $this->addContentViews($templater, $responseData);
        return $templater->generateHtml();
    }
}