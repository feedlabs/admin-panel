<?php

class AP_Component_ApplicationForm extends AP_Component_Abstract {

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $params = $this->getParams();
        $application = null;
        if ($params->has('application')) {
            $application = $this->getParams()->get('application');
        }

        $viewResponse->set('application', $application);
    }
}
