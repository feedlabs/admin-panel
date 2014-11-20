<?php

class AP_Component_FeedForm extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $params = $this->getParams();
        $application = $params->get('application');
        $feed = null;
        if ($params->has('feed')) {
            $feed = $this->getParams()->get('feed');
        }

        $viewResponse->set('application', $application);
        $viewResponse->set('feed', $feed);
    }
}
