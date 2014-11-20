<?php

class AP_Page_Application extends CM_Page_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $application = $this->getParams()->getString('application');
        $viewResponse->set('application', $application);
    }
}
