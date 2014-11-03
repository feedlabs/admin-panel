<?php

class AP_Component_ApplicationViewSmall extends AP_Component_Abstract {

    public function checkAccessible(CM_Frontend_Environment $environment) {
        if (!$environment->getViewer(true)->getRoles()->contains(AP_Role::ADMIN)) {
//            throw new CM_Exception_NotAllowed();
        }
    }

    public function prepare(CM_Frontend_Environment $environment, CM_Frontend_ViewResponse $viewResponse) {
        $application = $this->getParams()->getArray('application');
        $viewResponse->set('application', $application);
    }

    public function ajax_deleteApplication(CM_Params $params, CM_Frontend_JavascriptContainer_View $handler, CM_Response_View_Ajax $response) {
        $applicationId = (string) $params->get('applicationId');

        // delete application

        $handler->message('Success: Application delete.');
        $response->reloadComponent();
    }
}
