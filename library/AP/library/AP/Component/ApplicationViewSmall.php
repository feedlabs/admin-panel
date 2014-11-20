<?php

class AP_Component_ApplicationViewSmall extends AP_Component_Abstract {

    public function checkAccessible(CM_Frontend_Environment $environment) {
        if (!$environment->getViewer(true)->getRoles()->contains(AP_Role::ADMIN)) {
            //            throw new CM_Exception_NotAllowed();
        }
    }

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $application = $this->getParams()->get('application');
        $viewResponse->set('application', $application);
    }
}
