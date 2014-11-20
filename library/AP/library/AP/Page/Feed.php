<?php

class AP_Page_Feed extends CM_Page_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $application = $this->getParams()->getString('application');
        $feed = $this->getParams()->getString('feed');

        $viewResponse->set('application', $application);
        $viewResponse->set('feed', $feed);
    }
}
